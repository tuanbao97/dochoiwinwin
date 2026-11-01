<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\AppService;
use Illuminate\Http\Request;

class AppController extends Controller
{
    // Inject bean
    private AppService $appService;

    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }

    public function getListTrangThaiHoatDong(Request $request) {
        return $this->appService->getListTrangThaiHoatDong($request);
    }

    public function evictCache(Request $request) {
        return $this->appService->evictCache($request);
    }

    public function syncSapoCatalog(Request $request) {
        return $this->appService->syncSapoCatalog($request);
    }

    public function getSapoSyncStatus(Request $request) {
        return $this->appService->getSapoSyncStatus($request);
    }

    public function getSapoAutoFetch(Request $request) {
        return $this->appService->getSapoAutoFetch($request);
    }

    public function saveSapoAutoFetch(Request $request) {
        return $this->appService->saveSapoAutoFetch($request);
    }
}
