<?php

namespace App\Dto\product;

use App\Dto\address\AddressDetailDto;
use App\Dto\categoryP\CategoryPDetailDto;
use App\Dto\documentStorage\DocumentStorageDetailDto;
use App\Dto\user\UserDetailDto;
use stdClass;

class ProductDetailDto implements \JsonSerializable
{
    public ?int $id;
    public ?string $uuid;
    public ?string $maSanPham;
    public ?string $name;
    public ?string $nameSlug;
    public ?string $type;
    public ?string $typeDescription;

    public ?string $keywordsSeoWebsite;
    public ?string $descriptionDetail;
    public ?string $descriptionDetailOnlyText;
    public ?string $shortDescription;
    
    public ?string $loaiView;
    public ?string $pathView;

    public ?string $status;
    public ?string $crtDt;
    public ?string $crtId;
    public ?string $updDt;
    public ?string $updId;
    public ?string $crtName;
    public ?string $updName;
    public ?bool $isActive;

    public ?bool $isProductHot;
    public ?bool $isProductVip;
    public ?string $productTags;

    /** List API cửa hàng: JSON gọn cho card, không gửi mô tả/ATTR. */
    public bool $storefrontCardList = false;

    /* @var array<DocumentStorageDetailDto>|null */
    public ?array $danhSachHinhAnhDaiDien;

    /* @var array<DocumentStorageDetailDto>|null */
    public ?array $danhSachHinhAnh;

    /* @var array<DocumentStorageDetailDto>|null */
    public ?array $danhSachVideo;

    /* @var array<DocumentStorageDetailDto>|null */
    public ?array $danhSachFileDinhKem;



    public ?CategoryPDetailDto $danhMucSanPham;
    /** @var array<CategoryPDetailDto>|null */
    public ?array $danhMucSanPhams = null;

    /** @var array<\App\Dto\productVariant\ProductVariantDetailDto>|null */
    public ?array $danhSachBienThe = null;

    /** Nhãn nhóm biến thể Sapo (vd: Màu sắc, Phân loại) */
    public ?string $tenNhomBienThe = null;

    /** @var array<int, array{name:string, values:array<int, string>}>|null */
    public ?array $variantOptions = null;

    public ?AddressDetailDto $diaChiBds;
    public ?string $googleMapPinsPosition;

    public ?float $giaCa;
    public ?string $giaHienThi;
    public ?float $giaGoc;
    public ?string $huongDat;
    public ?string $linkGoogleDrive;
    public ?string $dacDiem;
    public ?int $nguoiLienHeId;
    public ?string $nguoiLienHeName;
    public ?string $nguoiLienHeEmail;
    public ?string $nguoiLienHeUsername;
    public ?string $nguoiLienHeMobile;
    public ?string $nguoiLienHeAddress;
    public ?int $nguoiLienHeAvatarId;
    public ?DocumentStorageDetailDto $nguoiLienHeAvatar;
    public ?bool $isGiaCaLienHe;
    public ?float $giaThue;
    public ?float $soTienCoc;
    
    public ?float $dienTich;
    public ?float $chieuNgang;
    public ?float $chieuDai;

    public ?string $tieuDeBaiDang;
    public ?string $moTaChiTiet;

    public ?string $attr1;
    public ?string $attr2;
    public ?string $attr3;
    public ?string $attr4;
    public ?string $attr5;
    public ?string $attr6;
    public ?string $attr7;
    public ?string $attr8;
    public ?string $attr9;
    public ?string $attr10;
    public ?string $attr11;
    public ?string $attr12;
    public ?string $attr13;
    public ?string $attr14;
    public ?string $attr15;
    public ?string $attr16;
    public ?string $attr17;
    public ?string $attr18;
    public ?string $attr19;
    public ?string $attr20;
    public ?string $attr21;
    public ?string $attr22;
    public ?string $attr23;
    public ?string $attr24;
    public ?string $attr25;
    public ?string $attr26;
    public ?string $attr27;
    public ?string $attr28;
    public ?string $attr29;
    public ?string $attr30;
    public ?string $attr31;
    public ?string $attr32;
    public ?string $attr33;
    public ?string $attr34;
    public ?string $attr35;
    public ?string $attr36;
    public ?string $attr37;
    public ?string $attr38;
    public ?string $attr39;
    public ?string $attr40;
    public ?string $attr41;
    public ?string $attr42;
    public ?string $attr43;
    public ?string $attr44;
    public ?string $attr45;
    public ?string $attr46;
    public ?string $attr47;
    public ?string $attr48;
    public ?string $attr49;
    public ?string $attr50;
    
