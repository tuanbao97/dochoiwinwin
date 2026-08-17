<?php

namespace Tests\Feature;

use App\Models\CategoryP;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontProductListingStockTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('app.url', 'http://localhost');
    }

    public function test_public_listing_exposes_variant_stock_for_sold_out_products(): void
    {
        $category = $this->category();
        [$inStock] = $this->catalogItem($category, 3);
        [$soldOut] = $this->catalogItem($category, 0);

        $response = $this->getJson('http://localhost/api/public/product/list?'.http_build_query([
            'PAGE' => 1,
            'PER_PAGE' => 50,
            'IS_API_PUBLIC' => 'true',
            'TRANG_THAI_HOAT_DONG' => 'true',
            'DANH_MUC_SAN_PHAM_ID' => [$category->ID],
        ]))->assertOk();

        $rows = collect($response->json('DATAS.PRODUCT.DATA'))->keyBy('ID');

        $this->assertSame(3, (int) $rows[$inStock->ID]['DANH_SACH_BIEN_THE'][0]['SO_LUONG_TON']);
        $this->assertTrue((bool) $rows[$inStock->ID]['DANH_SACH_BIEN_THE'][0]['CON_HANG']);
        $this->assertSame(0, (int) $rows[$soldOut->ID]['DANH_SACH_BIEN_THE'][0]['SO_LUONG_TON']);
        $this->assertFalse((bool) $rows[$soldOut->ID]['DANH_SACH_BIEN_THE'][0]['CON_HANG']);
    }

    public function test_con_hang_filter_drops_products_without_available_variants(): void
    {
        $category = $this->category();
        [$inStock] = $this->catalogItem($category, 3);
        [$soldOut] = $this->catalogItem($category, 0);

        $response = $this->getJson('http://localhost/api/public/product/list?'.http_build_query([
            'PAGE' => 1,
            'PER_PAGE' => 50,
            'IS_API_PUBLIC' => 'true',
            'TRANG_THAI_HOAT_DONG' => 'true',
            'CON_HANG' => 'true',
            'DANH_MUC_SAN_PHAM_ID' => [$category->ID],
        ]))->assertOk();

        $ids = collect($response->json('DATAS.PRODUCT.DATA'))->pluck('ID')->map('intval')->all();

        $this->assertContains((int) $inStock->ID, $ids);
        $this->assertNotContains((int) $soldOut->ID, $ids);
    }

    private function category(): CategoryP
    {
        return CategoryP::query()->create([
            'NAME' => 'Danh mục test tồn kho '.random_int(1000, 9999),
            'PARENT_ID' => null,
            'SORT_ORDER' => 0,
            'TREE_LEVEL' => 1,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);
    }

    /**
     * @return array{Product, ProductVariant}
     */
    private function catalogItem(CategoryP $category, int $stock): array
    {
        $suffix = (string) random_int(1000000, 9999999);
        $product = Product::query()->create([
            'NAME' => 'Sản phẩm test tồn kho '.$suffix,
            'UUID' => (string) Str::uuid(),
            'KEYWORDS_SEO_WEBSITE' => 'test-stock-'.$suffix,
            'SAPO_ID' => 960000000 + (int) $suffix,
            'PRODUCT_QUANTITY' => $stock,
            'PRICE' => 125000,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
        ]);

        DB::table('product_category')->insert([
            'PRODUCT_ID' => $product->ID,
            'CATEGORY_ID' => $category->ID,
            'STATUS' => 'USING',
            'IS_ACTIVE' => true,
            'CRT_DT' => now(),
            'UPD_DT' => now(),
        ]);

        $variant = ProductVariant::query()->create([
            'PRODUCT_ID' => $product->ID,
            'SAPO_VARIANT_ID' => 950000000 + (int) $suffix,
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
