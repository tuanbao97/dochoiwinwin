<?php

namespace App\Repository\impl;

use App\Enum\AppConstant;
use App\Mapper\ProductVariantMapper;
use App\Models\ProductVariant;
use App\Repository\BaseRepository;
use App\Repository\ProductVariantRepository;

class ProductVariantRepositoryImpl extends BaseRepository implements ProductVariantRepository
{
    public function getModel()
    {
        return ProductVariant::class;
    }

    public function deleteAllBienTheSanPhamSanPham($productId) : bool
    {
        $isDeleted = ProductVariant::where([
            ['PRODUCT_ID', '=', $productId],
        ])->delete();
        return $isDeleted;
    }

    public function saveBienTheSanPhams($productId, array $productVariants)
    {
        $keptIds = [];
        foreach ($productVariants as $variantInput) {
            $variantId = isset($variantInput['ID']) ? (int) $variantInput['ID'] : null;
            $variant = $variantId
                ? ProductVariant::query()->where('PRODUCT_ID', $productId)->where('ID', $variantId)->firstOrFail()
                : new ProductVariant();
            $optionValues = array_values(array_map(
                static fn ($value) => trim((string) $value),
                $variantInput['OPTION_VALUES'] ?? []
            ));
            $imageId = $variantInput['PRODUCT_IMAGE_ID']
                ?? ($variantInput['DANH_SACH_HINH_ANH_DAI_DIEN'][0]['ID'] ?? null);
            $isContactPrice = filter_var($variantInput['GIA_LIEN_HE'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $quantity = isset($variantInput['SO_LUONG_TON']) && $variantInput['SO_LUONG_TON'] !== ''
                ? (int) $variantInput['SO_LUONG_TON']
                : null;

            ProductVariantMapper::mapFromArray($variant, [
                'PRODUCT_ID' => $productId,
                'PRODUCT_STATUS' => ($variantInput['CON_HANG'] ?? true) ? 'CON_HANG' : 'HET_HANG',
                'PRODUCT_COLOR' => $optionValues[0] ?? ($variantInput['MAU_SAC'] ?? 'Mặc định'),
                'PRODUCT_STORAGE' => $optionValues[1] ?? null,
                'PRODUCT_IMAGE_ID' => $imageId,
                'IS_CONTACT_PRICE' => $isContactPrice,
                'PRODUCT_PRICE' => $isContactPrice ? null : ($variantInput['GIA_BAN'] ?? null),
                'PRODUCT_ORIGINAL_PRICE' => $isContactPrice ? null : ($variantInput['GIA_GOC'] ?? null),
                'IS_IN_STOCK' => filter_var($variantInput['CON_HANG'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'OPTION_VALUES' => $optionValues,
                'SKU' => trim((string) ($variantInput['SKU'] ?? '')) ?: null,
                'INVENTORY_QUANTITY' => $quantity,
                'TITLE' => trim((string) ($variantInput['TEN_BIEN_THE'] ?? implode(' / ', $optionValues))),
                'IS_ACTIVE' => filter_var($variantInput['TRANG_THAI_HOAT_DONG'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ])->save();
            $keptIds[] = (int) $variant->ID;
        }

        ProductVariant::query()
            ->where('PRODUCT_ID', $productId)
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('ID', $keptIds))
            ->update([
                'STATUS' => AppConstant::STATUS_DELETED,
                'IS_ACTIVE' => false,
            ]);
    }
}
