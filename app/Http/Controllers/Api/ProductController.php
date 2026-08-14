<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\product\ProductActiveRequest;
use App\Http\Requests\product\ProductDeleteRequest;
use App\Http\Requests\product\ProductDetailRequest;
use App\Http\Requests\product\ProductListRequest;
use App\Http\Requests\product\ProductSaveRequest;
use App\Http\Requests\product\ProductSoldRequest;
use App\Service\ProductService;

class ProductController extends Controller
{
    // Inject beans
    private ProductService $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    /**
     * Lưu sản phẩm
     * @param $request
     * @return mixed
     */
    public function saveSanPham(ProductSaveRequest $request) {
        return $this->productService->saveSanPham($request);
    }

    public function getDetailSanPham($ID, ProductDetailRequest $request) {
        return $this->productService->getDetailSanPham($ID, $request);
    }

    public function deleteSanPham($ID, ProductDeleteRequest $request) {
        return $this->productService->deleteSanPham($ID, $request);
    }

    public function getListSanPham(ProductListRequest $request) {
        return $this->productService->getListSanPham($request);
    }

    public function getListSanPhamPublic(ProductListRequest $request) {
        $request->merge([
            'IS_API_PUBLIC' => true
        ]);
        return $this->productService->getListSanPham($request);
    }

    public function getDetailSanPhamPublic($ID, ProductDetailRequest $request) {
        $request->merge([
            'IS_API_PUBLIC' => true
        ]);
        return $this->productService->getDetailSanPham($ID, $request);
    }

    public function activeSanPham($ID, ProductActiveRequest $request): mixed {
        return $this->productService->activeSanPham($ID, $request);
    }

    public function soldSanPham($ID, ProductSoldRequest $request): mixed {
        return $this->productService->soldSanPham($ID, $request);
    }

}

