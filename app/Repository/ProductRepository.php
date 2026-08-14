<?php

namespace App\Repository;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepository extends RepositoryInterface
{
    public function getDetailSanPhamWithFetchEdger(int $id) : ?Product;

    public function getDetailSanPham(int $id) : Product;

    public function getDetailBasicSanPham(int $id) : ?Product;

    public function getListSanPham(?string $tuKhoa, ?array $arrDanhMucSanPhamId, ?bool $trangThaiHoatDong
        , ?string $boLoc
        , ?array $arrNotInId
        , ?bool $productHot
        , ?string $trangThai
        , Request $request
        , ?bool $isApiPublic
        , int $page, int $perPage
        , ?bool $productVip = null) : LengthAwarePaginator;

         public function getListSanPhamBackup(?string $tuKhoa, ?array $arrDanhMucSanPhamId, ?bool $trangThaiHoatDong
        , ?string $tinhThanhPho, ?string $quanHuyen, ?string $phuongXaThiTran
        , ?string $boLoc, ?string $loai
        , ?array $arrNotInId
        , ?bool $productHot
        , Request $request
        , bool $isApiPublic
        , int $page, int $perPage) : LengthAwarePaginator;
}
