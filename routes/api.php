<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\CategoryNController;
use App\Http\Controllers\Api\CategoryPController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\DocumentStorageController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WardController;
use App\Http\Controllers\Auth\AuthController;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Support\Facades\Mail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->middleware([])->group(function() {
    /* Route::post('/register-user', [AuthController::class, 'registerUser']); */
    Route::post('/login-user', [AuthController::class, 'loginUser']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/reset-password', [AuthController::class, 'getUserByResetKey']);
    Route::post('/reset-password', [AuthController::class, 'resetPasswordByResetKey']);
});

Route::middleware(['api', 'custom-validate-oauth-token'])->group(function() {
    Route::post('/auth/logout-user', [AuthController::class, 'logoutUser']);
    Route::get('/auth/my-info', [AuthController::class, 'myInfo']);
    Route::post('/auth/my-info/update', [AuthController::class, 'updateMyInfo']);
    Route::post('/auth/my-info/update-password', [AuthController::class, 'updatePassword']);

    Route::middleware(['evict-cache-public-api'])->group(function() {
        Route::get('/user/list', [UserController::class, 'getListUser']);
        Route::put('/user/active/{ID}', [UserController::class, 'activeUser']);
        Route::delete('/user/delete/{ID}', [UserController::class, 'deleteUser']);
        Route::get('/user/{ID}', [UserController::class, 'getUserById']);
        Route::post('/user/save', [UserController::class, 'saveUser']);
        Route::post('/user/{ID}/save', [UserController::class, 'saveUser']);

        Route::get('/status-active/list', [AppController::class, 'getListTrangThaiHoatDong']);

        Route::get('/categoryp/detail/{ID}', [CategoryPController::class, 'getDetailDanhMucSanPham']);
        Route::post('/categoryp/save', [CategoryPController::class, 'save']);
        Route::delete('/categoryp/delete/{ID}', [CategoryPController::class, 'deleteDanhMucSanPham']);
        Route::get('/categoryp/list/tree', [CategoryPController::class, 'getListDanhMucSanPhamTree']);
        Route::get('/categoryp/list', [CategoryPController::class, 'getListDanhMucSanPham']);
        Route::put('/categoryp/active/{ID}', [CategoryPController::class, 'activeTrangThaiHoatDong']);

        Route::get('/categoryn/detail/{ID}', [CategoryNController::class, 'getDetailDanhMucTinTuc']);
        Route::post('/categoryn/save', [CategoryNController::class, 'save']);
        Route::delete('/categoryn/delete/{ID}', [CategoryNController::class, 'deleteDanhMucTinTuc']);
        Route::get('/categoryn/list/tree', [CategoryNController::class, 'getListDanhMucTinTucTree']);
        Route::get('/categoryn/list', [CategoryNController::class, 'getListDanhMucTinTuc']);
        Route::put('/categoryn/active/{ID}', [CategoryNController::class, 'activeTrangThaiHoatDong']);

        Route::get('/document-storages', [DocumentStorageController::class, 'getListFileDinhKem']);
        Route::get('/document-storages/download', [DocumentStorageController::class, 'downloadFileDinhKems']);
        Route::post('/document-storage/upload-multiples-file', [DocumentStorageController::class, 'uploadMutilple']);
        Route::post('/document-storage/file/update/{ID}', [DocumentStorageController::class, 'updateChiTietFileDinhKem']);
        Route::post('/document-storage/upload-multiples-hinh-anh', [DocumentStorageController::class, 'uploadMutilpleHinhAnh']);
        Route::post('/document-storage/hinh-anh/update/{ID}', [DocumentStorageController::class, 'updateChiTietHinhAnh']);
        Route::post('/document-storage/hinh-anh/crop/{ID}', [DocumentStorageController::class, 'cropHinhAnh']);
        Route::post('/document-storage/upload-multiples-video', [DocumentStorageController::class, 'uploadMutilpleVideo']);
        Route::post('/document-storage/hinh-anh-dai-dien-video/crop/{ID}', [DocumentStorageController::class, 'cropHinhAnhDaiDienVideo']);
        Route::post('/document-storage/video/update/{ID}', [DocumentStorageController::class, 'updateChiTietVideo']);

        Route::post("/cities/import-data", [ProvinceController::class, 'importDataCties']);
        Route::get("/province/list", [ProvinceController::class, 'getListTinhThanh']);
        Route::get("/district/list", [DistrictController::class, 'getListQuanHuyen']);
        Route::get("/ward/list", [WardController::class, 'getListPhuongXaThiTran']);

        Route::get('/product/list', [ProductController::class, 'getListSanPham']);
        Route::post('/product/save', [ProductController::class, 'saveSanPham']);
        Route::get('/product/detail/{ID}', [ProductController::class, 'getDetailSanPham']);
        Route::delete('/product/delete/{ID}', [ProductController::class, 'deleteSanPham']);
        Route::put('/product/active/{ID}', [ProductController::class, 'activeSanPham']);
        Route::patch('/product/sold/{ID}', [ProductController::class, 'soldSanPham']);

        Route::get('/news/list', [NewsController::class, 'getListTinTuc']);
        Route::post('/news/save', [NewsController::class, 'saveTinTuc']);
        Route::get('/news/detail/{ID}', [NewsController::class, 'getDetailTinTuc']);
        Route::delete('/news/delete/{ID}', [NewsController::class, 'deleteTinTuc']);
        Route::put('/news/active/{ID}', [NewsController::class, 'activeTinTuc']);

        Route::get('/video/list', [VideoController::class, 'getListVideo']);
        Route::post('/video/save', [VideoController::class, 'saveVideo']);
        Route::get('/video/detail/{ID}', [VideoController::class, 'getDetailVideo']);
        Route::delete('/video/delete/{ID}', [VideoController::class, 'deleteVideo']);
        Route::put('/video/active/{ID}', [VideoController::class, 'activeVideo']);

        Route::get('/transaction/list', [TransactionController::class, 'getListTransaction']);
        Route::get('/transaction/detail/{ID}', [TransactionController::class, 'getDetailTransaction']);
        Route::put('/transaction/status/{ID}', [TransactionController::class, 'updateTransactionStatus']);

        Route::get('/setting/detail/{CODE}', [SettingController::class, 'getDetailSetting']);
        Route::get('/setting/list', [SettingController::class, 'getListSetting']);
        Route::post('/setting/web/save', [SettingController::class, 'saveSettingWeb']);

        Route::get('/role/list', [RoleController::class, 'getListRole']);

        Route::post('/cache/evict', [AppController::class, 'evictCache']);
    });
});


Route::prefix('public')->middleware(['cache-public-api-response'])->group(function() {
    Route::get("/province/list", [ProvinceController::class, 'getListTinhThanh']);
    Route::get("/district/list", [DistrictController::class, 'getListQuanHuyen']);
    Route::get("/ward/list", [WardController::class, 'getListPhuongXaThiTran']);

    Route::get('/role/list', [RoleController::class, 'getListRole']);
    Route::get("/user/list", [UserController::class, 'getListUserPublic']);

    Route::get('/document-storages', [DocumentStorageController::class, 'getListFileDinhKem']);
    Route::get('/document-storages/download', [DocumentStorageController::class, 'downloadFileDinhKems']);

    Route::get('/categoryp/list/tree', [CategoryPController::class, 'getListDanhMucSanPhamTreePublic']);
    Route::get('/categoryp/list', [CategoryPController::class, 'getListDanhMucSanPhamPublic']);

    Route::get('/categoryn/list/tree', [CategoryNController::class, 'getPublicListDanhMucTinTucTree']);
    
    Route::get('/product/list', [ProductController::class, 'getListSanPhamPublic']);
    Route::get('/product/detail/{ID}', [ProductController::class, 'getDetailSanPhamPublic']);

    Route::get('/news/list', [NewsController::class, 'getListTinTucPublic']);
    Route::get('/news/detail/{ID}', [NewsController::class, 'getDetailTinTuc']);

    Route::get('/video/list', [VideoController::class, 'getListVideoPublic']);
    Route::get('/video/detail/{ID}', [VideoController::class, 'getDetailVideo']);

    Route::get('/setting/detail/{CODE}', [SettingController::class, 'getDetailSetting']);
    Route::get('/setting/list', [SettingController::class, 'getListSetting']);
    Route::get('/setting/banner-chinh', [SettingController::class, 'getDetailSettingBannerChinh']);
});

Route::prefix('public')->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
])->group(function () {
    Route::post('/transaction/place-order', [TransactionController::class, 'placeOrder']);
});