    /**
     * Create a new class instance.
     */
    public function __construct(?int $id = null, ?string $name = null, ?string $nameSlug = null, ?string $type = null,  ?string $typeDescription = null
        , ?string $descriptionDetail = null, ?string $descriptionDetailOnlyText = null

        , ?string $shortDescription = null
        , ?float $giaCa = null, ?string $giaHienThi = null, ?string $huongDat = null, ?string $linkGoogleDrive = null, ?int $nguoiLienHeId = null, ?string $nguoiLienHeName = null, ?string $nguoiLienHeEmail = null, ?string $nguoiLienHeUsername = null, ?string $nguoiLienHeMobile = null, ?string $nguoiLienHeAddress = null, ?int $nguoiLienHeAvatarId = null
        , ?float $dienTich = null, ?float $chieuNgang = null, ?float $chieuDai = null
        , ?string $googleMapPinsPosition = null

        , ?string $crtDt = null, ?string $crtId = null, ?string $updDt = null, ?string $updId = null, ?string $crtName = null, ?string $updName = null, ?bool $isActive = null, ?string $status = null
        , ?CategoryPDetailDto $danhMucSanPham = null
        , ?AddressDetailDto $diaChiBds = null
        , ?array $danhSachHinhAnhDaiDien = null, ?array $danhSachHinhAnh = null, ?array $danhSachVideo = null, ?array $danhSachFileDinhKem = null

        , ?string $loaiView = null, ?string $pathView = null
        , ? string $attr1 = null, ?string $attr2 = null, ?string $attr3 = null, ?string $attr4 = null, ?string $attr5 = null, ?string $attr6 = null, ?string $attr7 = null, ?string $attr8 = null, ?string $attr9 = null, ?string $attr10 = null, ?string $attr11 = null, ?string $attr12 = null, ?string $attr13 = null, ?string $attr14 = null, ?string $attr15 = null, ?string $attr16 = null, ?string $attr17 = null, ?string $attr18 = null, ?string $attr19 = null, ?string $attr20 = null, ?string $attr21 = null, ?string $attr22 = null, ?string $attr23 = null, ?string $attr24 = null, ?string $attr25 = null, ?string $attr26 = null, ?string $attr27 = null, ?string $attr28 = null, ?string $attr29 = null, ?string $attr30 = null, ?string $attr31 = null, ?string $attr32 = null, ?string $attr33 = null, ?string $attr34 = null, ?string $attr35 = null, ?string $attr36 = null, ?string $attr37 = null, ?string $attr38 = null, ?string $attr39 = null, ?string $attr40 = null, ?string $attr41 = null, ?string $attr42 = null, ?string $attr43 = null, ?string $attr44 = null, ?string $attr45 = null, ?string $attr46 = null, ?string $attr47 = null, ?string $attr48 = null, ?string $attr49 = null, ?string $attr50 = null
    )
    {
        $this->id = $id;
        $this->uuid = null;
        $this->maSanPham = null;
        $this->name = $name;
        $this->nameSlug = $nameSlug;
        $this->type = $type;
        $this->typeDescription = $typeDescription;

        $this->keywordsSeoWebsite = null;
        $this->descriptionDetail = $descriptionDetail;
        $this->descriptionDetailOnlyText = $descriptionDetailOnlyText;



        $this->shortDescription = $shortDescription;
        $this->giaCa = $giaCa;
        $this->giaHienThi = $giaHienThi;
        $this->giaGoc = null;
        $this->huongDat = $huongDat;
        $this->linkGoogleDrive = $linkGoogleDrive;
        $this->dacDiem = null;
        $this->nguoiLienHeId = $nguoiLienHeId;
        $this->nguoiLienHeName = $nguoiLienHeName;
        $this->nguoiLienHeEmail = $nguoiLienHeEmail;
        $this->nguoiLienHeUsername = $nguoiLienHeUsername;
        $this->nguoiLienHeMobile = $nguoiLienHeMobile;
        $this->nguoiLienHeAddress = $nguoiLienHeAddress;
        $this->nguoiLienHeAvatarId = $nguoiLienHeAvatarId;
        $this->nguoiLienHeAvatar = null;
        // Tính toán isGiaCaLienHe dựa vào giaCa
        $this->isGiaCaLienHe = ($giaCa === null || $giaCa === 0);
        
        $this->dienTich = $dienTich;
        $this->chieuNgang = $chieuNgang;
        $this->chieuDai = $chieuDai;
        $this->googleMapPinsPosition = $googleMapPinsPosition;


        $this->danhSachHinhAnhDaiDien = $danhSachHinhAnhDaiDien;
        $this->danhSachHinhAnh= $danhSachHinhAnh;
        $this->danhSachVideo = $danhSachVideo;
        $this->danhSachFileDinhKem = $danhSachFileDinhKem;
        $this->danhMucSanPham = $danhMucSanPham;
        $this->diaChiBds = $diaChiBds;



        $this->loaiView = $loaiView;
        $this->pathView = $pathView;

        $this->crtDt = $crtDt;
        $this->crtId = $crtId;
        $this->updDt = $updDt;
        $this->updId = $updId;
        $this->crtName = $crtName;
        $this->updName = $updName;
        $this->isActive = $isActive;
        $this->status = $status;
        $this->isProductHot = null;
        $this->isProductVip = null;
        $this->productTags = null;

        $this->attr1 = $attr1;
        $this->attr2 = $attr2;
        $this->attr3 = $attr3;
        $this->attr4 = $attr4;
        $this->attr5 = $attr5;
        $this->attr6 = $attr6;
        $this->attr7 = $attr7;
        $this->attr8 = $attr8;
        $this->attr9 = $attr9;
        $this->attr10 = $attr10;
        $this->attr11 = $attr11;
        $this->attr12 = $attr12;
        $this->attr13 = $attr13;
        $this->attr14 = $attr14;
        $this->attr15 = $attr15;
        $this->attr16 = $attr16;
        $this->attr17 = $attr17;
        $this->attr18 = $attr18;
        $this->attr19 = $attr19;
        $this->attr20 = $attr20;
        $this->attr21 = $attr21;
        $this->attr22 = $attr22;
        $this->attr23 = $attr23;
        $this->attr24 = $attr24;
        $this->attr25 = $attr25;
        $this->attr26 = $attr26;
        $this->attr27 = $attr27;
        $this->attr28 = $attr28;
        $this->attr29 = $attr29;
        $this->attr30 = $attr30;
        $this->attr31 = $attr31;
        $this->attr32 = $attr32;
        $this->attr33 = $attr33;
        $this->attr34 = $attr34;
        $this->attr35 = $attr35;
        $this->attr36 = $attr36;
        $this->attr37 = $attr37;
        $this->attr38 = $attr38;
        $this->attr39 = $attr39;
        $this->attr40 = $attr40;
        $this->attr41 = $attr41;
        $this->attr42 = $attr42;
        $this->attr43 = $attr43;
        $this->attr44 = $attr44;
        $this->attr45 = $attr45;
        $this->attr46 = $attr46;
        $this->attr47 = $attr47;
        $this->attr48 = $attr48;
        $this->attr49 = $attr49;
        $this->attr50 = $attr50;
    }

