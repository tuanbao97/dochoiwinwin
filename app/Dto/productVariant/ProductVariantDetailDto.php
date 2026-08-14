<?php

namespace App\Dto\productVariant;

class ProductVariantDetailDto implements \JsonSerializable
{
    public ?int $id;
    public ?int $productId;
    public ?string $productStatus;
    public ?string $productColor;
    public ?float $productStorage;

    public ?bool $isContactPrice;
    public ?float $productPrice;
    public ?float $productOriginalPrice;

    public ?bool $isInStock;
    
     /* @var array<DocumentStorageDetailDto>|null */
    public ?array $danhSachHinhAnhDaiDien;

    public ?string $title;

    public ?string $crtDt;
    public ?string $crtId;
    public ?string $updDt;
    public ?string $updId;
    public ?string $crtName;
    public ?string $updName;
    public ?bool $isActive;

    /**
     * Create a new class instance.
     */
    public function __construct(?int $id = null, ?int $productId = null
        , ?string $productStatus = null, ?string $productColor = null, ?string $productStorage = null
        , ?bool $isContactPrice = null, ?float $productPrice = null, ?float $productOriginalPrice = null
        , ?bool $isInStock = null
        , ?array $danhSachHinhAnhDaiDien = null
        , ?string $crtDt = null, ?string $crtId = null, ?string $updDt = null, ?string $updId = null, ?string $crtName = null, ?string $updName = null, ?bool $isActive = null
        , ?string $title = null)
    {
        $this->id = $id;
        $this->productId = $productId;

        $this->productStatus = $productStatus;
        $this->productColor = $productColor;
        $this->productStorage = $productStorage;

        $this->isContactPrice = $isContactPrice;
        $this->productPrice = $productPrice;
        $this->productOriginalPrice = $productOriginalPrice;

        $this->isInStock = $isInStock;

        $this->danhSachHinhAnhDaiDien = $danhSachHinhAnhDaiDien;
        $this->title = $title;

        $this->crtDt = $crtDt;
        $this->crtId = $crtId;
        $this->updDt = $updDt;
        $this->updId = $updId;
        $this->crtName = $crtName;
        $this->updName = $updName;
        $this->isActive = $isActive;
    }

    public static function createEmpty(): ProductVariantDetailDto {
        return new ProductVariantDetailDto();
    }
    
    public function jsonSerialize(): mixed {
        return [
            'ID' => $this->id
            , 'PRODUCT_ID' => $this->productId

            , 'TEN_BIEN_THE' => $this->title
            , 'TINH_TRANG_SAN_PHAM' => $this->productStatus
            , 'MAU_SAC' => $this->productColor
            , 'DUNG_LUONG' => $this->productStorage

            , 'GIA_LIEN_HE' => $this->isContactPrice
            , 'GIA_BAN' => $this->productPrice
            , 'GIA_GOC' => $this->productOriginalPrice

            , 'CON_HANG' => $this->isInStock

            , 'DANH_SACH_HINH_ANH_DAI_DIEN' => $this->danhSachHinhAnhDaiDien

            , 'CRT_DT' => $this->crtDt
            , 'CRT_ID' => $this->crtId
            , 'UPD_DT' => $this->updDt
            , 'UPD_ID' => $this->updId
            , 'CRT_NAME' => $this->crtName
            , 'UPD_NAME' => $this->updName
            , 'TRANG_THAI_HOAT_DONG' => $this->isActive
        ];
    }


}
