<?php

namespace App\Mapper;

use App\Dto\categoryP\CategoryPDetailDto;
use App\Dto\documentStorage\DocumentStorageDetailDto;
use App\Dto\product\ProductDetailDto;
use App\Dto\productVariant\ProductVariantDetailDto;
use App\Enum\AppConstant;

class SapoMapper
{
    public static function mapProduct(array $product): ProductDetailDto
    {
        $variants = is_array($product['variants'] ?? null) ? $product['variants'] : [];
        $firstVariant = $variants[0] ?? [];

        // Giá hiển thị trên list = giá biến thể thấp nhất
        $price = 0.0;
        $compare = null;
        $priceVariant = is_array($firstVariant) ? $firstVariant : [];
        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }
            $variantPrice = isset($variant['price']) ? (float) $variant['price'] : 0.0;
            if ($variantPrice <= 0) {
                continue;
            }
            if ($price <= 0 || $variantPrice < $price) {
                $price = $variantPrice;
                $priceVariant = $variant;
                $variantCompare = isset($variant['compare_at_price']) && $variant['compare_at_price'] !== null
                    ? (float) $variant['compare_at_price']
                    : null;
                $compare = $variantCompare;
            }
        }
        if ($price <= 0 && is_array($firstVariant) && isset($firstVariant['price'])) {
            $price = (float) $firstVariant['price'];
            $compare = isset($firstVariant['compare_at_price']) && $firstVariant['compare_at_price'] !== null
                ? (float) $firstVariant['compare_at_price']
                : null;
            $priceVariant = $firstVariant;
        }

        $name = (string) ($product['name'] ?? '');
        $alias = trim((string) ($product['alias'] ?? ''));
        $slug = $alias !== '' ? $alias : convertStrToSlug($name);

        $dto = new ProductDetailDto(
            id: (int) ($product['id'] ?? 0),
            name: $name,
            nameSlug: $slug,
            type: (string) ($product['product_type'] ?? AppConstant::TYPE_PRODUCT_COMMON),
            descriptionDetail: (string) ($product['content'] ?? ''),
            shortDescription: (string) ($product['summary'] ?? ''),
            giaCa: $price > 0 ? $price : null,
        );

        $dto->giaGoc = ($compare !== null && $compare > $price) ? $compare : null;
        $dto->maSanPham = (string) ($product['id'] ?? '');
        $dto->productTags = (string) ($product['tags'] ?? '');
        $dto->keywordsSeoWebsite = (string) ($product['meta_title'] ?? '');
        $dto->status = AppConstant::STATUS_USING;
        $dto->isActive = (($product['status'] ?? 'active') === 'active');
        $dto->isProductHot = false;
        $dto->isProductVip = false;
        $dto->isGiaCaLienHe = $price <= 0;
        $dto->crtDt = self::normalizeDate($product['created_on'] ?? null);
        $dto->updDt = self::normalizeDate($product['modified_on'] ?? null);
        $dto->pathView = 'sapo';
        $dto->loaiView = AppConstant::TYPE_PRODUCT_COMMON;
        $dto->attr1 = (string) ($priceVariant['id'] ?? $firstVariant['id'] ?? '');
        $dto->attr2 = (string) ($product['vendor'] ?? '');

        $images = is_array($product['images'] ?? null) ? $product['images'] : [];
        $primary = is_array($product['image'] ?? null) ? $product['image'] : ($images[0] ?? null);
        $imageById = [];
        $imageByVariantId = [];
        foreach ($images as $image) {
            if (! is_array($image) || ! isset($image['id'])) {
                continue;
            }
            $imageById[(int) $image['id']] = $image;
            $variantIds = is_array($image['variant_ids'] ?? null) ? $image['variant_ids'] : [];
            foreach ($variantIds as $vid) {
                $vid = (int) $vid;
                if ($vid > 0 && ! isset($imageByVariantId[$vid])) {
                    $imageByVariantId[$vid] = $image;
                }
            }
        }

        $dto->danhSachHinhAnhDaiDien = [];
        $dto->danhSachHinhAnh = [];
        $dto->danhSachVideo = [];
        $dto->danhSachFileDinhKem = [];

        if (is_array($primary) && ! empty($primary['src'])) {
            $dto->danhSachHinhAnhDaiDien[] = self::mapImage($primary, 'DANH_SACH_HINH_ANH_DAI_DIEN', true);
        }

        foreach ($images as $index => $image) {
            if (! is_array($image) || empty($image['src'])) {
                continue;
            }
            if ($index === 0 && is_array($primary) && (int) ($image['id'] ?? 0) === (int) ($primary['id'] ?? -1)) {
                continue;
            }
            $dto->danhSachHinhAnh[] = self::mapImage($image, 'DANH_SACH_HINH_ANH', false);
        }

        if ($dto->danhSachHinhAnh === [] && count($images) > 1 && is_array($images[1])) {
            $dto->danhSachHinhAnh[] = self::mapImage($images[1], 'DANH_SACH_HINH_ANH', false);
        }

        $options = is_array($product['options'] ?? null) ? $product['options'] : [];
        $optionName = trim((string) ($options[0]['name'] ?? ''));
        if ($optionName === '' || strcasecmp($optionName, 'Title') === 0) {
            $optionName = 'Phân loại';
        }
        $dto->tenNhomBienThe = $optionName;
        $dto->danhSachBienThe = self::mapVariants(
            $variants,
            (int) ($product['id'] ?? 0),
            $imageById,
            $imageByVariantId,
            $primary
        );

        return $dto;
    }

    /**
     * @param  array<int, array<string, mixed>>  $variants
     * @param  array<int, array<string, mixed>>  $imageById
     * @param  array<int, array<string, mixed>>  $imageByVariantId
     * @return array<int, ProductVariantDetailDto>
     */
    private static function mapVariants(
        array $variants,
        int $productId,
        array $imageById,
        array $imageByVariantId,
        mixed $primaryImage
    ): array {
        $result = [];
        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $price = isset($variant['price']) ? (float) $variant['price'] : 0.0;
            $compare = isset($variant['compare_at_price']) && $variant['compare_at_price'] !== null
                ? (float) $variant['compare_at_price']
                : null;
            $title = trim((string) ($variant['title'] ?? $variant['option1'] ?? 'Mặc định'));
            $qty = (int) ($variant['inventory_quantity'] ?? 0);
            $policy = (string) ($variant['inventory_policy'] ?? 'deny');
            $managed = (string) ($variant['inventory_management'] ?? '');
            $inStock = $managed === '' || $managed === 'null' || $qty > 0 || $policy === 'continue';

            $dto = ProductVariantDetailDto::createEmpty();
            $dto->id = (int) ($variant['id'] ?? 0);
            $dto->productId = $productId;
            $dto->title = $title;
            $dto->productColor = trim((string) ($variant['option1'] ?? $title));
            $dto->productStatus = $inStock ? 'CON_HANG' : 'HET_HANG';
            $dto->isContactPrice = $price <= 0;
            $dto->productPrice = $price > 0 ? $price : null;
            $dto->productOriginalPrice = ($compare !== null && $compare > $price) ? $compare : null;
            $dto->isInStock = $inStock;
            $dto->isActive = true;
            $dto->crtDt = self::normalizeDate($variant['created_on'] ?? null);
            $dto->updDt = self::normalizeDate($variant['modified_on'] ?? null);
            $dto->danhSachHinhAnhDaiDien = [];

            $imageId = isset($variant['image_id']) ? (int) $variant['image_id'] : 0;
            $variantId = (int) ($variant['id'] ?? 0);
            $image = null;
            if ($imageId > 0 && isset($imageById[$imageId])) {
                $image = $imageById[$imageId];
            } elseif ($variantId > 0 && isset($imageByVariantId[$variantId])) {
                $image = $imageByVariantId[$variantId];
            } elseif (is_array($primaryImage)) {
                $image = $primaryImage;
            }
            if (is_array($image) && ! empty($image['src'])) {
                $dto->danhSachHinhAnhDaiDien[] = self::mapImage($image, 'DANH_SACH_HINH_ANH_DAI_DIEN', true);
            }

            $result[] = $dto;
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, ProductDetailDto>
     */
    public static function mapProducts(array $products): array
    {
        $result = [];
        foreach ($products as $product) {
            if (is_array($product)) {
                $result[] = self::mapProduct($product);
            }
        }

        return $result;
    }

    public static function mapCollection(array $collection, int $sortOrder = 0): CategoryPDetailDto
    {
        $name = (string) ($collection['name'] ?? '');
        $alias = trim((string) ($collection['alias'] ?? ''));

        $dto = new CategoryPDetailDto(
            id: (int) ($collection['id'] ?? 0),
            parentId: null,
            name: $name,
            nameSlug: $alias !== '' ? $alias : convertStrToSlug($name),
            sortOrder: $sortOrder,
            description: (string) ($collection['description'] ?? ''),
            treeLevel: 1,
            countChildren: 0,
            danhSachHinhAnhDaiDien: [],
            danhSachChildren: [],
            crtDt: self::normalizeDate($collection['created_on'] ?? null),
            updDt: self::normalizeDate($collection['modified_on'] ?? null),
            status: AppConstant::STATUS_USING,
            isActive: true,
            pathView: 'sapo',
        );

        $image = $collection['image'] ?? null;
        if (is_array($image) && ! empty($image['src'])) {
            $dto->danhSachHinhAnhDaiDien = [self::mapImage($image, 'DANH_SACH_HINH_ANH_DAI_DIEN', true)];
        }

        return $dto;
    }

    /**
     * @param  array<int, array<string, mixed>>  $collections
     * @return array<int, CategoryPDetailDto>
     */
    public static function mapCollections(array $collections): array
    {
        $result = [];
        foreach ($collections as $index => $collection) {
            if (is_array($collection)) {
                $result[] = self::mapCollection($collection, $index + 1);
            }
        }

        return $result;
    }

    private static function mapImage(array $image, string $attr1, bool $isThumbnail): DocumentStorageDetailDto
    {
        $src = (string) ($image['src'] ?? '');
        $filename = (string) ($image['filename'] ?? basename(parse_url($src, PHP_URL_PATH) ?: 'image.jpg'));

        $dto = DocumentStorageDetailDto::createEmpty();
        $dto->id = isset($image['id']) ? (int) $image['id'] : null;
        $dto->name = $filename;
        $dto->originalName = $filename;
        $dto->extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg';
        $dto->path = $src;
        $dto->directory = null;
        $dto->size = isset($image['size']) ? (float) $image['size'] : null;
        $dto->typeFile = 'image';
        $dto->isThumnail = $isThumbnail;
        $dto->aspectRatio = '1x1';
        $dto->attr1 = $attr1;
        $dto->attr2 = '1x1';
        $dto->isActive = true;
        $dto->crtDt = self::normalizeDate($image['created_on'] ?? null);
        $dto->updDt = self::normalizeDate($image['modified_on'] ?? null);

        return $dto;
    }

    private static function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