    public static function createEmpty() : ProductDetailDto {
        return new self();
    }
    
    public function jsonSerialize(): mixed {
        if ($this->storefrontCardList) {
            return $this->jsonSerializeStorefrontCard();
        }

        return [
            'ID' => $this->id
            , 'UUID' => $this->uuid
            , 'MA_SAN_PHAM' => $this->maSanPham
            , 'TEN_SAN_PHAM' => $this->name
            , 'TEN_SAN_PHAM_SLUG' => $this->nameSlug
            

            , 'DANH_SACH_HINH_ANH_DAI_DIEN' => $this->danhSachHinhAnhDaiDien
            , 'DANH_SACH_HINH_ANH' => $this->danhSachHinhAnh
            , 'DANH_SACH_VIDEO' => $this->danhSachVideo
            , 'DANH_SACH_FILE_DINH_KEM' => $this->danhSachFileDinhKem
            , 'DANH_MUC_SAN_PHAM' => $this->danhMucSanPham
            , 'DANH_MUC_SAN_PHAMS' => $this->danhMucSanPhams
            , 'DANH_SACH_BIEN_THE' => $this->danhSachBienThe
            , 'TEN_NHOM_BIEN_THE' => $this->tenNhomBienThe
            , 'OPTIONS' => $this->variantOptions
            , 'DIA_CHI' => $this->diaChiBds
            , 'GOOGLE_MAP_PINS_POSITION' => $this->googleMapPinsPosition

            
            , 'GIA_CA' => $this->giaCa
            , 'GIA_HIEN_THI' => $this->giaHienThi
            , 'GIA_GOC' => $this->giaGoc
            , 'KEYWORDS_SEO_WEBSITE' => $this->keywordsSeoWebsite
            , 'HUONG_DAT' => $this->huongDat
            , 'LINK_GOOGLE_DRIVE' => $this->linkGoogleDrive
            , 'DAC_DIEM' => $this->dacDiem
            , 'NGUOI_LIEN_HE_ID' => $this->nguoiLienHeId
            , 'NGUOI_LIEN_HE_NAME' => $this->nguoiLienHeName
            , 'NGUOI_LIEN_HE_EMAIL' => $this->nguoiLienHeEmail
            , 'NGUOI_LIEN_HE_USERNAME' => $this->nguoiLienHeUsername
            , 'NGUOI_LIEN_HE_MOBILE' => $this->nguoiLienHeMobile
            , 'NGUOI_LIEN_HE_ADDRESS' => $this->nguoiLienHeAddress
            , 'NGUOI_LIEN_HE_AVATAR_ID' => $this->nguoiLienHeAvatarId
            , 'NGUOI_LIEN_HE_AVATAR' => $this->nguoiLienHeAvatar
            , 'IS_GIA_CA_LIEN_HE' => $this->isGiaCaLienHe
            
            , 'DIEN_TICH' => $this->dienTich
            , 'CHIEU_NGANG' => $this->chieuNgang
            , 'CHIEU_DAI' => $this->chieuDai

            , 'MO_TA_CHI_TIET' => $this->descriptionDetail
            , 'MO_TA_CHI_TIET_ONLY_TEXT' => $this->descriptionDetailOnlyText

            , 'LOAI_VIEW' => $this->loaiView
            , 'PATH_VIEW' => $this->pathView

            , 'CRT_DT' => $this->crtDt
            , 'CRT_ID' => $this->crtId
            , 'UPD_DT' => $this->updDt
            , 'UPD_ID' => $this->updId
            , 'CRT_NAME' => $this->crtName
            , 'UPD_NAME' => $this->updName
            , 'TRANG_THAI_HOAT_DONG' => $this->isActive
            , 'SAN_PHAM_NOI_BAT' => $this->isProductHot
            , 'SAN_PHAM_VIP' => $this->isProductVip
            , 'TRANG_THAI' => $this->status

            , 'ATTR1' => $this->attr1
            , 'ATTR2' => $this->attr2
            , 'ATTR3' => $this->attr3
            , 'ATTR4' => $this->attr4
            , 'ATTR5' => $this->attr5
            , 'ATTR6' => $this->attr6
            , 'ATTR7' => $this->attr7
            , 'ATTR8' => $this->attr8
            , 'ATTR9' => $this->attr9
            , 'ATTR10' => $this->attr10
            , 'ATTR11' => $this->attr11
            , 'ATTR12' => $this->attr12
            , 'ATTR13' => $this->attr13
            , 'ATTR14' => $this->attr14
            , 'ATTR15' => $this->attr15
            , 'ATTR16' => $this->attr16
            , 'ATTR17' => $this->attr17
            , 'ATTR18' => $this->attr18
            , 'ATTR19' => $this->attr19
            , 'ATTR20' => $this->attr20
            , 'ATTR21' => $this->attr21
            , 'ATTR22' => $this->attr22
            , 'ATTR23' => $this->attr23
            , 'ATTR24' => $this->attr24
            , 'ATTR25' => $this->attr25
            , 'ATTR26' => $this->attr26
            , 'ATTR27' => $this->attr27
            , 'ATTR28' => $this->attr28
            , 'ATTR29' => $this->attr29
            , 'ATTR30' => $this->attr30
            , 'ATTR31' => $this->attr31
            , 'ATTR32' => $this->attr32
            , 'ATTR33' => $this->attr33
            , 'ATTR34' => $this->attr34
            , 'ATTR35' => $this->attr35
            , 'ATTR36' => $this->attr36
            , 'ATTR37' => $this->attr37
            , 'ATTR38' => $this->attr38
            , 'ATTR39' => $this->attr39
            , 'ATTR40' => $this->attr40
            , 'ATTR41' => $this->attr41
            , 'ATTR42' => $this->attr42
            , 'ATTR43' => $this->attr43
            , 'ATTR44' => $this->attr44
            , 'ATTR45' => $this->attr45
            , 'ATTR46' => $this->attr46
            , 'ATTR47' => $this->attr47
            , 'ATTR48' => $this->attr48
            , 'ATTR49' => $this->attr49
            , 'ATTR50' => $this->attr50
        ];
    }

