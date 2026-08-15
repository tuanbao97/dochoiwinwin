<?php

namespace App\Http\Controllers;

use App\Enum\AppConstant;
use App\Service\CategoryNService;
use App\Service\NewsService;
use App\Service\ProductService;
use App\Service\VideoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ThemeStorefrontController extends Controller
{
    private const SESSION_KEY = 'theme_storefront_cart';

    public function __construct(
        private ProductService $productService,
        private NewsService $newsService,
        private CategoryNService $categoryNService,
        private VideoService $videoService,
    ) {}

    /**
     * Biến thể flash sale trên trang chủ (UI-FRONTEND/index) — dùng khi thêm giỏ không gửi kèm metadata.
     *
     * @var array<int, array{title: string, handle: string, price: int, image: string}>
     */
    private const VARIANT_CATALOG = [
        127890663 => [
            'title' => 'Bỉm Goldgi Eco Dán L56',
            'handle' => 'bim-goldgi-eco-dan-l56-9-14kg',
            'price' => 440000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
        127890654 => [
            'title' => 'Bỉm Goldgi+ X5 (Tã dán)',
            'handle' => 'bim-goldgi-x5-dan',
            'price' => 590000,
            'image' => 'thumb/large/100/531/894/products/d-l-56-845x1024-c24ae3e9aa1945b68465f24bd5cc4c66.png?v=1730192671777',
        ],
        127890639 => [
            'title' => 'Sữa Meiji nội địa Nhật Bản số 9, 800g (1 - 3 tuổi)',
            'handle' => 'sua-meiji-noi-dia-so-9-1-3-tuoi',
            'price' => 790000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
        127890638 => [
            'title' => 'Sữa Meiji nội địa Nhật Bản số 0, 800g (0 -  1 tuổi)',
            'handle' => 'sua-meiji-noi-dia-nhat-ban-so-0-800g-0-1-tuoi',
            'price' => 990000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
        127890635 => [
            'title' => 'Sữa Meiji nội địa Nhật Bản dạng thanh số 0, (0 - 1 tuổi)',
            'handle' => 'sua-meiji-noi-dia-nhat-ban-dang-thanh-so-0-0-1-tuoi',
            'price' => 48000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
        127890630 => [
            'title' => 'Sữa Meiji Kids Formula 900g (3 - 10 tuổi)',
            'handle' => 'sua-meiji-kids-formula-900g-3-10-tuoi',
            'price' => 1090000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
        127890622 => [
            'title' => 'Sữa Meiji nhập khẩu số 9 lon 800g (1-3 tuổi)',
            'handle' => 'sua-meiji-nhap-khau-so-1-800g-1-3-tuoi',
            'price' => 950000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
        127890619 => [
            'title' => 'Sữa Nan Nga số 1 Optipro (0-6 tháng)',
            'handle' => 'sua-nan-nga-so-1-optipro-0-6-thang',
            'price' => 980000,
            'image' => 'thumb/large/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777',
        ],
    ];

    public function cartAdd(Request $request): \Illuminate\Http\JsonResponse
    {
        $variantId = (int) ($request->input('variantId')
            ?? $request->input('VariantId')
            ?? $request->input('id'));
        $quantity = max(1, (int) $request->input('quantity', 1));

        if ($variantId <= 0) {
            return response()->json(['message' => 'Thiếu mã biến thể'], 422);
        }

        $meta = self::VARIANT_CATALOG[$variantId] ?? null;
        $title = $request->input('product_title', $meta['title'] ?? 'Sản phẩm');
        $handle = $request->input('product_handle', $meta['handle'] ?? '');
        $price = (int) $request->input('price', $meta['price'] ?? 0);
        $imageRel = $request->input('image', $meta['image'] ?? '');
        $categoryId = (int) ($request->input('category_id')
            ?? $request->input('product_category_id')
            ?? 0);
        if ($categoryId <= 0) {
            $categoryId = $this->resolveProductCategoryId($variantId);
        }

        $items = array_values($this->getCartLines());
        $existingIndex = null;
        foreach ($items as $k => $line) {
            if ((int) $line['variant_id'] === $variantId) {
                $existingIndex = $k;
                break;
            }
        }

        if ($existingIndex !== null) {
            $existing = $items[$existingIndex];
            unset($items[$existingIndex]);
            $items = array_values($items);

            $existing['quantity'] = (int) ($existing['quantity'] ?? 0) + $quantity;
            $existing['line_price'] = $existing['quantity'] * (int) ($existing['price'] ?? 0);
            if ($categoryId > 0) {
                $existing['category_id'] = $categoryId;
            }
            // Đưa dòng vừa thêm/cập nhật lên đầu danh sách
            array_unshift($items, $existing);
        } else {
            array_unshift($items, [
                'variant_id' => $variantId,
                'title' => $title,
                'variant_title' => $request->input('variant_title', 'Mặc định'),
                'quantity' => $quantity,
                'price' => $price,
                'line_price' => $price * $quantity,
                'image' => $imageRel,
                'handle' => $handle,
                'category_id' => $categoryId,
            ]);
        }

        session([self::SESSION_KEY => array_values($items)]);

        $detailHandle = trim((string) $handle);
        if ($detailHandle === '') {
            $detailHandle = 'sp-'.$variantId;
        } elseif (! preg_match('/-\d+$/', $detailHandle)) {
            $detailHandle .= '-'.$variantId;
        }
        $url = url('san-pham/chi-tiet/'.ltrim($detailHandle, '/'));

        $line = collect($items)->firstWhere('variant_id', $variantId);

        return response()->json([
            'variant_id' => $variantId,
            'title' => is_array($line) ? ($line['title'] ?? $title) : $title,
            'variant_title' => is_array($line) ? ($line['variant_title'] ?? 'Mặc định') : 'Mặc định',
            'url' => $url,
            'item_count' => $this->totalQuantity($items),
        ]);
    }

    public function cartChange(Request $request): Response
    {
        $line = max(1, (int) $request->query('line', 0));
        $quantity = (int) $request->query('quantity', 0);
        $items = array_values($this->getCartLines());

        $index = $line - 1;
        if (! isset($items[$index])) {
            return response('', 404);
        }

        if ($quantity <= 0) {
            array_splice($items, $index, 1);
        } else {
            $items[$index]['quantity'] = $quantity;
            $items[$index]['line_price'] = $quantity * (int) $items[$index]['price'];
        }

        session([self::SESSION_KEY => $items]);

        return response('', 200);
    }

    public function cartUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => true]);
    }

    public function cartClear(): \Illuminate\Http\JsonResponse
    {
        session()->forget(self::SESSION_KEY);

        return response()->json(['success' => true]);
    }

    public function cartPage(Request $request): BinaryFileResponse|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
    {
        if ($request->query('view') === 'data') {
            $items = $this->getCartLines();

            return response()->view('theme.cart-data', [
                'items' => $items,
                'totalQuantity' => $this->totalQuantity($items),
                'totalPrice' => $this->totalPrice($items),
                'appUrl' => rtrim(url('/'), '/'),
            ]);
        }

        return view('UI-FRONTEND.cart.index', [
            'productId' => 0,
        ]);
    }

    public function checkoutPage(): View
    {
        $items = $this->getCartLines();

        return view('UI-FRONTEND.thanh-toan.index', [
            'productId' => 0,
            'items' => $items,
            'totalQuantity' => $this->totalQuantity($items),
            'totalPrice' => $this->totalPrice($items),
            'appUrl' => rtrim(url('/'), '/'),
        ]);
    }

    public function cartRecommendations(Request $request): \Illuminate\Http\JsonResponse
    {
        $items = array_values($this->getCartLines());
        if ($items === []) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $cartProductIds = [];
        $categoryIds = [];
        $itemsChanged = false;

        foreach ($items as $index => $line) {
            $productId = (int) ($line['variant_id'] ?? 0);
            if ($productId > 0) {
                $cartProductIds[] = $productId;
            }

            $categoryId = (int) ($line['category_id'] ?? 0);
            if ($categoryId <= 0 && $productId > 0) {
                $categoryId = $this->resolveProductCategoryId($productId);
                if ($categoryId > 0) {
                    $items[$index]['category_id'] = $categoryId;
                    $itemsChanged = true;
                }
            }

            if ($categoryId > 0) {
                $categoryIds[] = $categoryId;
            }
        }

        if ($itemsChanged) {
            session([self::SESSION_KEY => $items]);
        }

        $categoryIds = array_values(array_unique($categoryIds));
        $cartProductIds = array_values(array_unique($cartProductIds));
        if ($categoryIds === []) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $limit = max(1, min(12, (int) $request->query('limit', 8)));
        $listRequest = Request::create('/', 'GET', [
            'PAGE' => 1,
            'PER_PAGE' => $limit,
            'BO_LOC' => 'moi-den-cu',
            'TRANG_THAI_HOAT_DONG' => true,
            'IS_API_PUBLIC' => true,
            'DANH_MUC_SAN_PHAM_ID' => $categoryIds,
            'NOT_IN_ID' => $cartProductIds,
        ]);

        $response = $this->productService->getListSanPham($listRequest);
        $body = json_decode($response->getContent(), true);
        $products = $body['DATAS']['PRODUCT']['DATA'] ?? [];

        if ($cartProductIds !== []) {
            $products = array_values(array_filter($products, static function (array $product) use ($cartProductIds): bool {
                return ! in_array((int) ($product['ID'] ?? 0), $cartProductIds, true);
            }));
        }

        return response()->json([
            'success' => true,
            'products' => array_slice($products, 0, $limit),
            'category_ids' => $categoryIds,
            'excluded_ids' => $cartProductIds,
        ]);
    }

    public function search(Request $request): View|Response|RedirectResponse
    {
        $view = (string) $request->query('view', '');
        if ($view === 'quick-search') {
            return $this->renderProductListing($request, $this->listingParamsFromQuery($request));
        }
        // Fragment Sapo theme (product / product-row / section / …) — chưa port sang Laravel
        if ($view !== '') {
            return response('', 200)->header('Content-Type', 'text/html; charset=UTF-8');
        }

        if ($this->hasLegacyListingQuery($request)) {
            $target = $this->legacyListingRedirectTarget($request, 'search');
            if ($target !== null) {
                return redirect($target, 301);
            }
        }

        // /search không còn dùng làm trang listing; redirect về tất cả SP hoặc giữ empty search UI
        $params = $this->listingParamsFromQuery($request);
        if ($params['query'] === '' && $params['categoryKey'] === '' && ! $params['productHot'] && ! $params['productVip']) {
            return redirect(storefrontListingUrl(['mode' => 'all']), 301);
        }

        return redirect($this->listingUrlFromParams($params), 301);
    }

    public function categoryListing(Request $request, string $categoryKey, ?string $filters = null): View|Response|RedirectResponse
    {
        $parsed = storefrontParseListingFilters($filters);
        $params = $this->listingParamsFromPath($parsed, [
            'mode' => 'category',
            'categoryKey' => $categoryKey,
            'categoryId' => (int) ($this->parseCategoryIdFromKey($categoryKey) ?? 0),
        ]);

        return $this->renderProductListing($request, $params);
    }

    public function keywordListing(Request $request, string $query, ?string $filters = null): View|Response|RedirectResponse
    {
        $parsed = storefrontParseListingFilters($filters);
        $params = $this->listingParamsFromPath($parsed, [
            'mode' => 'search',
            'query' => trim(rawurldecode($query)),
        ]);

        return $this->renderProductListing($request, $params);
    }

    public function vipListing(Request $request, ?string $filters = null): View|Response|RedirectResponse
    {
        $parsed = storefrontParseListingFilters($filters);
        $params = $this->listingParamsFromPath($parsed, [
            'mode' => 'vip',
            'productVip' => true,
        ]);

        return $this->renderProductListing($request, $params);
    }

    public function hotListing(Request $request, ?string $filters = null): View|Response|RedirectResponse
    {
        $parsed = storefrontParseListingFilters($filters);
        $params = $this->listingParamsFromPath($parsed, [
            'mode' => 'hot',
            'productHot' => true,
        ]);

        return $this->renderProductListing($request, $params);
    }

    public function allProducts(Request $request, ?string $filters = null): View|Response|RedirectResponse
    {
        if ($filters === null && $this->hasLegacyListingQuery($request)) {
            $target = $this->legacyListingRedirectTarget($request, 'all');
            if ($target !== null) {
                return redirect($target, 301);
            }
        }

        $parsed = storefrontParseListingFilters($filters);
        $params = $this->listingParamsFromPath($parsed, [
            'mode' => 'all',
            'listAll' => true,
        ]);

        return $this->renderProductListing($request, $params);
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function renderProductListing(Request $request, array $params): View|Response
    {
        $query = (string) ($params['query'] ?? '');
        $categoryId = (int) ($params['categoryId'] ?? 0);
        $categoryKey = (string) ($params['categoryKey'] ?? '');
        $page = max(1, (int) ($params['page'] ?? 1));
        $view = (string) $request->query('view', '');
        $perPage = $view === 'quick-search' ? 8 : 20;
        $boLoc = (string) ($params['boLoc'] ?? 'default');
        if (! in_array($boLoc, AppConstant::DANH_SACH_BO_LOC_TIM_KIEM, true)) {
            $boLoc = 'default';
        }

        $priceFilterOptions = $this->productPriceFilterOptions();
        $selectedPriceFilterIds = array_values(array_filter(
            (array) ($params['giaChips'] ?? []),
            static fn ($id): bool => is_string($id) && $id !== ''
        ));
        $validIds = array_column($priceFilterOptions, 'id');
        $selectedPriceFilterIds = array_values(array_filter(
            $selectedPriceFilterIds,
            static fn (string $id): bool => in_array($id, $validIds, true)
        ));
        $mucGia = $this->buildMucGiaFromFilterIds($selectedPriceFilterIds, $priceFilterOptions);

        $giaTuVal = array_key_exists('giaTu', $params) && $params['giaTu'] !== null && $params['giaTu'] !== ''
            ? (int) $params['giaTu']
            : null;
        $giaDenVal = array_key_exists('giaDen', $params) && $params['giaDen'] !== null && $params['giaDen'] !== ''
            ? (int) $params['giaDen']
            : null;
        // Chỉ dùng giaTu/giaDen khi không có chip mức giá (chip đã mang đúng < / >=<= / >)
        if ($selectedPriceFilterIds === [] && ($giaTuVal !== null || $giaDenVal !== null)) {
            $range = $this->buildMucGiaRangeFromGiaTuDen($giaTuVal, $giaDenVal);
            if ($range !== []) {
                $mucGia[] = $range;
            }
        }

        $productHot = ! empty($params['productHot']);
        $productVip = ! empty($params['productVip']);
        $listAll = ! empty($params['listAll']) || (($params['mode'] ?? '') === 'all');

        $result = $this->searchProducts(
            $query,
            $categoryId > 0 ? [$categoryId] : [],
            $page,
            $perPage,
            $boLoc,
            $mucGia,
            $listAll,
            $productHot,
            $productVip,
            false
        );
        $appUrl = rtrim(url('/'), '/');
        $categoryName = $categoryId > 0 ? $this->resolveCategoryName($categoryId) : '';
        if ($categoryKey === '' && $categoryId > 0) {
            $categoryKey = $this->buildProductCategoryKey($categoryId);
        }
        if ($productVip && $categoryName === '' && $query === '') {
            $categoryName = 'Chớp thời cơ. Giá như mơ!';
        } elseif ($productHot && $categoryName === '' && $query === '') {
            $categoryName = 'Sản phẩm nổi bật';
        }

        if ($view === 'quick-search') {
            return response()->view('UI-FRONTEND.tim-kiem.partials.quick-search-results', [
                'query' => $query,
                'products' => $result['products'],
                'total' => $result['total'],
                'appUrl' => $appUrl,
                'categoryId' => $categoryId,
                'categoryKey' => $categoryKey,
            ]);
        }

        $mode = (string) ($params['mode'] ?? '');
        if ($mode === '') {
            if ($listAll) {
                $mode = 'all';
            } elseif ($productVip && $categoryKey === '' && $query === '') {
                $mode = 'vip';
            } elseif ($productHot && $categoryKey === '' && $query === '') {
                $mode = 'hot';
            } elseif ($categoryKey !== '') {
                $mode = 'category';
            } elseif ($query !== '') {
                $mode = 'search';
            } else {
                $mode = 'all';
            }
        }

        $pageBasePath = match ($mode) {
            'vip' => '/san-pham-vip',
            'hot' => '/san-pham-noi-bat',
            'category' => '/danh-muc/' . ($categoryKey !== '' ? $categoryKey : 'danh-muc-0'),
            'search' => '/tim-kiem/' . rawurlencode($query !== '' ? $query : '-'),
            default => '/tat-ca-san-pham',
        };

        $viewData = [
            'query' => $query,
            'categoryId' => $categoryId,
            'categoryKey' => $categoryKey,
            'categoryName' => $listAll && $categoryName === '' ? 'Tất cả sản phẩm' : $categoryName,
            'boLoc' => $boLoc,
            'priceFilterOptions' => $priceFilterOptions,
            'selectedPriceFilterIds' => $selectedPriceFilterIds,
            'giaTu' => $giaTuVal,
            'giaDen' => $giaDenVal,
            'page' => $page,
            'products' => $result['products'],
            'total' => $result['total'],
            'perPage' => $perPage,
            'totalPages' => $result['totalPages'],
            'appUrl' => $appUrl,
            'productId' => 0,
            'listAll' => $listAll,
            'productHot' => $productHot,
            'productVip' => $productVip,
            'pageBasePath' => $pageBasePath,
            'listingMode' => $mode,
            'listingTopics' => $this->productListingTopics(
                $categoryId,
                $listAll && $query === '' && $categoryId <= 0 && ! $productHot && ! $productVip
            ),
        ];

        if ($listAll && $query === '' && $categoryId <= 0 && ! $productHot && ! $productVip) {
            return view('UI-FRONTEND.san-pham.tat-ca', $viewData);
        }

        return view('UI-FRONTEND.tim-kiem.ket-qua', $viewData);
    }

    /**
     * Dải chủ đề đầu trang danh sách: ưu tiên danh mục con của danh mục đang xem,
     * nếu danh mục đó không có con thì hiển thị các danh mục cùng cấp.
     *
     * @return array<int, array{id: int, name: string, url: string, icon: string, active: bool}>
     */
    private function productListingTopics(int $categoryId, bool $isGlobalAll = false): array
    {
        try {
            $categories = DB::table('category_p')
                ->where('STATUS', AppConstant::STATUS_USING)
                ->where('IS_ACTIVE', true)
                ->orderBy('SORT_ORDER')
                ->orderBy('ID')
                ->get(['ID', 'NAME', 'PARENT_ID']);
        } catch (Throwable) {
            return [];
        }

        $current = $categoryId > 0
            ? $categories->firstWhere('ID', $categoryId)
            : null;

        $parentId = null;
        $scopeParent = null;
        if ($current !== null) {
            $hasChildren = $categories->contains(fn ($c) => (int) ($c->PARENT_ID ?? 0) === (int) $current->ID);
            $parentId = $hasChildren ? (int) $current->ID : ($current->PARENT_ID !== null ? (int) $current->PARENT_ID : null);

            if ($hasChildren) {
                $scopeParent = $current;
            } elseif ($current->PARENT_ID !== null) {
                $scopeParent = $categories->firstWhere('ID', (int) $current->PARENT_ID);
            }
        }

        // Trong nhóm cha–con, "Tất cả" là danh mục cha.
        // Ở cấp root, "Tất cả" là toàn bộ catalog.
        $topics = [[
            'id' => $scopeParent !== null ? (int) $scopeParent->ID : 0,
            'name' => 'Tất cả',
            'url' => $scopeParent !== null
                ? storefrontProductCategoryUrl((int) $scopeParent->ID, (string) $scopeParent->NAME)
                : url('/tat-ca-san-pham'),
            'icon' => asset('UI-FRONTEND/assets/ww-menu-icons/chu-de-tat-ca.svg'),
            'active' => $scopeParent !== null
                ? $categoryId === (int) $scopeParent->ID
                : $isGlobalAll,
        ]];

        foreach ($categories as $category) {
            $itemParent = $category->PARENT_ID !== null ? (int) $category->PARENT_ID : null;
            if ($itemParent !== $parentId) {
                continue;
            }

            $name = (string) $category->NAME;
            $topics[] = [
                'id' => (int) $category->ID,
                'name' => $name,
                'url' => storefrontProductCategoryUrl((int) $category->ID, $name),
                'icon' => storefrontCategoryIconUrl($name),
                'active' => (int) $category->ID === $categoryId,
            ];
        }

        return count($topics) > 1 ? $topics : [];
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function listingParamsFromPath(array $parsed, array $overrides = []): array
    {
        return array_merge([
            'mode' => '',
            'query' => '',
            'categoryId' => 0,
            'categoryKey' => '',
            'boLoc' => $parsed['boLoc'] ?? 'default',
            'giaTu' => $parsed['giaTu'] ?? null,
            'giaDen' => $parsed['giaDen'] ?? null,
            'giaChips' => $parsed['giaChips'] ?? [],
            'page' => $parsed['page'] ?? 1,
            'productHot' => ! empty($parsed['productHot']),
            'productVip' => ! empty($parsed['productVip']),
            'listAll' => false,
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private function listingParamsFromQuery(Request $request): array
    {
        $query = trim((string) ($request->query('query', $request->query('q', ''))));
        $categoryId = $this->parseProductCategoryId($request);
        $categoryKey = '';
        $danhMuc = $request->query('danh-muc');
        if (is_string($danhMuc) && $danhMuc !== '') {
            $categoryKey = $danhMuc;
        } elseif ($categoryId > 0) {
            $categoryKey = $this->buildProductCategoryKey($categoryId);
        }

        $boLoc = (string) $request->query('bo_loc', 'default');
        $giaTu = $request->query('gia_tu');
        $giaDen = $request->query('gia_den');

        return [
            'mode' => '',
            'query' => $query,
            'categoryId' => $categoryId,
            'categoryKey' => $categoryKey,
            'boLoc' => $boLoc,
            'giaTu' => is_numeric($giaTu) ? (int) $giaTu : null,
            'giaDen' => is_numeric($giaDen) ? (int) $giaDen : null,
            'giaChips' => $this->parseSelectedPriceFilterIds($request),
            'page' => max(1, (int) $request->query('PAGE', 1)),
            'productHot' => filter_var($request->query('PRODUCT_HOT', false), FILTER_VALIDATE_BOOLEAN),
            'productVip' => filter_var($request->query('PRODUCT_VIP', false), FILTER_VALIDATE_BOOLEAN),
            'listAll' => false,
        ];
    }

    private function hasLegacyListingQuery(Request $request): bool
    {
        foreach (['query', 'q', 'danh-muc', 'category_id', 'bo_loc', 'gia', 'gia_tu', 'gia_den', 'PAGE', 'PRODUCT_HOT', 'PRODUCT_VIP', 'type'] as $key) {
            if ($request->query->has($key)) {
                return true;
            }
        }

        return false;
    }

    private function legacyListingRedirectTarget(Request $request, string $context): ?string
    {
        $params = $this->listingParamsFromQuery($request);
        if ($context === 'all') {
            $params['mode'] = 'all';
            $params['listAll'] = true;
        }

        return $this->listingUrlFromParams($params);
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function listingUrlFromParams(array $params): string
    {
        $opts = [
            'mode' => (string) ($params['mode'] ?? ''),
            'categoryKey' => (string) ($params['categoryKey'] ?? ''),
            'query' => (string) ($params['query'] ?? ''),
            'boLoc' => (string) ($params['boLoc'] ?? 'default'),
            'giaTu' => $params['giaTu'] ?? null,
            'giaDen' => $params['giaDen'] ?? null,
            'giaChips' => (array) ($params['giaChips'] ?? []),
            'page' => max(1, (int) ($params['page'] ?? 1)),
            'productHot' => ! empty($params['productHot']),
            'productVip' => ! empty($params['productVip']),
        ];

        if ($opts['mode'] === '' && ! empty($params['listAll'])) {
            $opts['mode'] = 'all';
        }

        return storefrontListingUrl($opts);
    }

    /**
     * @param  array<int, int>  $categoryIds
     * @param  array<int, array{MIN_VALUE?: int|float|null, MAX_VALUE?: int|float|null}>  $mucGia
     * @return array{products: array<int, array<string, mixed>>, total: int, totalPages: int}
     */
    private function searchProducts(
        string $query,
        array $categoryIds,
        int $page,
        int $perPage,
        string $boLoc = 'default',
        array $mucGia = [],
        bool $listAll = false,
        bool $productHot = false,
        bool $productVip = false,
        bool $getAll = false
    ): array {
        // Cho phép xem danh sách theo danh mục / tất cả / flash sale mà không cần từ khóa
        if ($query === '' && $categoryIds === [] && ! $listAll && ! $productHot && ! $productVip) {
            return ['products' => [], 'total' => 0, 'totalPages' => 0];
        }

        $params = [
            'PAGE' => $getAll ? 1 : $page,
            'PER_PAGE' => $getAll ? 9999 : $perPage,
            'BO_LOC' => $boLoc,
            'TRANG_THAI_HOAT_DONG' => true,
            'IS_API_PUBLIC' => true,
        ];
        if ($getAll) {
            $params['IS_GET_ALL_ELEMENTS'] = true;
        }
        if ($query !== '') {
            $params['TU_KHOA'] = $query;
        }
        if ($categoryIds !== []) {
            $params['DANH_MUC_SAN_PHAM_ID'] = $categoryIds;
        }
        if ($mucGia !== []) {
            $params['MUC_GIA'] = $mucGia;
        }
        if ($productHot) {
            $params['PRODUCT_HOT'] = true;
        }
        if ($productVip) {
            $params['PRODUCT_VIP'] = true;
        }

        $listRequest = Request::create('/', 'GET', $params);
        $response = $this->productService->getListSanPham($listRequest);
        $body = json_decode($response->getContent(), true);
        $pagination = $body['DATAS']['PRODUCT'] ?? [];

        return [
            'products' => $pagination['DATA'] ?? [],
            'total' => (int) ($pagination['TOTAL_ITEM'] ?? 0),
            'totalPages' => $getAll ? 1 : (int) ($pagination['TOTAL_PAGE'] ?? 0),
        ];
    }

    private function resolveCategoryName(int $categoryId): string
    {
        try {
            $category = \App\Models\CategoryP::query()
                ->where('ID', $categoryId)
                ->where('STATUS', 'USING')
                ->first();

            return (string) ($category->NAME ?? '');
        } catch (Throwable) {
            return '';
        }
    }

    public function productDetail(Request $request, string $productKey): View|Response
    {
        $parts = explode('-', $productKey);
        $productId = (int) end($parts);

        if ($productId <= 0) {
            abort(404);
        }

        $slug = count($parts) > 1
            ? implode('-', array_slice($parts, 0, -1))
            : $productKey;

        if ($request->query('view') === 'quickview') {
            return $this->productQuickViewFragment($productId, $slug);
        }

        $seo = $this->resolveProductShareSeo($productId);

        return view('UI-FRONTEND.san-pham.chi-tiet', [
            'productId' => $productId,
            'productSlug' => $slug,
            'productKey' => $productKey,
            'seoTitle' => $seo['title'],
            'seoDescription' => $seo['description'],
            'seoImage' => $seo['image'],
            'seoType' => 'product',
            'seoUrl' => url()->current(),
        ]);
    }

    public function newsList(Request $request, ?string $categoryKey = null, ?int $page = null): View|RedirectResponse
    {
        if ($categoryKey === null && $page === null && ($request->query->has('danh-muc') || $request->query->has('PAGE'))) {
            $legacyCategory = $request->query('danh-muc');
            $legacyPage = max(1, (int) $request->query('PAGE', 1));
            $key = is_string($legacyCategory) && $legacyCategory !== '' ? $legacyCategory : null;

            return redirect(storefrontNewsListUrl($legacyPage, $key), 301);
        }

        $resolvedPage = max(1, $page ?? 1);
        $categoryId = $categoryKey ? $this->parseCategoryIdFromKey($categoryKey) : null;
        $perPage = 12;

        $result = $this->fetchNewsList($resolvedPage, $perPage, $categoryId);
        $hotNews = $this->fetchNewsList(1, 5, null, true);

        return view('UI-FRONTEND.tin-tuc.index', [
            'productId' => 0,
            'newsList' => $result['items'],
            'total' => $result['total'],
            'totalPages' => $result['totalPages'],
            'page' => $resolvedPage,
            'perPage' => $perPage,
            'categoryId' => $categoryId,
            'categoryKey' => $categoryKey,
            'hotNews' => $hotNews['items'],
            'categories' => $this->fetchNewsCategoryTree(),
            'appUrl' => rtrim(url('/'), '/'),
        ]);
    }

    public function videoList(Request $request, ?int $page = null): View|RedirectResponse
    {
        if ($page === null && $request->query->has('PAGE')) {
            return redirect(storefrontVideoListUrl(max(1, (int) $request->query('PAGE', 1))), 301);
        }

        $resolvedPage = max(1, $page ?? 1);
        $perPage = 12;
        $result = $this->fetchVideoList($resolvedPage, $perPage);

        return view('UI-FRONTEND.video.index', [
            'productId' => 0,
            'videoList' => $result['items'],
            'total' => $result['total'],
            'totalPages' => $result['totalPages'],
            'page' => $resolvedPage,
            'perPage' => $perPage,
            'appUrl' => rtrim(url('/'), '/'),
        ]);
    }


    public function newsDetail(Request $request, string $newsKey): View
    {
        $parts = explode('-', $newsKey);
        $newsId = (int) end($parts);

        if ($newsId <= 0) {
            abort(404);
        }

        try {
            $response = $this->newsService->getDetailTinTuc($newsId, Request::create('/', 'GET'));
            $body = json_decode($response->getContent(), true);
            $news = $body['DATAS']['NEWS'] ?? null;
        } catch (Throwable) {
            abort(404);
        }

        if (! is_array($news) || empty($news['ID'])) {
            abort(404);
        }

        $categoryId = (int) ($news['DANH_MUC_TIN_TUC']['ID'] ?? 0);
        $relatedNews = [];

        if ($categoryId > 0) {
            $relatedResult = $this->fetchNewsList(1, 7, $categoryId, null, [$newsId]);
            $relatedNews = array_values(array_filter(
                $relatedResult['items'],
                static fn (array $item): bool => (int) ($item['ID'] ?? 0) !== $newsId
            ));
            $relatedNews = array_slice($relatedNews, 0, 6);
        }

        return view('UI-FRONTEND.tin-tuc.chi-tiet', [
            'productId' => 0,
            'news' => $news,
            'newsId' => $newsId,
            'relatedNews' => $relatedNews,
            'categories' => $this->fetchNewsCategoryTree(),
            'hotNews' => $this->fetchNewsList(1, 5, null, true)['items'],
            'appUrl' => rtrim(url('/'), '/'),
        ]);
    }

    /**
     * Meta share Facebook cho chi tiết sản phẩm (server-side — crawler không chạy JS).
     *
     * @return array{title: string, description: string, image: string}
     */
    private function resolveProductShareSeo(int $productId): array
    {
        $fallback = storefrontSeo(['type' => 'product']);

        try {
            $response = $this->productService->getDetailSanPham($productId, Request::create('/', 'GET', [
                'IS_API_PUBLIC' => true,
            ]));
            $body = json_decode($response->getContent(), true);
            $product = $body['DATAS']['PRODUCT'] ?? null;
        } catch (Throwable) {
            return [
                'title' => $fallback['title'],
                'description' => $fallback['description'],
                'image' => $fallback['image'],
            ];
        }

        if (! is_array($product) || empty($product['ID'])) {
            return [
                'title' => $fallback['title'],
                'description' => $fallback['description'],
                'image' => $fallback['image'],
            ];
        }

        $name = trim((string) ($product['TEN_SAN_PHAM'] ?? ''));
        $title = ($name !== '' ? $name : 'Sản phẩm') . ' — Win Win';

        $description = trim((string) ($product['MO_TA_CHI_TIET_ONLY_TEXT'] ?? ''));
        if ($description === '') {
            $description = trim(strip_tags((string) ($product['MO_TA_CHI_TIET'] ?? '')));
        }
        if ($description === '') {
            $description = $name !== ''
                ? ($name . ' tại ' . $fallback['siteName'])
                : $fallback['description'];
        }

        $image = '';
        $upd = $product['UPD_DT'] ?? null;
        foreach (['DANH_SACH_HINH_ANH_DAI_DIEN', 'DANH_SACH_HINH_ANH'] as $key) {
            $list = $product[$key] ?? [];
            if (! is_array($list) || $list === []) {
                continue;
            }
            $first = $list[0] ?? null;
            if (is_array($first)) {
                $image = storefrontMediaImageUrl($first, $upd, true);
            }
            if ($image !== '') {
                break;
            }
        }

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image !== '' ? $image : $fallback['image'],
        ];
    }

    private function productQuickViewFragment(int $productId, string $slug): Response
    {
        try {
            $response = $this->productService->getDetailSanPham($productId, Request::create('/', 'GET', [
                'IS_API_PUBLIC' => true,
            ]));
            $body = json_decode($response->getContent(), true);
            $product = $body['DATAS']['PRODUCT'] ?? null;
        } catch (Throwable) {
            abort(404);
        }

        if (!$product || !is_array($product)) {
            abort(404);
        }

        return response()
            ->view('UI-FRONTEND.san-pham.partials.quick-view-fragment', [
                'product' => $product,
                'productId' => $productId,
                'productSlug' => $slug,
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function productOrQuickview(Request $request, string $themeProductSlug): View|Response
    {
        $file = public_path('UI-FRONTEND/'.$themeProductSlug.'.html');

        if (File::exists($file)) {
            if ($request->query('view') === 'quickview') {
                return response(File::get($file), 200)->header('Content-Type', 'text/html; charset=UTF-8');
            }

            return response()->file($file);
        }

        if (preg_match('/-(\d+)$/', $themeProductSlug, $matches)) {
            $productId = (int) $matches[1];
            if ($productId > 0) {
                return $this->productDetail($request, $themeProductSlug);
            }
        }

        abort(404);
    }

    private function parseCategoryIdFromKey(mixed $key): ?int
    {
        if (! is_string($key) || $key === '') {
            return null;
        }

        if (! preg_match('/-(\d+)$/', $key, $matches)) {
            return null;
        }

        $id = (int) $matches[1];

        return $id > 0 ? $id : null;
    }

    private function parseProductCategoryId(Request $request): int
    {
        $danhMuc = $request->query('danh-muc');
        if (is_string($danhMuc) && $danhMuc !== '') {
            return (int) ($this->parseCategoryIdFromKey($danhMuc) ?? 0);
        }

        return (int) $request->query('category_id', 0);
    }

    /**
     * @return array<int, array{id: string, label: string, min: int|null, max: int|null, minInclusive: bool, maxInclusive: bool}>
     */
    private function productPriceFilterOptions(): array
    {
        return [
            // dưới 500k → PRICE < 500000
            ['id' => 'duoi-500k', 'label' => 'Dưới 500k', 'min' => null, 'max' => 500000, 'minInclusive' => true, 'maxInclusive' => false],
            // từ 500k đến 700k → PRICE >= 500000 và <= 700000
            ['id' => '500-700k', 'label' => 'Từ 500k đến 700k', 'min' => 500000, 'max' => 700000, 'minInclusive' => true, 'maxInclusive' => true],
            // trên 700k → PRICE > 700000
            ['id' => 'tren-700k', 'label' => 'Trên 700k', 'min' => 700000, 'max' => null, 'minInclusive' => false, 'maxInclusive' => true],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function parseSelectedPriceFilterIds(Request $request): array
    {
        $gia = $request->query('gia', '');
        if (! is_string($gia) || $gia === '') {
            return [];
        }

        $validIds = array_column($this->productPriceFilterOptions(), 'id');

        return array_values(array_filter(
            array_map('trim', explode(',', $gia)),
            static fn (string $id): bool => $id !== '' && in_array($id, $validIds, true)
        ));
    }

    /**
     * @param  array<int, string>  $selectedIds
     * @param  array<int, array{id: string, label: string, min: int|null, max: int|null, minInclusive?: bool, maxInclusive?: bool}>  $options
     * @return array<int, array{MIN_VALUE?: int, MAX_VALUE?: int, MIN_INCLUSIVE?: bool, MAX_INCLUSIVE?: bool}>
     */
    private function buildMucGiaFromFilterIds(array $selectedIds, array $options): array
    {
        if ($selectedIds === []) {
            return [];
        }

        $optionMap = [];
        foreach ($options as $option) {
            $optionMap[$option['id']] = $option;
        }

        $mucGia = [];
        foreach ($selectedIds as $id) {
            $option = $optionMap[$id] ?? null;
            if (! $option) {
                continue;
            }

            $range = [];
            if ($option['min'] !== null) {
                $range['MIN_VALUE'] = (int) $option['min'];
                $range['MIN_INCLUSIVE'] = array_key_exists('minInclusive', $option)
                    ? (bool) $option['minInclusive']
                    : true;
            }
            if ($option['max'] !== null) {
                $range['MAX_VALUE'] = (int) $option['max'];
                $range['MAX_INCLUSIVE'] = array_key_exists('maxInclusive', $option)
                    ? (bool) $option['maxInclusive']
                    : true;
            }
            if ($range !== []) {
                $mucGia[] = $range;
            }
        }

        return $mucGia;
    }

    /**
     * Map URL gia/{tu}-{den} sang khoảng giá.
     * Nhận diện 3 mức Giỏ trái cây để áp đúng toán tử; còn lại mặc định >= và <=.
     *
     * @return array{MIN_VALUE?: int, MAX_VALUE?: int, MIN_INCLUSIVE?: bool, MAX_INCLUSIVE?: bool}
     */
    private function buildMucGiaRangeFromGiaTuDen(?int $giaTuVal, ?int $giaDenVal): array
    {
        // dưới 500k: gia/0-500000 → PRICE < 500000
        if ($giaTuVal === 0 && $giaDenVal === 500000) {
            return [
                'MAX_VALUE' => 500000,
                'MAX_INCLUSIVE' => false,
            ];
        }

        // từ 500k đến 700k: gia/500000-700000 → >= và <=
        if ($giaTuVal === 500000 && $giaDenVal === 700000) {
            return [
                'MIN_VALUE' => 500000,
                'MAX_VALUE' => 700000,
                'MIN_INCLUSIVE' => true,
                'MAX_INCLUSIVE' => true,
            ];
        }

        // trên 700k: gia/700000-up → PRICE > 700000
        if ($giaTuVal === 700000 && $giaDenVal === null) {
            return [
                'MIN_VALUE' => 700000,
                'MIN_INCLUSIVE' => false,
            ];
        }

        $range = [];
        if ($giaTuVal !== null && $giaTuVal >= 0) {
            $range['MIN_VALUE'] = $giaTuVal;
            $range['MIN_INCLUSIVE'] = true;
        }
        if ($giaDenVal !== null && $giaDenVal > 0) {
            $range['MAX_VALUE'] = $giaDenVal;
            $range['MAX_INCLUSIVE'] = true;
        }

        return $range;
    }

    private function buildProductCategoryKey(int $categoryId): string
    {
        if ($categoryId <= 0) {
            return '';
        }

        try {
            $category = \App\Models\CategoryP::query()
                ->where('ID', $categoryId)
                ->where('STATUS', 'USING')
                ->first();

            if (! $category) {
                return 'danh-muc-' . $categoryId;
            }

            return convertStrToSlug((string) $category->NAME) . '-' . $categoryId;
        } catch (Throwable) {
            return 'danh-muc-' . $categoryId;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchNewsCategoryTree(): array
    {
        try {
            $response = $this->categoryNService->getListDanhMucTinTucTree(Request::create('/', 'GET', [
                'IS_GET_ALL_ELEMENTS' => true,
                'IS_API_PUBLIC' => true,
            ]));
            $body = json_decode($response->getContent(), true);

            return $body['DATAS']['CATEGORY_N']['DATA'] ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @param  array<int, int>|null  $notInIds
     * @return array{items: array<int, array<string, mixed>>, total: int, totalPages: int}
     */
    private function fetchNewsList(
        int $page,
        int $perPage,
        ?int $categoryId = null,
        ?bool $hotOnly = null,
        ?array $notInIds = null,
    ): array {
        $params = [
            'PAGE' => $page,
            'PER_PAGE' => $perPage,
            'BO_LOC' => 'moi-den-cu',
            'TRANG_THAI_HOAT_DONG' => true,
            'IS_API_PUBLIC' => true,
        ];

        if ($categoryId !== null && $categoryId > 0) {
            $params['DANH_MUC_TIN_TUC_ID'] = [$categoryId];
        }

        if ($hotOnly === true) {
            $params['LOAI_TIN_TUC'] = true;
        }

        if ($notInIds !== null && $notInIds !== []) {
            $params['NOT_IN_ID'] = $notInIds;
        }

        try {
            $response = $this->newsService->getListTinTuc(Request::create('/', 'GET', $params));
            $body = json_decode($response->getContent(), true);
            $pagination = $body['DATAS']['NEWS'] ?? [];
        } catch (Throwable) {
            return ['items' => [], 'total' => 0, 'totalPages' => 0];
        }

        return [
            'items' => $pagination['DATA'] ?? [],
            'total' => (int) ($pagination['TOTAL_ITEM'] ?? 0),
            'totalPages' => (int) ($pagination['TOTAL_PAGE'] ?? 0),
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, totalPages: int}
     */
    private function fetchVideoList(int $page, int $perPage): array
    {
        $params = [
            'PAGE' => $page,
            'PER_PAGE' => $perPage,
            'BO_LOC' => 'moi-den-cu',
            'TRANG_THAI_HOAT_DONG' => true,
            'IS_API_PUBLIC' => true,
        ];

        try {
            $response = $this->videoService->getListVideo(Request::create('/', 'GET', $params));
            $body = json_decode($response->getContent(), true);
            $pagination = $body['DATAS']['VIDEO'] ?? [];
        } catch (Throwable) {
            return ['items' => [], 'total' => 0, 'totalPages' => 0];
        }

        return [
            'items' => $pagination['DATA'] ?? [],
            'total' => (int) ($pagination['TOTAL_ITEM'] ?? 0),
            'totalPages' => (int) ($pagination['TOTAL_PAGE'] ?? 0),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getCartLines(): array
    {
        return session(self::SESSION_KEY, []) ?: [];
    }

    private function resolveProductCategoryId(int $productId): int
    {
        if ($productId <= 0) {
            return 0;
        }

        try {
            $response = $this->productService->getDetailSanPham($productId, Request::create('/', 'GET', [
                'IS_API_PUBLIC' => true,
            ]));
            $body = json_decode($response->getContent(), true);
            $category = $body['DATAS']['PRODUCT']['DANH_MUC_SAN_PHAM'] ?? null;
            return (int) ($category['ID'] ?? 0);
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function totalQuantity(array $items): int
    {
        $n = 0;
        foreach ($items as $line) {
            $n += (int) ($line['quantity'] ?? 0);
        }

        return $n;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function totalPrice(array $items): int
    {
        $n = 0;
        foreach ($items as $line) {
            $n += (int) ($line['line_price'] ?? 0);
        }

        return $n;
    }
}
