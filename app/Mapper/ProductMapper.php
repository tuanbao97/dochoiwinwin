<?php

namespace App\Mapper;

use App\Dto\address\AddressDetailDto;
use App\Dto\categoryP\CategoryPDetailDto;
use App\Dto\documentStorage\DocumentStorageDetailDto;
use App\Dto\product\ProductDetailDto;
use App\Dto\productVariant\ProductVariantDetailDto;
use App\Enum\AppConstant;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductMapper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function mapFromArray(Product $product, array $data) : ?Product {
        if ($product == null) return null;
        $product->ID = self::issetkey($data, 'ID');
        
        // Generate UUID chỉ khi tạo mới (UUID chưa có hoặc rỗng)
        if (empty($product->UUID)) {
            $product->UUID = self::generateUniqueUuid();
        }
        // Khi update: giữ nguyên UUID cũ, không cập nhật

        $maSanPham = self::issetkey($data, 'MA_SAN_PHAM');
        $maSanPham = is_string($maSanPham) ? trim($maSanPham) : $maSanPham;
        if ($maSanPham === null || $maSanPham === '') {
            // Chưa nhập: dùng ID nếu đã có (update); create sẽ gán sau khi save
            $product->MA_SAN_PHAM = !empty($product->ID) ? (string) $product->ID : null;
        } else {
            $product->MA_SAN_PHAM = (string) $maSanPham;
        }
        
        $product->NAME = self::issetkey($data, 'TEN_SAN_PHAM');
        $product->KEYWORDS_SEO_WEBSITE = self::issetkey($data, 'KEYWORDS_SEO_WEBSITE');
        if (array_key_exists('OPTIONS', $data)) {
            $product->VARIANT_OPTIONS = array_values($data['OPTIONS'] ?? []);
            $product->ATTR3 = $product->VARIANT_OPTIONS[0]['name'] ?? null;
        }
        
        $product->DESCRIPTION_DETAIL = self::issetkey($data, fieldName:'MO_TA_CHI_TIET');
        $product->DESCRIPTION_DETAIL_ONLY_TEXT = self::issetkey($data, fieldName:'MO_TA_CHI_TIET_ONLY_TEXT');
        
        // Xử lý giá cả dựa trên IS_GIA_CA_LIEN_HE
        $isGiaCaLienHe = filter_var(self::issetkey($data, 'IS_GIA_CA_LIEN_HE', false), FILTER_VALIDATE_BOOLEAN);
        if ($isGiaCaLienHe === true) {
            $product->PRICE = null; // Khi bật giá liên hệ thì không lưu giá cả
            $product->PRICE_SALE = null; // Giá gốc cũng không áp dụng khi giá liên hệ
        } else {
            $product->PRICE = self::issetkey($data, 'GIA_CA');
            $giaGoc = self::issetkey($data, 'GIA_GOC');
            $product->PRICE_SALE = ($giaGoc === null || $giaGoc === '') ? null : $giaGoc;
        }
        // Giá hiển thị (text)
        $product->PRICE_DISPLAY_TEXT = self::issetkey($data, 'GIA_HIEN_THI');

        // Đặc điểm lưu vào ATTR4 (HTML), bản text thuần vào ATTR5
        $product->ATTR4 = self::issetkey($data, 'DAC_DIEM');
        $product->ATTR5 = self::issetkey($data, 'DAC_DIEM_ONLY_TEXT');

        $product->PRODUCT_HOT = filter_var(self::issetkey($data, 'SAN_PHAM_NOI_BAT', false), FILTER_VALIDATE_BOOLEAN);
        $product->PRODUCT_VIP = filter_var(self::issetkey($data, 'SAN_PHAM_VIP', false), FILTER_VALIDATE_BOOLEAN);
        $product->IS_ACTIVE = filter_var(self::issetkey($data, 'TRANG_THAI_HOAT_DONG', true), FILTER_VALIDATE_BOOLEAN);

        $product->CRT_DT = !is_null($product->CRT_DT) ? $product->CRT_DT : Carbon::now();
        $product->UPD_DT = Carbon::now();
        $product->CRT_ID = !is_null($product->CRT_ID) ? $product->CRT_ID : Auth::user()->ID;
        $product->UPD_ID = Auth::user()->ID;
        $product->CRT_NAME = !is_null($product->CRT_NAME) ? $product->CRT_NAME : Auth::user()->FULL_NAME;
        $product->UPD_NAME = Auth::user()->FULL_NAME;

        return $product;
    }
    
    public static function mapProductDetailDtoFromEntity($product): ?ProductDetailDto {
        if ($product == null) return null;

        $productDto = new ProductDetailDto(
            id: $product->ID
            , giaCa: $product->PRICE
            , giaHienThi: $product->PRICE_DISPLAY_TEXT ?? null
        );
        $productDto->giaGoc = $product->PRICE_SALE !== null ? (float) $product->PRICE_SALE : null;
        $productDto->diaChiBds = AddressDetailDto::createEmpty();
        
        // Avatar hình đại diện
        if (isset($product->avatars) && count($product->avatars) > 0) {
            foreach ($product->avatars as $index => $avatar) {
                $avatarUpload = DocumentStorageDetailDto::createEmpty();
                $avatarUpload->id = $avatar->ID;
                $avatarUpload->name = $avatar->NAME;
                $avatarUpload->originalName = $avatar->ORIGINAL_NAME;
                $avatarUpload->extension = $avatar->EXTENSION;
                $avatarUpload->path = $avatar->PATH;
                $avatarUpload->directory = $avatar->DIRECTORY;
                $avatarUpload->size = $avatar->SIZE;
                $avatarUpload->md5 = $avatar->MD5;
                $avatarUpload->typeFile = $avatar->TYPE_FILE;
                $avatarUpload->description = $avatar->DESCRIPTION;

                $avatarUpload->crtId = $avatar->CRT_ID;
                $avatarUpload->crtName = $avatar->CRT_NAME;
                $avatarUpload->crtDt = $avatar->CRT_DT;
                $avatarUpload->updId = $avatar->UPD_ID;
                $avatarUpload->updName = $avatar->UPD_NAME;
                $avatarUpload->updDt = $avatar->UPD_DT;
                if (!is_null($avatar->IS_ACTIVE)) $avatarUpload->isActive = filter_var($avatar->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

                // Pivot
                $avatarUpload->sortOrder = $avatar->pivot->SORT_ORDER;
                $avatarUpload->type = $avatar->pivot->ATTR1;
                $avatarUpload->isThumnail = $avatar->pivot->IS_THUMNAIL;
                $avatarUpload->aspectRatio = $avatar->pivot->ATTR2 ?? '1x1';

                // Thêm vào danh sách mảng
                $productDto->danhSachHinhAnhDaiDien[] = $avatarUpload;
            }
        }

        // Avatar người liên hệ (nếu có alias từ LEFT JOIN)
        if (!is_null($product->OBJ_CONTACT_AVATAR_ID)) {
            $contactAvatar = DocumentStorageDetailDto::createEmpty();
            $contactAvatar->id = $product->OBJ_CONTACT_AVATAR_ID;
            $contactAvatar->name = $product->OBJ_CONTACT_AVATAR_NAME;
            $contactAvatar->originalName = $product->OBJ_CONTACT_AVATAR_ORIGINAL_NAME;
            $contactAvatar->extension = $product->OBJ_CONTACT_AVATAR_EXTENSION;
            $contactAvatar->path = $product->OBJ_CONTACT_AVATAR_PATH;
            $contactAvatar->directory = $product->OBJ_CONTACT_AVATAR_DIRECTORY;
            $contactAvatar->size = $product->OBJ_CONTACT_AVATAR_SIZE;
            $contactAvatar->md5 = $product->OBJ_CONTACT_AVATAR_MD5;
            $contactAvatar->typeFile = $product->OBJ_CONTACT_AVATAR_TYPE_FILE;
            $contactAvatar->description = $product->OBJ_CONTACT_AVATAR_DESCRIPTION;

            $contactAvatar->crtId = $product->OBJ_CONTACT_AVATAR_CRT_ID;
            $contactAvatar->crtName = $product->OBJ_CONTACT_AVATAR_CRT_NAME;
            $contactAvatar->crtDt = $product->OBJ_CONTACT_AVATAR_CRT_DT;
            $contactAvatar->updId = $product->OBJ_CONTACT_AVATAR_UPD_ID;
            $contactAvatar->updName = $product->OBJ_CONTACT_AVATAR_UPD_NAME;
            $contactAvatar->updDt = $product->OBJ_CONTACT_AVATAR_UPD_DT;
            if (!is_null($product->OBJ_CONTACT_AVATAR_IS_ACTIVE)) $contactAvatar->isActive = filter_var($product->OBJ_CONTACT_AVATAR_IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

            // Gắn vào DTO field riêng
            $productDto->nguoiLienHeAvatar = $contactAvatar;
        }

        // Hình ảnh upload
        if (isset($product->images) && count($product->images) > 0) {
            foreach ($product->images as $index => $image) {
                $imageUpload = DocumentStorageDetailDto::createEmpty();
                $imageUpload->id = $image->ID;
                $imageUpload->name = $image->NAME;
                $imageUpload->originalName = $image->ORIGINAL_NAME;
                $imageUpload->extension = $image->EXTENSION;
                $imageUpload->path = $image->PATH;
                $imageUpload->directory = $image->DIRECTORY;
                $imageUpload->size = $image->SIZE;
                $imageUpload->md5 = $image->MD5;
                $imageUpload->typeFile = $image->TYPE_FILE;
                $imageUpload->description = $image->DESCRIPTION;

                $imageUpload->crtId = $image->CRT_ID;
                $imageUpload->crtName = $image->CRT_NAME;
                $imageUpload->crtDt = $image->CRT_DT;
                $imageUpload->updId = $image->UPD_ID;
                $imageUpload->updName = $image->UPD_NAME;
                $imageUpload->updDt = $image->UPD_DT;
                if (!is_null($image->IS_ACTIVE)) $imageUpload->isActive = filter_var($image->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

                // Pivot
                $imageUpload->sortOrder = $image->pivot->SORT_ORDER;
                $imageUpload->type = $image->pivot->ATTR1;
                $imageUpload->isThumnail = $image->pivot->IS_THUMNAIL;
                $imageUpload->aspectRatio = $image->pivot->ATTR2 ?? '1x1';

                // Thêm vào danh sách mảng
                $productDto->danhSachHinhAnh[] = $imageUpload;
            }
        }

        // Video upload
        if (isset($product->videos) && count($product->videos) > 0) {
            foreach ($product->videos as $index => $video) {
                $videoUpload = DocumentStorageDetailDto::createEmpty();
                $videoUpload->id = $video->ID;
                $videoUpload->name = $video->NAME;
                $videoUpload->originalName = $video->ORIGINAL_NAME;
                $videoUpload->extension = $video->EXTENSION;
                $videoUpload->path = $video->PATH;
                $videoUpload->directory = $video->DIRECTORY;
                $videoUpload->size = $video->SIZE;
                $videoUpload->md5 = $video->MD5;
                $videoUpload->typeFile = $video->TYPE_FILE;
                $videoUpload->description = $video->DESCRIPTION;
                $videoUpload->imageThumnail = $video->ATTR1;

                $videoUpload->crtId = $video->CRT_ID;
                $videoUpload->crtName = $video->CRT_NAME;
                $videoUpload->crtDt = $video->CRT_DT;
                $videoUpload->updId = $video->UPD_ID;
                $videoUpload->updName = $video->UPD_NAME;
                $videoUpload->updDt = $video->UPD_DT;
                if (!is_null($video->IS_ACTIVE)) $videoUpload->isActive = filter_var($video->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

                // Pivot
                $videoUpload->sortOrder = $video->pivot->SORT_ORDER;
                $videoUpload->type = $video->pivot->ATTR1;
                $videoUpload->isThumnail = $video->pivot->IS_THUMNAIL;
                $videoUpload->aspectRatio = '1x1';

                // Thêm vào danh sách mảng
                $productDto->danhSachVideo[] = $videoUpload;
            }
        }

        // File đính kèm upload
        if (isset($product->files) && count($product->files) > 0) {
            foreach ($product->files as $index => $file) {
                $fileUpload = DocumentStorageDetailDto::createEmpty();
                $fileUpload->id = $file->ID;
                $fileUpload->name = $file->NAME;
                $fileUpload->originalName = $file->ORIGINAL_NAME;
                $fileUpload->extension = $file->EXTENSION;
                $fileUpload->path = $file->PATH;
                $fileUpload->directory = $file->DIRECTORY;
                $fileUpload->size = $file->SIZE;
                $fileUpload->md5 = $file->MD5;
                $fileUpload->typeFile = $file->TYPE_FILE;
                $fileUpload->description = $file->DESCRIPTION;
                $fileUpload->imageThumnail = $file->ATTR1;

                $fileUpload->crtId = $file->CRT_ID;
                $fileUpload->crtName = $file->CRT_NAME;
                $fileUpload->crtDt = $file->CRT_DT;
                $fileUpload->updId = $file->UPD_ID;
                $fileUpload->updName = $file->UPD_NAME;
                $fileUpload->updDt = $file->UPD_DT;
                if (!is_null($file->IS_ACTIVE)) $fileUpload->isActive = filter_var($file->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

                // Pivot
                $fileUpload->sortOrder = $file->pivot->SORT_ORDER;
                $fileUpload->type = $file->pivot->ATTR1;
                $fileUpload->isThumnail = $file->pivot->IS_THUMNAIL;

                // Thêm vào danh sách mảng
                $productDto->danhSachFileDinhKem[] = $fileUpload;
            }
        }

        // Danh mục sản phẩm
        if (isset($product->categories) && count($product->categories) > 0) {
            $productDto->danhMucSanPhams = [];
            foreach ($product->categories as $category) {
                $danhMucSanPham = CategoryPDetailDto::createEmpty();

                $danhMucSanPham->id = $category->ID;
                $danhMucSanPham->name = $category->NAME;
                $danhMucSanPham->parentId = $category->PARENT_ID;
                $danhMucSanPham->sortOrder = $category->SORT_ORDER;
                $danhMucSanPham->description = $category->DESCRIPTION;
                $danhMucSanPham->treeLevel = $category->TREE_LEVEL;

                $danhMucSanPham->crtId = $category->CRT_ID;
                $danhMucSanPham->crtName = $category->CRT_NAME;
                $danhMucSanPham->crtDt = $category->CRT_DT;
                $danhMucSanPham->updId = $category->UPD_ID;
                $danhMucSanPham->updName = $category->UPD_NAME;
                $danhMucSanPham->updDt = $category->UPD_DT;
                if (!is_null($category->IS_ACTIVE)) {
                    $danhMucSanPham->isActive = filter_var($category->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);
                }

                $productDto->danhMucSanPhams[] = $danhMucSanPham;
            }

            // Giữ field cũ để tương thích các client chỉ đọc một danh mục.
            $productDto->danhMucSanPham = $productDto->danhMucSanPhams[0];
        }

        // Biến thể sản phẩm
        $productDto->danhSachBienThe = [];
        $productDto->variantOptions = is_array($product->VARIANT_OPTIONS)
            ? $product->VARIANT_OPTIONS
            : [];
        if ($productDto->variantOptions === [] && is_array($product->SAPO_PAYLOAD)) {
            foreach (($product->SAPO_PAYLOAD['options'] ?? []) as $option) {
                if (!is_array($option)) continue;
                $name = trim((string) ($option['name'] ?? ''));
                if ($name === '') continue;
                $values = is_array($option['values'] ?? null)
                    ? array_values(array_filter(array_map('strval', $option['values'])))
                    : [];
                $productDto->variantOptions[] = ['name' => $name, 'values' => $values];
            }
        }
        $optionGroup = trim((string) ($product->ATTR3 ?? ''));
        $productDto->tenNhomBienThe = $optionGroup !== '' ? $optionGroup : 'Phân loại';
        if (isset($product->variants) && count($product->variants) > 0) {
            foreach ($product->variants as $index => $variant) {
                $bienTheSanPham = ProductVariantDetailDto::createEmpty();
                $bienTheSanPham->id = $variant->ID;
                $bienTheSanPham->productId = $product->ID;
                $bienTheSanPham->productStatus = $variant->PRODUCT_STATUS;
                $bienTheSanPham->productColor = $variant->PRODUCT_COLOR;
                $bienTheSanPham->title = $variant->ATTR4 ?: $variant->PRODUCT_COLOR;
                $bienTheSanPham->sapoVariantId = $variant->SAPO_VARIANT_ID;
                $bienTheSanPham->sku = $variant->SKU ?: $variant->ATTR1;
                $bienTheSanPham->inventoryQuantity = $variant->INVENTORY_QUANTITY;
                $bienTheSanPham->optionValues = is_array($variant->OPTION_VALUES)
                    ? $variant->OPTION_VALUES
                    : array_values(array_filter([
                        $variant->PRODUCT_COLOR,
                        $variant->ATTR2,
                        $variant->ATTR3,
                    ], static fn ($value) => $value !== null && $value !== ''));
                foreach ($bienTheSanPham->optionValues as $optionIndex => $optionValue) {
                    if (!isset($productDto->variantOptions[$optionIndex])) {
                        $productDto->variantOptions[$optionIndex] = [
                            'name' => $optionIndex === 0 ? $productDto->tenNhomBienThe : 'Thuộc tính '.($optionIndex + 1),
                            'values' => [],
                        ];
                    }
                    $values = $productDto->variantOptions[$optionIndex]['values'] ?? [];
                    if (!in_array($optionValue, $values, true)) {
                        $values[] = $optionValue;
                    }
                    $productDto->variantOptions[$optionIndex]['values'] = $values;
                }
                $bienTheSanPham->productStorage = $variant->PRODUCT_STORAGE;
                $bienTheSanPham->isContactPrice = $variant->IS_CONTACT_PRICE;
                $bienTheSanPham->productPrice = $variant->PRODUCT_PRICE;
                $bienTheSanPham->productOriginalPrice = $variant->PRODUCT_ORIGINAL_PRICE;
                $bienTheSanPham->isInStock = $variant->IS_IN_STOCK;
                $bienTheSanPham->isActive = $variant->IS_ACTIVE;
                $bienTheSanPham->danhSachHinhAnhDaiDien = [];

                $bienTheSanPham->crtId = $variant->CRT_ID;
                $bienTheSanPham->crtName = $variant->CRT_NAME;
                $bienTheSanPham->crtDt = $variant->CRT_DT;
                $bienTheSanPham->updId = $variant->UPD_ID;
                $bienTheSanPham->updName = $variant->UPD_NAME;
                $bienTheSanPham->updDt = $variant->UPD_DT;
                if (!is_null($variant->IS_ACTIVE)) $bienTheSanPham->isActive = filter_var($variant->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

                // Hình ảnh đại diện
                if (isset($variant->productImage) && !is_null($variant->productImage)) {
                    $hinhAnhDaiDien = DocumentStorageDetailDto::createEmpty();
                    $file = $variant->productImage;

                    $hinhAnhDaiDien->id = $file->ID;
                    $hinhAnhDaiDien->name = $file->NAME;
                    $hinhAnhDaiDien->originalName = $file->ORIGINAL_NAME;
                    $hinhAnhDaiDien->extension = $file->EXTENSION;
                    $hinhAnhDaiDien->path = $file->PATH;
                    $hinhAnhDaiDien->directory = $file->DIRECTORY;
                    $hinhAnhDaiDien->size = $file->SIZE;
                    $hinhAnhDaiDien->md5 = $file->MD5;
                    $hinhAnhDaiDien->typeFile = $file->TYPE_FILE;
                    $hinhAnhDaiDien->description = $file->DESCRIPTION;

                    $hinhAnhDaiDien->isThumnail = true;
                    $hinhAnhDaiDien->attr1 = 'DANH_SACH_HINH_ANH_DAI_DIEN';
                    $hinhAnhDaiDien->attr2 = '1x1';
                    $hinhAnhDaiDien->aspectRatio = '1x1';

                    $hinhAnhDaiDien->crtId = $file->CRT_ID;
                    $hinhAnhDaiDien->crtName = $file->CRT_NAME;
                    $hinhAnhDaiDien->crtDt = $file->CRT_DT;
                    $hinhAnhDaiDien->updId = $file->UPD_ID;
                    $hinhAnhDaiDien->updName = $file->UPD_NAME;
                    $hinhAnhDaiDien->updDt = $file->UPD_DT;
                    if (!is_null($file->IS_ACTIVE)) $hinhAnhDaiDien->isActive = filter_var($file->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

                    $bienTheSanPham->danhSachHinhAnhDaiDien[] = $hinhAnhDaiDien;
                }

                $productDto->danhSachBienThe[] = $bienTheSanPham;
            }
        }
        
        $productDto->name = $product->NAME;
        $productDto->uuid = $product->UUID;
        $productDto->maSanPham = !empty($product->MA_SAN_PHAM)
            ? (string) $product->MA_SAN_PHAM
            : (string) $product->ID;
        $productDto->nameSlug = convertStrToSlug($product->NAME);
        $productDto->keywordsSeoWebsite = $product->KEYWORDS_SEO_WEBSITE;

        

        $productDto->descriptionDetail = normalizeStorefrontRichHtml(convertMediaPathsToAbsolute($product->DESCRIPTION_DETAIL));
        $productDto->descriptionDetailOnlyText = $product->DESCRIPTION_DETAIL_ONLY_TEXT;



        $productDto->shortDescription = $product->SHORT_DESCRIPTION;

        // Loại View và Path View
        $productDto->loaiView = $product->ATTR49;
        $productDto->pathView = $product->ATTR50;

        // Attribute động
        $productDto->attr1 = $product->ATTR1;
        $productDto->attr2 = $product->ATTR2;
        $productDto->attr3 = $product->ATTR3;
        // Map đặc điểm ra DTO riêng để FE dùng key DAC_DIEM
        $productDto->dacDiem = $product->ATTR4;
        $productDto->attr4 = $product->ATTR4;
        $productDto->attr5 = $product->ATTR5;
        $productDto->attr6 = $product->ATTR6;
        $productDto->attr7 = $product->ATTR7;
        $productDto->attr8 = $product->ATTR8;
        $productDto->attr9 = $product->ATTR9;
        $productDto->attr10 = $product->ATTR10;
        $productDto->attr11 = $product->ATTR11;
        $productDto->attr12 = $product->ATTR12;
        $productDto->attr13 = $product->ATTR13;
        $productDto->attr14 = $product->ATTR14;
        $productDto->attr15 = $product->ATTR15;
        $productDto->attr16 = $product->ATTR16;
        $productDto->attr17 = $product->ATTR17;
        $productDto->attr18 = $product->ATTR18;
        $productDto->attr19 = $product->ATTR19;
        $productDto->attr20 = $product->ATTR20;
        $productDto->attr21 = $product->ATTR21;
        $productDto->attr22 = $product->ATTR22;
        $productDto->attr23 = $product->ATTR23;
        $productDto->attr24 = $product->ATTR24;
        $productDto->attr25 = $product->ATTR25;
        $productDto->attr26 = $product->ATTR26;
        $productDto->attr27 = $product->ATTR27;
        $productDto->attr28 = $product->ATTR28;
        $productDto->attr29 = $product->ATTR29;
        $productDto->attr30 = $product->ATTR30;
        $productDto->attr31 = $product->ATTR31;
        $productDto->attr32 = $product->ATTR32;
        $productDto->attr33 = $product->ATTR33;
        $productDto->attr34 = $product->ATTR34;
        $productDto->attr35 = $product->ATTR35;
        $productDto->attr36 = $product->ATTR36;
        $productDto->attr37 = $product->ATTR37;
        $productDto->attr38 = $product->ATTR38;
        $productDto->attr39 = $product->ATTR39;
        $productDto->attr40 = $product->ATTR40;
        $productDto->attr41 = $product->ATTR41;
        $productDto->attr42 = $product->ATTR42;
        $productDto->attr43 = $product->ATTR43;
        $productDto->attr44 = $product->ATTR44;
        $productDto->attr45 = $product->ATTR45;
        $productDto->attr46 = $product->ATTR46;
        $productDto->attr47 = $product->ATTR47;
        $productDto->attr48 = $product->ATTR48;
        $productDto->attr49 = $product->ATTR49;
        $productDto->attr50 = $product->ATTR50;

        // Thông tin modire và trạng thái hoạt động
        $productDto->crtId = $product->CRT_ID;
        $productDto->crtName = $product->CRT_NAME;
        $productDto->crtDt = $product->CRT_DT;
        $productDto->updId = $product->UPD_ID;
        $productDto->updName = $product->UPD_NAME;
        $productDto->updDt = $product->UPD_DT;
        $productDto->status = $product->STATUS;
        if (!is_null($product->IS_ACTIVE)) $productDto->isActive = filter_var($product->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);
        if (!is_null($product->PRODUCT_HOT)) $productDto->isProductHot = filter_var($product->PRODUCT_HOT, FILTER_VALIDATE_BOOLEAN);
        if (!is_null($product->PRODUCT_VIP ?? null)) $productDto->isProductVip = filter_var($product->PRODUCT_VIP, FILTER_VALIDATE_BOOLEAN);

        return $productDto;
    }

    public static function mapProductDetailListDtoFromEntity($product): ?ProductDetailDto {
        if ($product == null) return null;

        $productDto = new ProductDetailDto(
            id: $product->ID
            , giaCa: $product->PRICE
            , giaHienThi: $product->PRICE_DISPLAY_TEXT ?? null
        );
        $productDto->giaGoc = $product->PRICE_SALE !== null ? (float) $product->PRICE_SALE : null;
        $productDto->diaChiBds = AddressDetailDto::createEmpty();

        // Avatar hình đại diện
        if (!is_null($product->OBJ_AVATAR_ID)) {
            $avatarUpload = DocumentStorageDetailDto::createEmpty();
            $avatarUpload->id = $product->OBJ_AVATAR_ID;
            $avatarUpload->name = $product->OBJ_AVATAR_NAME;
            $avatarUpload->originalName = $product->OBJ_AVATAR_ORIGINAL_NAME;
            $avatarUpload->extension = $product->OBJ_AVATAR_EXTENSION;
            $avatarUpload->path = $product->OBJ_AVATAR_PATH;
            $avatarUpload->directory = $product->OBJ_AVATAR_DIRECTORY;
            $avatarUpload->size = $product->OBJ_AVATAR_SIZE;
            $avatarUpload->md5 = $product->OBJ_AVATAR_MD5;
            $avatarUpload->typeFile = $product->OBJ_AVATAR_TYPE_FILE;
            $avatarUpload->description = $product->OBJ_AVATAR_DESCRIPTION;

            $avatarUpload->crtId = $product->OBJ_AVATAR_CRT_ID;
            $avatarUpload->crtName = $product->OBJ_AVATAR_CRT_NAME;
            $avatarUpload->crtDt = $product->OBJ_AVATAR_CRT_DT;
            $avatarUpload->updId = $product->OBJ_AVATAR_UPD_ID;
            $avatarUpload->updName = $product->OBJ_AVATAR_UPD_NAME;
            $avatarUpload->updDt = $product->OBJ_AVATAR_UPD_DT;
            if (!is_null($product->OBJ_AVATAR_IS_ACTIVE)) $avatarUpload->isActive = filter_var($product->OBJ_AVATAR_IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

            // Pivot
            $avatarUpload->sortOrder = $product->OBJ_AVATAR_SORT_ORDER;
            $avatarUpload->type = $product->OBJ_AVATAR_TYPE;
            $avatarUpload->isThumnail = $product->OBJ_AVATAR_IS_THUMNAIL;
            $avatarUpload->aspectRatio = $product->OBJ_AVATAR_ASPECT_RATIO ?? '1x1';

            // Thêm vào danh sách mảng
            $productDto->danhSachHinhAnhDaiDien[] = $avatarUpload;
        }

        // Ảnh hover trên card danh sách (ảnh thứ 2 của sản phẩm)
        if (!is_null($product->OBJ_HOVER_ID ?? null)) {
            $hoverUpload = DocumentStorageDetailDto::createEmpty();
            $hoverUpload->id = $product->OBJ_HOVER_ID;
            $hoverUpload->name = $product->OBJ_HOVER_NAME;
            $hoverUpload->originalName = $product->OBJ_HOVER_ORIGINAL_NAME;
            $hoverUpload->extension = $product->OBJ_HOVER_EXTENSION;
            $hoverUpload->path = $product->OBJ_HOVER_PATH;
            $hoverUpload->directory = $product->OBJ_HOVER_DIRECTORY;
            $hoverUpload->size = $product->OBJ_HOVER_SIZE;
            $hoverUpload->md5 = $product->OBJ_HOVER_MD5;
            $hoverUpload->typeFile = $product->OBJ_HOVER_TYPE_FILE;
            $hoverUpload->description = $product->OBJ_HOVER_DESCRIPTION;

            $hoverUpload->crtId = $product->OBJ_HOVER_CRT_ID;
            $hoverUpload->crtName = $product->OBJ_HOVER_CRT_NAME;
            $hoverUpload->crtDt = $product->OBJ_HOVER_CRT_DT;
            $hoverUpload->updId = $product->OBJ_HOVER_UPD_ID;
            $hoverUpload->updName = $product->OBJ_HOVER_UPD_NAME;
            $hoverUpload->updDt = $product->OBJ_HOVER_UPD_DT;
            if (!is_null($product->OBJ_HOVER_IS_ACTIVE)) $hoverUpload->isActive = filter_var($product->OBJ_HOVER_IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);

            // Pivot
            $hoverUpload->sortOrder = $product->OBJ_HOVER_SORT_ORDER;
            $hoverUpload->type = $product->OBJ_HOVER_TYPE;
            $hoverUpload->isThumnail = false;
            $hoverUpload->aspectRatio = $product->OBJ_HOVER_ASPECT_RATIO ?? '1x1';

            $productDto->danhSachHinhAnh[] = $hoverUpload;
        }

        // Danh mục sản phẩm
        if (!is_null($product->OBJ_CATEGORY_ID)) {
            $danhMucSanPham = CategoryPDetailDto::createEmpty();
            
            $danhMucSanPham->id = $product->OBJ_CATEGORY_ID;
            $danhMucSanPham->name = $product->OBJ_CATEGORY_NAME;
            $danhMucSanPham->parentId = $product->OBJ_CATEGORY_PARENT_ID;
            $danhMucSanPham->sortOrder = $product->OBJ_CATEGORY_SORT_ORDER;
            $danhMucSanPham->description = $product->OBJ_CATEGORY_DESCRIPTION;
            $danhMucSanPham->treeLevel = $product->OBJ_CATEGORY_TREE_LEVEL;

            $danhMucSanPham->crtId = $product->OBJ_CATEGORY_CRT_ID;
            $danhMucSanPham->crtName = $product->OBJ_CATEGORY_CRT_NAME;
            $danhMucSanPham->crtDt = $product->OBJ_CATEGORY_CRT_DT;
            $danhMucSanPham->updId = $product->OBJ_CATEGORY_UPD_ID;
            $danhMucSanPham->updName = $product->OBJ_CATEGORY_UPD_NAME;
            $danhMucSanPham->updDt = $product->OBJ_CATEGORY_UPD_DT;
            if (!is_null($product->OBJ_CATEGORY_IS_ACTIVE)) $danhMucSanPham->isActive = filter_var($product->OBJ_CATEGORY_IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);
            
            $productDto->danhMucSanPham = $danhMucSanPham;
        }

        // Biến thể sản phẩm đã được xóa - không còn sử dụng
        
        // Tên sản phẩm
        $productDto->name = $product->NAME;
        $productDto->uuid = $product->UUID;
        $productDto->maSanPham = !empty($product->MA_SAN_PHAM)
            ? (string) $product->MA_SAN_PHAM
            : (string) $product->ID;
        $productDto->nameSlug = convertStrToSlug($product->NAME);
        $productDto->keywordsSeoWebsite = $product->KEYWORDS_SEO_WEBSITE;

        

        $productDto->descriptionDetail = $product->DESCRIPTION_DETAIL;
        $productDto->descriptionDetailOnlyText = $product->DESCRIPTION_DETAIL_ONLY_TEXT;
        $productDto->dacDiem = $product->ATTR4;

        $productDto->shortDescription = $product->SHORT_DESCRIPTION;

        // Attribute động
        $productDto->attr1 = $product->ATTR1;
        $productDto->attr2 = $product->ATTR2;
        $productDto->attr3 = $product->ATTR3;
        $productDto->attr4 = $product->ATTR4;
        $productDto->attr5 = $product->ATTR5;
        $productDto->attr6 = $product->ATTR6;
        $productDto->attr7 = $product->ATTR7;
        $productDto->attr8 = $product->ATTR8;
        $productDto->attr9 = $product->ATTR9;
        $productDto->attr10 = $product->ATTR10;
        $productDto->attr11 = $product->ATTR11;
        $productDto->attr12 = $product->ATTR12;
        $productDto->attr13 = $product->ATTR13;
        $productDto->attr14 = $product->ATTR14;
        $productDto->attr15 = $product->ATTR15;
        $productDto->attr16 = $product->ATTR16;
        $productDto->attr17 = $product->ATTR17;
        $productDto->attr18 = $product->ATTR18;
        $productDto->attr19 = $product->ATTR19;
        $productDto->attr20 = $product->ATTR20;
        $productDto->attr21 = $product->ATTR21;
        $productDto->attr22 = $product->ATTR22;
        $productDto->attr23 = $product->ATTR23;
        $productDto->attr24 = $product->ATTR24;
        $productDto->attr25 = $product->ATTR25;
        $productDto->attr26 = $product->ATTR26;
        $productDto->attr27 = $product->ATTR27;
        $productDto->attr28 = $product->ATTR28;
        $productDto->attr29 = $product->ATTR29;
        $productDto->attr30 = $product->ATTR30;
        $productDto->attr31 = $product->ATTR31;
        $productDto->attr32 = $product->ATTR32;
        $productDto->attr33 = $product->ATTR33;
        $productDto->attr34 = $product->ATTR34;
        $productDto->attr35 = $product->ATTR35;
        $productDto->attr36 = $product->ATTR36;
        $productDto->attr37 = $product->ATTR37;
        $productDto->attr38 = $product->ATTR38;
        $productDto->attr39 = $product->ATTR39;
        $productDto->attr40 = $product->ATTR40;
        $productDto->attr41 = $product->ATTR41;
        $productDto->attr42 = $product->ATTR42;
        $productDto->attr43 = $product->ATTR43;
        $productDto->attr44 = $product->ATTR44;
        $productDto->attr45 = $product->ATTR45;
        $productDto->attr46 = $product->ATTR46;
        $productDto->attr47 = $product->ATTR47;
        $productDto->attr48 = $product->ATTR48;
        $productDto->attr49 = $product->ATTR49;
        $productDto->attr50 = $product->ATTR50;

        // Thông tin modire và trạng thái hoạt động
        $productDto->crtId = $product->CRT_ID;
        $productDto->crtDt = $product->CRT_DT;
        $productDto->updId = $product->UPD_ID;
        $productDto->updDt = $product->UPD_DT;
        $productDto->status = $product->STATUS;
        if (!is_null($product->IS_ACTIVE)) $productDto->isActive = filter_var($product->IS_ACTIVE, FILTER_VALIDATE_BOOLEAN);
        if (!is_null($product->PRODUCT_HOT)) $productDto->isProductHot = filter_var($product->PRODUCT_HOT, FILTER_VALIDATE_BOOLEAN);
        if (!is_null($product->PRODUCT_VIP ?? null)) $productDto->isProductVip = filter_var($product->PRODUCT_VIP, FILTER_VALIDATE_BOOLEAN);

        return $productDto;
    }

    /**
     * @param Collection<Product> $listProduct
     * @return Collection<ProductDetailDto>
     */
    public static function mapListProductDetailFromPaginator(Collection $listProduct): Collection {
        $listProductDto = new Collection();
        if ($listProduct->isEmpty()) return $listProductDto;
        
        foreach ($listProduct as $key => $product) {
            $listProductDto->push(self::mapProductDetailListDtoFromEntity($product));
        }
        return $listProductDto;
    }

    private static function issetkey($array, $fieldName, $defaultValue = null) {
        return isset($array[$fieldName]) ? $array[$fieldName] : $defaultValue;
    }

    /**
     * Generate UUID 6 ký tự duy nhất (chỉ chữ và số, không ký tự đặc biệt)
     */
    private static function generateUniqueUuid(): string {
        $maxAttempts = 100;
        $attempt = 0;
        
        do {
            // Generate random 6 ký tự (a-z, A-Z, 0-9)
            $uuid = \Illuminate\Support\Str::random(6);
            // Đảm bảo chỉ chứa chữ và số (loại bỏ ký tự đặc biệt nếu có)
            $uuid = preg_replace('/[^a-zA-Z0-9]/', '', $uuid);
            
            // Nếu sau khi filter còn thiếu, generate lại
            while (strlen($uuid) < 6) {
                $uuid .= \Illuminate\Support\Str::random(1);
                $uuid = preg_replace('/[^a-zA-Z0-9]/', '', $uuid);
            }
            $uuid = substr($uuid, 0, 6);
            
            // Check unique trong database
            $exists = \App\Models\Product::where('UUID', $uuid)->exists();
            $attempt++;
            
        } while ($exists && $attempt < $maxAttempts);
        
        if ($attempt >= $maxAttempts) {
            throw new \Exception('Không thể tạo UUID duy nhất sau ' . $maxAttempts . ' lần thử.');
        }
        
        return $uuid;
    }

}
