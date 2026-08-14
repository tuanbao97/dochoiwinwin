<?php

namespace App\Service;

use Illuminate\Http\Request;

interface AppService
{
    /**
     * Get thư mục upload theo ngày tháng năm hiện tại
     * @return string
     */
    public function getCurrDirectory() : string;

    public function removeDauChamCau($string) : string;

    public function removeTiengViet($string) : string;

    public function removeDauCauVaTiengViet($string) : string;

    public function buildUriFromText($string) : string;

    public function getListTrangThaiHoatDong(Request $request);

    public function evictCache(Request $request);

    public function syncSapoCatalog(Request $request);

}