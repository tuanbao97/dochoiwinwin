<?php

namespace App\Mapper;

use App\Enum\AppConstant;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductVariantMapper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function mapFromArray(ProductVariant $productVariant, array $data) : ?ProductVariant {
        if ($productVariant == null) return null;

        if (array_key_exists('ID', $data) && $data['ID'] !== null) {
            $productVariant->ID = $data['ID'];
        }
        $productVariant->PRODUCT_ID = self::issetkey($data, 'PRODUCT_ID');
        $productVariant->PRODUCT_STATUS = self::issetkey($data, 'PRODUCT_STATUS');
        $productVariant->PRODUCT_COLOR = self::issetkey($data, 'PRODUCT_COLOR');
        $productVariant->PRODUCT_IMAGE_ID = self::issetkey($data, 'PRODUCT_IMAGE_ID');
        $productVariant->IS_CONTACT_PRICE = self::issetkey($data, 'IS_CONTACT_PRICE');
        $productVariant->PRODUCT_PRICE = self::issetkey($data, 'PRODUCT_PRICE');
        $productVariant->PRODUCT_ORIGINAL_PRICE = self::issetkey($data, 'PRODUCT_ORIGINAL_PRICE');
        $productVariant->IS_IN_STOCK = self::issetkey($data, 'IS_IN_STOCK');
        $productVariant->PRODUCT_STORAGE = self::issetkey($data, 'PRODUCT_STORAGE');
        $productVariant->OPTION_VALUES = self::issetkey($data, 'OPTION_VALUES', []);
        $productVariant->SKU = self::issetkey($data, 'SKU');
        $productVariant->INVENTORY_QUANTITY = self::issetkey($data, 'INVENTORY_QUANTITY');
        $productVariant->ATTR1 = $productVariant->SKU;
        $productVariant->ATTR2 = $productVariant->OPTION_VALUES[1] ?? null;
        $productVariant->ATTR3 = $productVariant->OPTION_VALUES[2] ?? null;
        $productVariant->ATTR4 = self::issetkey($data, 'TITLE', $productVariant->PRODUCT_COLOR);
        $productVariant->STATUS = AppConstant::STATUS_USING;

        $productVariant->IS_ACTIVE = filter_var(self::issetkey($data, 'IS_ACTIVE', true), FILTER_VALIDATE_BOOLEAN);

        $productVariant->CRT_DT = !is_null($productVariant->CRT_DT) ? $productVariant->CRT_DT : Carbon::now();
        $productVariant->UPD_DT = Carbon::now();
        $productVariant->CRT_ID = !is_null($productVariant->CRT_ID) ? $productVariant->CRT_ID : Auth::user()->ID;
        $productVariant->UPD_ID = Auth::user()->ID;
        $productVariant->CRT_NAME = !is_null($productVariant->CRT_NAME) ? $productVariant->CRT_NAME : Auth::user()->FULL_NAME;
        $productVariant->UPD_NAME = Auth::user()->FULL_NAME;
        
        return $productVariant;
    }

    private static function issetkey($array, $fiedlName, $defaultValue = null) {
        return isset($array[$fiedlName]) ? $array[$fiedlName] : $defaultValue;
    }
}