    /**
     * Payload card: id, tên, giá, 2 ảnh, biến thể tối thiểu.
     *
     * @return array<string, mixed>
     */
    private function jsonSerializeStorefrontCard(): array
    {
        $category = null;
        if ($this->danhMucSanPham instanceof CategoryPDetailDto) {
            $category = ['ID' => $this->danhMucSanPham->id];
        } elseif (is_array($this->danhMucSanPham) && isset($this->danhMucSanPham['ID'])) {
            $category = ['ID' => $this->danhMucSanPham['ID']];
        }

        $variants = [];
        foreach ($this->danhSachBienThe ?? [] as $variant) {
            if ($variant instanceof \App\Dto\productVariant\ProductVariantDetailDto) {
                $variants[] = [
                    'ID' => $variant->id,
                    'GIA_BAN' => $variant->productPrice,
                    'GIA_GOC' => $variant->productOriginalPrice,
                    'SO_LUONG_TON' => $variant->inventoryQuantity,
                    'CON_HANG' => $variant->isInStock,
                ];
                continue;
            }
            if (is_array($variant)) {
                $variants[] = [
                    'ID' => $variant['ID'] ?? null,
                    'GIA_BAN' => $variant['GIA_BAN'] ?? null,
                    'GIA_GOC' => $variant['GIA_GOC'] ?? null,
                    'SO_LUONG_TON' => $variant['SO_LUONG_TON'] ?? null,
                    'CON_HANG' => $variant['CON_HANG'] ?? null,
                ];
            }
        }

        return [
            'ID' => $this->id,
            'TEN_SAN_PHAM' => $this->name,
            'TEN_SAN_PHAM_SLUG' => $this->nameSlug,
            'GIA_CA' => $this->giaCa,
            'GIA_HIEN_THI' => $this->giaHienThi,
            'GIA_GOC' => $this->giaGoc,
            'IS_GIA_CA_LIEN_HE' => $this->isGiaCaLienHe,
            'UPD_DT' => $this->updDt,
            'DANH_SACH_HINH_ANH_DAI_DIEN' => $this->slimCardImages($this->danhSachHinhAnhDaiDien, 1),
            'DANH_SACH_HINH_ANH' => $this->slimCardImages($this->danhSachHinhAnh, 1),
            'DANH_MUC_SAN_PHAM' => $category,
            'DANH_SACH_BIEN_THE' => $variants,
        ];
    }

