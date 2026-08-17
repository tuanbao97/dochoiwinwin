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
            'DANH_SACH_SAN_PHAM' => $this->danhSachSanPham,
        ];
    }
}
