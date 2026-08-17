<?php

namespace Tests\Feature;

use App\Models\DiscountVoucher;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Service\SapoService;
use App\Support\StorefrontVoucher;
use App\Support\SapoVoucherSynchronizer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StorefrontVoucherTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('app.url', 'http://localhost');
        config()->set('storefront.shipping_fee', 30000);
    }

    public function test_percentage_uses_integer_vnd_rounding_and_maximum_cap(): void
    {
        $voucher = $this->voucher([
            'DISCOUNT_VALUE' => 1000,
            'MAX_DISCOUNT_AMOUNT' => 30000,
        ]);
        $service = app(StorefrontVoucher::class);

        $belowCap = $service->quote($voucher->CODE, 200000, 30000, null, 'a@example.com', '0909000001');
        $atCap = $service->quote($voucher->CODE, 300000, 30000, null, 'b@example.com', '0909000002');
        $aboveCap = $service->quote($voucher->CODE, 500000, 30000, null, 'c@example.com', '0909000003');
        $fractional = $service->quote($voucher->CODE, 99999, 30000, null, 'd@example.com', '0909000004');

        $this->assertSame(20000, $belowCap['discount_amount']);
        $this->assertSame(30000, $atCap['discount_amount']);
        $this->assertSame(30000, $aboveCap['discount_amount']);
        $this->assertSame(9999, $fractional['discount_amount']);
        $this->assertSame(120000, $fractional['total']);
    }

    public function test_fixed_discount_never_exceeds_subtotal(): void
    {
        $voucher = $this->voucher([
            'DISCOUNT_TYPE' => StorefrontVoucher::TYPE_FIXED_AMOUNT,
            'DISCOUNT_VALUE' => 150000,
            'MAX_DISCOUNT_AMOUNT' => null,
        ]);

        $quote = app(StorefrontVoucher::class)->quote(
            $voucher->CODE,
            100000,
            30000,
            null,
            'fixed@example.com',
            '0909000010'
        );

        $this->assertSame(100000, $quote['discount_amount']);
        $this->assertSame(30000, $quote['total']);
    }

    public function test_minimum_usage_limit_and_once_per_customer_are_enforced(): void
    {
        $voucher = $this->voucher([
            'MIN_SUBTOTAL' => 200000,
            'USAGE_LIMIT' => 2,
            'ONCE_PER_CUSTOMER' => true,
        ]);
        $service = app(StorefrontVoucher::class);

        try {
            $service->quote($voucher->CODE, 199999, 30000, null, 'low@example.com', '0909000011');
            $this->fail('Expected minimum subtotal validation.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('200.000', $e->errors()['DISCOUNT_CODE'][0]);
        }

        Transaction::query()->create([
            'USER_BUY_EMAIL' => 'used@example.com',
            'DISCOUNT_VOUCHER_ID' => $voucher->ID,
            'DISCOUNT_CODE' => $voucher->CODE,
            'DISCOUNT_AMOUNT' => 20000,
            'TRANSACTION_STATUS' => 'PENDING',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        $this->expectException(ValidationException::class);
        $service->quote($voucher->CODE, 250000, 30000, null, 'USED@example.com', '0909000012');
    }

    public function test_quote_and_checkout_recalculate_catalog_price_on_server(): void
    {
        [, $variant] = $this->catalogItem(500000, 2);
        $voucher = $this->voucher([
            'DISCOUNT_VALUE' => 1000,
            'MAX_DISCOUNT_AMOUNT' => 30000,
        ]);

        $items = [[
            'PRODUCT_ID' => $variant->ID,
            'QUANTITY' => 1,
            'PRICE' => 1,
        ]];

        $this->postJson('http://localhost/api/public/voucher/quote', [
            'DISCOUNT_CODE' => $voucher->CODE,
            'EMAIL' => 'money@example.com',
            'SO_DIEN_THOAI' => '0909000020',
            'ITEMS' => $items,
        ])
            ->assertOk()
            ->assertJsonPath('DATAS.VOUCHER.subtotal', 500000)
            ->assertJsonPath('DATAS.VOUCHER.discount_amount', 30000)
            ->assertJsonPath('DATAS.VOUCHER.total', 500000);

        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(false);
        $this->app->instance(SapoService::class, $sapo);

        $response = $this->postJson('http://localhost/api/public/transaction/place-order', [
            'name' => 'Khách kiểm tra tiền',
            'phone' => '0909000020',
            'email' => 'money@example.com',
            'address' => '123 Đường kiểm tra',
            'discount_code' => strtolower($voucher->CODE),
            'ITEMS' => $items,
        ])->assertOk();

        $transactionId = (int) $response->json('DATAS.TRANSACTION.ID');
        $transaction = Transaction::query()->findOrFail($transactionId);
        $this->assertSame(500000, (int) $transaction->SUBTOTAL_PRICE);
        $this->assertSame(30000, (int) $transaction->SHIPPING_FEE);
        $this->assertSame(30000, (int) $transaction->DISCOUNT_AMOUNT);
        $this->assertSame(500000, (int) $transaction->TOTAL_PRICE);
        $this->assertSame($voucher->CODE, $transaction->DISCOUNT_CODE);
        $this->assertSame($voucher->ID, (int) $transaction->DISCOUNT_VOUCHER_ID);
    }

    public function test_create_command_uses_basis_points_and_saves_sapo_ids(): void
    {
        $code = 'CMD'.random_int(100000, 999999);
        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(true);
        $sapo->expects($this->exactly(2))
            ->method('post')
            ->willReturnCallback(function (string $path, array $payload) use ($code): array {
                if ($path === '/admin/price_rules.json') {
                    $this->assertSame($code, $payload['price_rule']['title']);
                    $this->assertSame('-10', $payload['price_rule']['value']);
                    $this->assertSame('30000', $payload['price_rule']['value_limit_amount']);

                    return ['price_rule' => ['id' => 111001, 'title' => $code]];
                }

                $this->assertSame('/admin/price_rules/111001/discount_codes.json', $path);
                $this->assertSame($code, $payload['discount_code']['code']);

                return ['discount_code' => ['id' => 222002, 'code' => $code]];
            });
        $this->app->instance(SapoService::class, $sapo);

        $exitCode = Artisan::call('voucher:create', [
            'code' => $code,
            '--type' => 'percentage',
            '--value' => '10',
            '--max-discount' => '30000',
            '--min-subtotal' => '100000',
            '--usage-limit' => '50',
            '--once-per-customer' => true,
        ]);

        $this->assertSame(0, $exitCode, Artisan::output());
        $voucher = DiscountVoucher::query()->where('CODE', $code)->firstOrFail();
        $this->assertSame(1000, (int) $voucher->DISCOUNT_VALUE);
        $this->assertSame(30000, (int) $voucher->MAX_DISCOUNT_AMOUNT);
        $this->assertSame(100000, (int) $voucher->MIN_SUBTOTAL);
        $this->assertSame(50, (int) $voucher->USAGE_LIMIT);
        $this->assertTrue((bool) $voucher->ONCE_PER_CUSTOMER);
        $this->assertSame(111001, (int) $voucher->SAPO_PRICE_RULE_ID);
        $this->assertSame(222002, (int) $voucher->SAPO_DISCOUNT_CODE_ID);
    }

    public function test_voucher_list_returns_sapo_description_and_order_eligibility(): void
    {
        [, $variant] = $this->catalogItem(500000, 2);
        $voucher = $this->voucher([
            'CODE' => 'LIST'.random_int(100000, 999999),
            'TITLE' => 'Voucher từ Sapo',
            'DESCRIPTION' => 'Giảm 10% tối đa 50.000₫ cho toàn bộ đơn hàng.',
            'MAX_DISCOUNT_AMOUNT' => 50000,
            'MIN_SUBTOTAL' => 300000,
            'SAPO_PRICE_RULE_ID' => 123456,
        ]);

        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(false);
        $this->app->instance(SapoService::class, $sapo);

        $this->postJson('http://localhost/api/public/voucher/list', [
            'EMAIL' => 'list@example.com',
            'SO_DIEN_THOAI' => '0909000099',
            'ITEMS' => [[
                'PRODUCT_ID' => $variant->ID,
                'QUANTITY' => 1,
            ]],
        ])
            ->assertOk()
            ->assertJsonFragment([
                'code' => $voucher->CODE,
                'description' => 'Giảm 10% tối đa 50.000₫ cho toàn bộ đơn hàng.',
                'eligible' => true,
                'discount_amount' => 50000,
                'source' => 'SAPO',
            ]);
    }

    public function test_sapo_sync_imports_rule_summary_codes_and_maximum_discount(): void
    {
        $code = 'SAPO'.random_int(100000, 999999);
        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(true);
        $sapo->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function (string $path, array $query) use ($code): array {
                $this->assertSame(250, $query['limit']);
                if ($path === '/admin/price_rules.json') {
                    return ['price_rules' => [[
                        'id' => 919191,
                        'title' => $code,
                        'value' => '-10',
                        'value_type' => 'percentage',
                        'target_type' => 'line_item',
                        'status' => 'active',
                        'value_limit_amount' => '50000',
                        'usage_limit' => 100,
                        'times_used' => 3,
                        'once_per_customer' => true,
                        'starts_on' => now()->subHour()->utc()->toIso8601String(),
                        'ends_on' => null,
                        'prerequisite_subtotal_range' => [
                            'greater_than_or_equal_to' => 300000,
                        ],
                        'summary' => 'Mô tả điều kiện do Sapo trả về',
                    ]]];
                }

                $this->assertSame('/admin/price_rules/919191/discount_codes.json', $path);

                return ['discount_codes' => [[
                    'id' => 818181,
                    'code' => $code,
                    'usage_count' => 3,
                ]]];
            });
        $this->app->instance(SapoService::class, $sapo);

        $result = app(SapoVoucherSynchronizer::class)->sync();
        $this->assertSame(['rules' => 1, 'codes' => 1], $result);

        $voucher = DiscountVoucher::query()->where('CODE', $code)->firstOrFail();
        $this->assertSame('Mô tả điều kiện do Sapo trả về', $voucher->DESCRIPTION);
        $this->assertSame(1000, (int) $voucher->DISCOUNT_VALUE);
        $this->assertSame(50000, (int) $voucher->MAX_DISCOUNT_AMOUNT);
        $this->assertSame(300000, (int) $voucher->MIN_SUBTOTAL);
        $this->assertSame(919191, (int) $voucher->SAPO_PRICE_RULE_ID);
        $this->assertSame(818181, (int) $voucher->SAPO_DISCOUNT_CODE_ID);
    }

    public function test_combinable_voucher_stacks_with_percentage_voucher(): void
    {
        $percent = $this->voucher([
            'DISCOUNT_VALUE' => 1000,
            'MAX_DISCOUNT_AMOUNT' => 30000,
        ]);
        $freeship = $this->combinableVoucher();
        $service = app(StorefrontVoucher::class);

        $quote = $service->quoteMany(
            [$percent->CODE, $freeship->CODE],
            500000,
            30000,
            null,
            'ship@example.com',
            '0909000030'
        );

        $this->assertSame(60000, $quote['discount_amount']);
        $this->assertSame(470000, $quote['total']);
        $this->assertSame($freeship->ID, $quote['extra_voucher_id']);
        $this->assertContains($percent->CODE, $quote['codes']);
        $this->assertContains($freeship->CODE, $quote['codes']);

        $this->expectException(ValidationException::class);
        $service->quoteMany(
            [$percent->CODE, $this->voucher()->CODE],
            500000,
            30000,
            null,
            'two@example.com',
            '0909000031'
        );
    }

    public function test_place_order_stores_both_voucher_codes(): void
    {
        $percent = $this->voucher([
            'DISCOUNT_VALUE' => 1000,
            'MAX_DISCOUNT_AMOUNT' => 30000,
        ]);
        $freeship = $this->combinableVoucher();
        $service = app(StorefrontVoucher::class);

        $redeemed = $service->redeemMany(
            [$percent->CODE, $freeship->CODE],
            500000,
            30000,
            null,
            'order@example.com',
            '0909000032'
        );

        $this->assertSame(60000, $redeemed['discount_amount']);
        $this->assertSame($percent->ID, $redeemed['voucher_id']);
        $this->assertSame($freeship->ID, $redeemed['extra_voucher_id']);
        $this->assertSame($freeship->CODE, $redeemed['extra_code']);
        $this->assertCount(2, $redeemed['vouchers']);
    }

    private function combinableVoucher(array $overrides = []): DiscountVoucher
    {
        return $this->voucher(array_merge([
            'DISCOUNT_TYPE' => StorefrontVoucher::TYPE_FIXED_AMOUNT,
            'DISCOUNT_VALUE' => 30000,
            'MAX_DISCOUNT_AMOUNT' => null,
            'MIN_SUBTOTAL' => 0,
            'SAPO_PAYLOAD' => [
                'price_rule' => [
                    'combines_with' => [
                        'order_discount' => true,
                        'product_discount' => true,
                        'shipping_discount' => true,
                    ],
                ],
            ],
        ], $overrides));
    }

    private function voucher(array $overrides = []): DiscountVoucher
    {
        $suffix = (string) random_int(100000, 999999);

        return DiscountVoucher::query()->create(array_merge([
            'CODE' => 'TEST'.$suffix,
            'TITLE' => 'Voucher test '.$suffix,
            'DISCOUNT_TYPE' => StorefrontVoucher::TYPE_PERCENTAGE,
            'DISCOUNT_VALUE' => 1000,
            'MAX_DISCOUNT_AMOUNT' => 30000,
            'MIN_SUBTOTAL' => 0,
            'USAGE_LIMIT' => null,
            'ONCE_PER_CUSTOMER' => false,
            'STARTS_AT' => now()->subMinute(),
            'ENDS_AT' => now()->addDay(),
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ], $overrides));
    }

    /**
     * @return array{Product, ProductVariant}
     */
    private function catalogItem(int $price, int $stock): array
    {
        $suffix = (string) random_int(1000000, 9999999);
        $product = Product::query()->create([
            'NAME' => 'Sản phẩm voucher '.$suffix,
            'UUID' => (string) Str::uuid(),
            'KEYWORDS_SEO_WEBSITE' => 'voucher-'.$suffix,
            'SAPO_ID' => 940000000 + (int) $suffix,
            'PRODUCT_QUANTITY' => $stock,
            'PRICE' => $price,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        $variant = ProductVariant::query()->create([
            'PRODUCT_ID' => $product->ID,
            'SAPO_VARIANT_ID' => 930000000 + (int) $suffix,
            'PRODUCT_STATUS' => 'CON_HANG',
            'PRODUCT_COLOR' => 'Mặc định',
            'PRODUCT_PRICE' => $price,
            'INVENTORY_QUANTITY' => $stock,
            'IS_IN_STOCK' => true,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        return [$product, $variant];
    }
}