    /**
     * @param  array<int, mixed>|null  $images
     * @return array<int, array<string, mixed>>
     */
    private function slimCardImages(?array $images, int $limit): array
    {
        $out = [];
        foreach (array_slice($images ?? [], 0, $limit) as $img) {
            if ($img instanceof DocumentStorageDetailDto) {
                $path = storefrontCdnCardThumb((string) ($img->path ?? ''));
                $out[] = [
                    'PATH' => $path !== '' ? $path : $img->path,
                    'DIRECTORY' => $img->directory,
                    'NAME' => $img->name,
                    'IMAGE_THUMNAIL' => $img->imageThumnail,
                    'ASPECT_RATIO' => $img->aspectRatio ?: '1x1',
                    'UPD_DT' => $img->updDt,
                ];
                continue;
            }
            if (is_array($img)) {
                $path = storefrontCdnCardThumb((string) ($img['PATH'] ?? ''));
                $out[] = [
                    'PATH' => $path !== '' ? $path : ($img['PATH'] ?? null),
                    'DIRECTORY' => $img['DIRECTORY'] ?? null,
                    'NAME' => $img['NAME'] ?? null,
                    'IMAGE_THUMNAIL' => $img['IMAGE_THUMNAIL'] ?? null,
                    'ASPECT_RATIO' => $img['ASPECT_RATIO'] ?? '1x1',
                    'UPD_DT' => $img['UPD_DT'] ?? null,
                ];
            }
        }

        return $out;
    }

}
