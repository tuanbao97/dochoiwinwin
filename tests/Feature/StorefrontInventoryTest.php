<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Service\SapoService;
use App\Support\StorefrontInventory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontInventoryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('app.url', 'http://localhost');
    }

    public function test_cart_rejects_sold_out_and_cumulative_quantity_above_stock(): void
    {
        [$product, $variant] = $this->catalogItem(2);

        $this->postJson('http://localhost/cart/add', [
            'variantId' => $variant->ID,
            'quantity' => 2,
        ])->assertOk()->assertJsonPath('stock', 2);

        $this->postJson('http://localhost/cart/add', [
            'variantId' => $variant->ID,
            'quantity' => 1,
        ])->assertStatus(409)->assertJsonPath('stock', 2);

        $variant->update([
            'INVENTORY_QUANTITY' => 0,
            'IS_IN_STOCK' => false,
            'PRODUCT_STATUS' => 'HET_HANG',
        ]);

        $this->postJson('http://localhost/cart/add', [
            'variantId' => $variant->ID,
            'quantity' => 1,
        ])->assertStatus(409)->assertJsonPath('stock', 0);
    }

    public function test_stale_cart_is_clamped_and_removed_using_current_inventory(): void
    {
        [, $variant] = $this->catalogItem(2);
        $inventory = app(StorefrontInventory::class);

        $result = $inventory->reconcileCart([[
            'variant_id' => $variant->ID,
            'title' => 'Tên cũ',
            'quantity' => 9,
            'price' => 1,
            'line_price' => 9,
        ]]);

        $this->assertTrue($result['changed']);
        $this->assertSame(2, $result['items'][0]['quantity']);
        $this->assertSame(125000, $result['items'][0]['price']);
        $this->assertSame(2, $result['items'][0]['stock']);

        $variant->update(['INVENTORY_QUANTITY' => 0, 'IS_IN_STOCK' => false]);
        $result = $inventory->reconcileCart($result['items']);
        $this->assertSame([], $result['items']);
    }

    public function test_checkout_reserves_last_stock_and_rejects_the_next_order(): void
    {
        [, $variant] = $this->catalogItem(1);
        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(false);
        $this->app->instance(SapoService::class, $sapo);

        $payload = [
            'name' => 'Khách thử nghiệm',
            'phone' => '0909000111',
            'email' => 'stock@example.com',
            'address' => '123 Đường thử nghiệm',
            'ITEMS' => [[
                'PRODUCT_ID' => $variant->ID,
                'QUANTITY' => 1,
                'PRICE' => 1,
            ]],
        ];

        $this->postJson('http://localhost/api/public/transaction/place-order', $payload)
            ->assertOk();
        $variant->refresh();
        $this->assertSame(0, (int) $variant->INVENTORY_QUANTITY);
        $this->assertFalse((bool) $variant->IS_IN_STOCK);

        $this->postJson('http://localhost/api/public/transaction/place-order', $payload)
            ->assertStatus(422);
        $this->assertSame(0, (int) $variant->fresh()->INVENTORY_QUANTITY);
    }

    public function test_checkout_aggregates_duplicate_lines_before_stock_check(): void
    {
        [, $variant] = $this->catalogItem(1);

        $payload = [
            'name' => 'Khách thử nghiệm',
            'phone' => '0909000111',
            'address' => '123 Đường thử nghiệm',
            'ITEMS' => [
                ['PRODUCT_ID' => $variant->ID, 'QUANTITY' => 1, 'PRICE' => 1],
                ['PRODUCT_ID' => $variant->ID, 'QUANTITY' => 1, 'PRICE' => 1],
            ],
        ];

        $this->postJson('http://localhost/api/public/transaction/place-order', $payload)
            ->assertStatus(422);
        $this->assertSame(1, (int) $variant->fresh()->INVENTORY_QUANTITY);
    }

    /**
     * @return array{Product, ProductVariant}
     */
    private function catalogItem(int $stock): array
    {
        $suffix = (string) random_int(1000000, 9999999);
        $product = Product::query()->create([
            'NAME' => 'Sản phẩm test tồn kho '.$suffix,
            'UUID' => (string) Str::uuid(),
            'KEYWORDS_SEO_WEBSITE' => 'test-stock-'.$suffix,
            'SAPO_ID' => 980000000 + (int) $suffix,
            'PRODUCT_QUANTITY' => $stock,
            'PRICE' => 125000,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
        $variant = ProductVariant::query()->create([
            'PRODUCT_ID' => $product->ID,
            'SAPO_VARIANT_ID' => 970000000 + (int) $suffix,
            'PRODUCT_STATUS' => $stock > 0 ? 'CON_HANG' : 'HET_HANG',
            'PRODUCT_COLOR' => 'Mặc định',
            'PRODUCT_PRICE' => 125000,
            'INVENTORY_QUANTITY' => $stock,
            'IS_IN_STOCK' => $stock > 0,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        return [$product, $variant];
    }
}
