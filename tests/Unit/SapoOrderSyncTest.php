<?php

namespace Tests\Unit;

use App\Jobs\SyncSapoOrderJob;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Service\SapoService;
use App\Service\impl\SapoServiceImpl;
use App\Support\SapoOrderPuller;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use ReflectionMethod;
use Tests\TestCase;

class SapoOrderSyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_order_payload_maps_sapo_variant_customer_and_local_reference(): void
    {
        $transaction = new Transaction([
            'ID' => 123,
            'USER_BUY_EMAIL' => 'buyer@example.com',
            'USER_BUY_FULLNAME' => 'Nguyễn Văn An',
            'USER_BUY_PHONE' => '0909000111',
            'USER_BUY_ADDRESS' => '12 Nguyễn Trãi, TP.HCM',
            'USER_BUY_MESSAGE' => 'Giao giờ hành chính',
            'SHIPPING_FEE' => 30000,
            'DISCOUNT_CODE' => 'WINWIN10',
            'DISCOUNT_AMOUNT' => 30000,
        ]);
        $transaction->ID = 123;

        $item = new OrderItem([
            'PRODUCT_ID' => 10,
            'SAPO_VARIANT_ID' => 84440001,
            'QUANTITY' => 2,
        ]);
        $transaction->setRelation('orderItems', collect([$item]));

        $method = new ReflectionMethod(SyncSapoOrderJob::class, 'payload');
        $method->setAccessible(true);
        $payload = $method->invoke(new SyncSapoOrderJob(123), $transaction);
        $order = $payload['order'];

        $this->assertSame('pending', $order['financial_status']);
        $this->assertSame('buyer@example.com', $order['email']);
        $this->assertSame('buyer@example.com', $order['customer']['email']);
        $this->assertSame('+84909000111', $order['phone']);
        $this->assertSame('+84909000111', $order['customer']['phone']);
        $this->assertSame(
            [['variant_id' => 84440001, 'quantity' => 2]],
            $order['line_items']
        );
        $this->assertSame([
            ['title' => 'Phí vận chuyển', 'price' => 30000, 'code' => 'WEBSITE_FIXED'],
        ], $order['shipping_lines']);
        $this->assertSame([[
            'code' => 'WINWIN10',
            'amount' => 30000,
            'type' => 'fixed_amount',
        ]], $order['discount_codes']);
        $this->assertSame('+84909000111', $order['shipping_address']['phone']);
        $this->assertSame(
            ['name' => 'local_transaction_id', 'value' => '123'],
            $order['note_attributes'][0]
        );
    }

    public function test_order_payload_omits_empty_email_but_keeps_phone_contact(): void
    {
        $transaction = new Transaction([
            'ID' => 124,
            'USER_BUY_EMAIL' => '',
            'USER_BUY_FULLNAME' => 'Khách',
            'USER_BUY_PHONE' => '0909000111',
            'USER_BUY_ADDRESS' => '12 Nguyễn Trãi, TP.HCM',
        ]);
        $transaction->ID = 124;
        $transaction->setRelation('orderItems', collect([
            new OrderItem(['PRODUCT_ID' => 10, 'SAPO_VARIANT_ID' => 84440001, 'QUANTITY' => 1]),
        ]));

        $method = new ReflectionMethod(SyncSapoOrderJob::class, 'payload');
        $method->setAccessible(true);
        $payload = $method->invoke(new SyncSapoOrderJob(124), $transaction);
        $order = $payload['order'];

        $this->assertArrayNotHasKey('email', $order);
        $this->assertArrayNotHasKey('email', $order['customer']);
        $this->assertSame('+84909000111', $order['phone']);
        $this->assertSame('+84909000111', $order['customer']['phone']);
    }

    public function test_sapo_order_window_is_sent_in_utc(): void
    {
        $sapo = $this->createMock(SapoService::class);
        $sapo->expects($this->once())
            ->method('get')
            ->willReturnCallback(function (string $path, array $query): array {
                $this->assertSame('/admin/orders.json', $path);
                $this->assertSame('2026-08-17T07:00:00+00:00', $query['modified_on_min']);
                $this->assertSame('2026-08-17T07:30:00+00:00', $query['modified_on_max']);
                $this->assertSame(1, $query['page']);
                $this->assertSame(250, $query['limit']);

                return ['orders' => []];
            });

        $method = new ReflectionMethod(SapoOrderPuller::class, 'fetchWindow');
        $method->setAccessible(true);
        $orders = $method->invoke(
            new SapoOrderPuller($sapo),
            Carbon::parse('2026-08-17T14:00:00+07:00'),
            Carbon::parse('2026-08-17T14:30:00+07:00')
        );

        $this->assertSame([], $orders);
    }

    public function test_sapo_pull_replaces_items_and_recalculates_order_totals(): void
    {
        $transaction = Transaction::query()->create([
            'SAPO_ORDER_ID' => 990000001,
            'TOTAL_QUANTITY' => 1,
            'SUBTOTAL_PRICE' => 100000,
            'SHIPPING_FEE' => 30000,
            'TOTAL_PRICE' => 130000,
            'TRANSACTION_STATUS' => 'PENDING',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        $oldItem = OrderItem::query()->create([
            'TRANSACTION_ID' => $transaction->ID,
            'SAPO_LINE_ITEM_ID' => 7001,
            'SAPO_PRODUCT_ID' => 8001,
            'SAPO_VARIANT_ID' => 9001,
            'QUANTITY' => 1,
            'PRICE' => 100000,
            'ATTR1' => 'Sản phẩm cũ',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        $sapoOrder = [
            'id' => 990000001,
            'status' => 'open',
            'financial_status' => 'pending',
            'line_items' => [
                [
                    'id' => 7001,
                    'product_id' => 8001,
                    'variant_id' => 9001,
                    'current_quantity' => 0,
                    'quantity' => 1,
                    'deleted' => true,
                    'price' => 100000,
                    'name' => 'Sản phẩm cũ',
                ],
                [
                    'id' => 7002,
                    'product_id' => 8002,
                    'variant_id' => 9002,
                    'current_quantity' => 3,
                    'quantity' => 3,
                    'price' => 120000,
                    'name' => 'Sản phẩm mới',
                ],
            ],
            'current_subtotal_price' => 360000,
            'total_shipping_price' => 30000,
            'current_total_price' => 390000,
        ];

        $method = new ReflectionMethod(SapoOrderPuller::class, 'apply');
        $method->setAccessible(true);
        $changed = $method->invoke(
            new SapoOrderPuller($this->createMock(SapoService::class)),
            $transaction,
            $sapoOrder
        );

        $this->assertTrue($changed);
        $this->assertSame('DELETED', $oldItem->fresh()->STATUS);
        $newItem = OrderItem::query()
            ->where('TRANSACTION_ID', $transaction->ID)
            ->where('SAPO_LINE_ITEM_ID', 7002)
            ->firstOrFail();
        $this->assertNull($newItem->PRODUCT_ID);
        $this->assertSame(3.0, (float) $newItem->QUANTITY);
        $this->assertSame('Sản phẩm mới', $newItem->ATTR1);

        $transaction->refresh();
        $this->assertSame(3.0, (float) $transaction->TOTAL_QUANTITY);
        $this->assertSame(360000, (int) $transaction->SUBTOTAL_PRICE);
        $this->assertSame(30000, (int) $transaction->SHIPPING_FEE);
        $this->assertSame(390000, (int) $transaction->TOTAL_PRICE);
    }

    public function test_sapo_pull_does_not_inflate_capped_website_discount(): void
    {
        $transaction = Transaction::query()->create([
            'SAPO_ORDER_ID' => 990000010,
            'TOTAL_QUANTITY' => 1,
            'SUBTOTAL_PRICE' => 500000,
            'SHIPPING_FEE' => 30000,
            'DISCOUNT_CODE' => 'WINWIN10',
            'DISCOUNT_AMOUNT' => 30000,
            'DISCOUNT_SNAPSHOT' => [
                'type' => 'PERCENTAGE',
                'value' => 1000,
                'max_discount_amount' => 30000,
            ],
            'TOTAL_PRICE' => 500000,
            'TRANSACTION_STATUS' => 'PENDING',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        OrderItem::query()->create([
            'TRANSACTION_ID' => $transaction->ID,
            'SAPO_LINE_ITEM_ID' => 8010,
            'QUANTITY' => 1,
            'PRICE' => 500000,
            'ATTR1' => 'Xe điều khiển',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        $method = new ReflectionMethod(SapoOrderPuller::class, 'apply');
        $method->setAccessible(true);
        $method->invoke(
            new SapoOrderPuller($this->createMock(SapoService::class)),
            $transaction,
            [
                'id' => 990000010,
                'status' => 'open',
                'line_items' => [[
                    'id' => 8010,
                    'quantity' => 1,
                    'current_quantity' => 1,
                    'price' => 500000,
                    'name' => 'Xe điều khiển',
                ]],
                'current_subtotal_price' => 500000,
                'total_shipping_price' => 30000,
                'total_discounts' => 50000,
                'current_total_price' => 480000,
            ]
        );

        $transaction->refresh();
        $this->assertSame(500000, (int) $transaction->SUBTOTAL_PRICE);
        $this->assertSame(30000, (int) $transaction->DISCOUNT_AMOUNT);
        $this->assertSame(500000, (int) $transaction->TOTAL_PRICE);
    }

    public function test_sapo_pull_keeps_item_image_when_catalog_has_no_thumbnail(): void
    {
        $transaction = Transaction::query()->create([
            'SAPO_ORDER_ID' => 990000004,
            'TOTAL_QUANTITY' => 1,
            'SUBTOTAL_PRICE' => 100000,
            'SHIPPING_FEE' => 30000,
            'TOTAL_PRICE' => 130000,
            'TRANSACTION_STATUS' => 'PENDING',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        $item = OrderItem::query()->create([
            'TRANSACTION_ID' => $transaction->ID,
            'SAPO_LINE_ITEM_ID' => 7030,
            'QUANTITY' => 1,
            'PRICE' => 100000,
            'ATTR1' => 'Sản phẩm có ảnh',
            'ATTR2' => 'upload/UI-BACKEND/2026-08-14/1x1_anh-goc.jpg',
            'ATTR3' => 'san-pham-co-anh',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        $method = new ReflectionMethod(SapoOrderPuller::class, 'apply');
        $method->setAccessible(true);
        $method->invoke(
            new SapoOrderPuller($this->createMock(SapoService::class)),
            $transaction,
            [
                'id' => 990000004,
                'status' => 'open',
                'line_items' => [[
                    'id' => 7030,
                    'quantity' => 4,
                    'price' => 100000,
                    'name' => 'Sản phẩm có ảnh',
                ]],
                'current_subtotal_price' => 400000,
                'total_shipping_price' => 30000,
                'current_total_price' => 430000,
            ]
        );

        $item->refresh();
        $this->assertSame(4.0, (float) $item->QUANTITY);
        $this->assertSame('upload/UI-BACKEND/2026-08-14/1x1_anh-goc.jpg', $item->ATTR2);
        $this->assertSame('san-pham-co-anh', $item->ATTR3);
    }

    public function test_sapo_pull_without_line_items_keeps_existing_items(): void
    {
        $transaction = Transaction::query()->create([
            'SAPO_ORDER_ID' => 990000002,
            'TOTAL_QUANTITY' => 1,
            'SUBTOTAL_PRICE' => 100000,
            'SHIPPING_FEE' => 30000,
            'TOTAL_PRICE' => 130000,
            'TRANSACTION_STATUS' => 'PENDING',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        $item = OrderItem::query()->create([
            'TRANSACTION_ID' => $transaction->ID,
            'SAPO_LINE_ITEM_ID' => 7010,
            'QUANTITY' => 1,
            'PRICE' => 100000,
            'ATTR1' => 'Giữ nguyên',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        $method = new ReflectionMethod(SapoOrderPuller::class, 'apply');
        $method->setAccessible(true);
        $method->invoke(
            new SapoOrderPuller($this->createMock(SapoService::class)),
            $transaction,
            ['id' => 990000002, 'status' => 'open']
        );

        $this->assertSame('USING', $item->fresh()->STATUS);
        $this->assertSame(1.0, (float) $item->fresh()->QUANTITY);
    }

    public function test_cancelled_sapo_order_keeps_purchased_items_and_original_total(): void
    {
        $transaction = Transaction::query()->create([
            'SAPO_ORDER_ID' => 990000003,
            'TOTAL_QUANTITY' => 1,
            'SUBTOTAL_PRICE' => 180000,
            'SHIPPING_FEE' => 0,
            'TOTAL_PRICE' => 180000,
            'TRANSACTION_STATUS' => 'PENDING',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        $item = OrderItem::query()->create([
            'TRANSACTION_ID' => $transaction->ID,
            'SAPO_LINE_ITEM_ID' => 7020,
            'QUANTITY' => 1,
            'PRICE' => 180000,
            'ATTR1' => 'Búp bê Baby',
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        $method = new ReflectionMethod(SapoOrderPuller::class, 'apply');
        $method->setAccessible(true);
        $method->invoke(
            new SapoOrderPuller($this->createMock(SapoService::class)),
            $transaction,
            [
                'id' => 990000003,
                'status' => 'cancelled',
                'cancelled_on' => '2026-08-17T09:00:00Z',
                'cancel_reason' => 'customer',
                'line_items' => [[
                    'id' => 7020,
                    'quantity' => 1,
                    'current_quantity' => 0,
                    'deleted' => false,
                    'price' => 180000,
                    'name' => 'Búp bê Baby',
                ]],
                'current_subtotal_price' => 0,
                'subtotal_price' => 180000,
                'current_total_price' => 0,
                'total_price' => 180000,
                'total_shipping_price' => 0,
            ]
        );

        $this->assertSame('USING', $item->fresh()->STATUS);
        $this->assertSame(1.0, (float) $item->fresh()->QUANTITY);
        $transaction->refresh();
        $this->assertSame(180000, (int) $transaction->SUBTOTAL_PRICE);
        $this->assertSame(180000, (int) $transaction->TOTAL_PRICE);
        $this->assertSame('CANCELLED', $transaction->TRANSACTION_STATUS);
        $this->assertSame('Khách hàng yêu cầu hủy', $transaction->SAPO_CANCEL_REASON);
    }

    /**
     * @dataProvider sapoOrderStatuses
     *
     * @param  array<string, mixed>  $order
     */
    public function test_sapo_order_status_maps_to_local_transaction_status(array $order, string $expected): void
    {
        $method = new ReflectionMethod(SapoOrderPuller::class, 'mapStatus');
        $method->setAccessible(true);

        $this->assertSame(
            $expected,
            $method->invoke(new SapoOrderPuller($this->createMock(SapoService::class)), $order)->value
        );
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string}>
     */
    public static function sapoOrderStatuses(): array
    {
        return [
            'new order' => [['status' => 'open', 'financial_status' => 'pending'], 'PENDING'],
            'paid order' => [['status' => 'open', 'financial_status' => 'paid'], 'CONFIRMED'],
            'confirmed order' => [['status' => 'open', 'confirmed_on' => '2026-08-17T06:49:49Z'], 'CONFIRMED'],
            'shipping order' => [['status' => 'open', 'fulfillment_status' => 'partial'], 'SHIPPING'],
            'fulfilled order' => [['status' => 'closed', 'fulfillment_status' => 'fulfilled'], 'COMPLETED'],
            'cancelled order' => [
                ['status' => 'cancelled', 'cancelled_on' => '2026-08-17T06:40:32Z', 'financial_status' => 'paid'],
                'CANCELLED',
            ],
        ];
    }

    public function test_sapo_service_posts_json_with_existing_credentials(): void
    {
        config()->set('services.sapo', [
            'enabled' => true,
            'store' => 'example.mysapo.net',
            'api_key' => 'key',
            'api_secret' => 'secret',
            'timeout' => 10,
            'access_token' => null,
            'product_type' => 'Đồ chơi',
        ]);

        Http::fake([
            'https://example.mysapo.net/admin/orders.json' => Http::response([
                'order' => ['id' => 999],
            ], 201),
        ]);

        $response = (new SapoServiceImpl())->post('/admin/orders.json', [
            'order' => ['line_items' => [['variant_id' => 1, 'quantity' => 1]]],
        ]);

        $this->assertSame(999, $response['order']['id']);
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'https://example.mysapo.net/admin/orders.json'
            && $request['order']['line_items'][0]['variant_id'] === 1);
    }
}
