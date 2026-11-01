<?php

namespace App\Dto\transaction;

class TransactionDetailDto implements \JsonSerializable
{
    public ?int $id;
    public ?string $maDon;
    public ?string $hoTen;
    public ?string $soDienThoai;
    public ?string $email;
    public ?string $diaChi;
    public ?string $ghiChu;
    public ?float $tongSoLuong;
    public ?float $tongTien;
    public ?string $trangThaiGiaoDich;
    public ?string $trangThaiGiaoDichText;
    public ?string $phuongThucThanhToan;
    public ?string $ngayTao;
    public float $subtotal = 0;
    public float $shippingFee = 0;
    public float $discountAmount = 0;
    public array $vouchers = [];
    public ?string $nhanVien = null;
    public ?string $ngayHenGiao = null;
    public ?string $lyDoHuy = null;
    public array $danhSachSanPham = [];

    public static function createEmpty(): TransactionDetailDto
    {
        return new TransactionDetailDto();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'ID' => $this->id,
            'MA_DON' => $this->maDon,
            'HO_TEN' => $this->hoTen,
            'SO_DIEN_THOAI' => $this->soDienThoai,
            'EMAIL' => $this->email,
            'DIA_CHI' => $this->diaChi,
            'GHI_CHU' => $this->ghiChu,
            'TONG_SO_LUONG' => $this->tongSoLuong,
            'TONG_TIEN' => $this->tongTien,
            'TRANG_THAI_GIAO_DICH' => $this->trangThaiGiaoDich,
            'TRANG_THAI_GIAO_DICH_TEXT' => $this->trangThaiGiaoDichText,
            'PHUONG_THUC_THANH_TOAN' => $this->phuongThucThanhToan,
            'NGAY_TAO' => $this->ngayTao,
            'SUBTOTAL' => $this->subtotal,
            'SHIPPING_FEE' => $this->shippingFee,
            'DISCOUNT_AMOUNT' => $this->discountAmount,
            'VOUCHERS' => $this->vouchers,
            'NHAN_VIEN' => $this->nhanVien,
            'NGAY_HEN_GIAO' => $this->ngayHenGiao,
            'LY_DO_HUY' => $this->lyDoHuy,
            'DANH_SACH_SAN_PHAM' => $this->danhSachSanPham,
        ];
    }
}
