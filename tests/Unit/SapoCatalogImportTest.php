<?php

namespace Tests\Unit;

use App\Enum\AppConstant;
use App\Mapper\ProductMapper;
use App\Mapper\SapoMapper;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Service\impl\SapoImageDownloaderImpl;
use Tests\TestCase;

class SapoCatalogImportTest extends TestCase
{
    public function test_normalize_source_url_strips_query(): void
    {
        $src = 'https://bizweb.dktcdn.net/100/products/a.jpg?v=123';
        $this->assertSame(
            'https://bizweb.dktcdn.net/100/products/a.jpg',
            SapoImageDownloaderImpl::normalizeSourceUrl($src)
        );
    }

    public function test_resolve_extension_from_filename_and_mime(): void
    {
        $this->assertSame('jpg', SapoImageDownloaderImpl::resolveExtension(
            ['filename' => 'photo.jpeg'],
            'https://cdn.example/x.jpeg?v=1',
            'image/jpeg'
        ));
        $this->assertSame('png', SapoImageDownloaderImpl::resolveExtension(
            [],
            'https://cdn.example/x',
            'image/png'
        ));
        $this->assertNull(SapoImageDownloaderImpl::resolveExtension(
            ['filename' => 'file.pdf'],
            'https://cdn.example/file.pdf',
            'application/pdf'
        ));
    }

    public function test_product_mapper_keeps_local_variants_and_does_not_require_sapo_id(): void
    {
        $product = new Product();
        $product->ID = 9;
        $product->UUID = 'abc123';
        $product->NAME = 'Xe điều khiển local';
        $product->KEYWORDS_SEO_WEBSITE = 'xe';
        $product->PRICE = 100000;
        $product->ATTR3 = 'Màu sắc';
        $product->ATTR49 = AppConstant::TYPE_PRODUCT_COMMON;
        $product->STATUS = AppConstant::STATUS_USING;
        $product->IS_ACTIVE = true;
        $product->SAPO_ID = null;

        $variant = new ProductVariant();
        $variant->ID = 21;
        $variant->PRODUCT_ID = 9;
        $variant->PRODUCT_STATUS = 'CON_HANG';
        $variant->PRODUCT_COLOR = 'Đỏ';
        $variant->ATTR4 = 'Đỏ';
        $variant->PRODUCT_PRICE = 100000;
        $variant->IS_IN_STOCK = true;
        $variant->IS_ACTIVE = true;
        $product->setRelation('variants', collect([$variant]));
        $product->setRelation('avatars', collect());
        $product->setRelation('images', collect());
        $product->setRelation('videos', collect());
        $product->setRelation('files', collect());
        $product->setRelation('categories', collect());

        $dto = ProductMapper::mapProductDetailDtoFromEntity($product);

        $this->assertSame(9, $dto->id);
        $this->assertSame('Màu sắc', $dto->tenNhomBienThe);
        $this->assertCount(1, $dto->danhSachBienThe);
        $this->assertSame('Đỏ', $dto->danhSachBienThe[0]->productColor);
        $this->assertNull($product->SAPO_ID);
    }

    public function test_sapo_mapper_clamps_negative_inventory_and_marks_variant_sold_out(): void
    {
        $dto = SapoMapper::mapProduct([
            'id' => 123,
            'name' => 'Sản phẩm hết hàng',
            'status' => 'active',
            'variants' => [[
                'id' => 456,
                'title' => 'Mặc định',
                'price' => 100000,
                'inventory_quantity' => -5,
                'inventory_policy' => 'continue',
            ]],
        ]);

        $this->assertCount(1, $dto->danhSachBienThe);
        $this->assertSame(0, $dto->danhSachBienThe[0]->inventoryQuantity);
        $this->assertFalse($dto->danhSachBienThe[0]->isInStock);
        $this->assertSame('HET_HANG', $dto->danhSachBienThe[0]->productStatus);
    }
}
