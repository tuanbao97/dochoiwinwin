<?php

namespace App\Http\Middleware;

use App\Enum\AppConstant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicApiResponseCacheMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // TODO: tạm tắt cache response UI. Bỏ comment khối bên dưới khi muốn bật lại.
        return $next($request);

        /*
        $route = $request->route();
        $routePrefix = $route ? $route->getPrefix() : null;

        // Chỉ cache GET và prefix phải là api/public
        if ($request->method() !== 'GET' || $routePrefix !== AppConstant::PREFIX_API['API_PUBLIC']) {
            return $next($request);
        }

        // Lấy query string, loại bỏ tham số cache-bust
        $query = $request->query();
        unset($query['_'], $query['_ts'], $query['nocache'], $query['r']);
        ksort($query);
        $rawKey = $request->path() . '?' . http_build_query($query);
        $cacheKey = AppConstant::CACHE_PREFIX_DATA_UI_FRONTEND . md5($rawKey);

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            $data = $response->getContent();
            $decoded = json_decode($data, true);

            // Không cache list rỗng — tránh giữ section trang chủ bị ẩn sau khi vừa thêm SP
            if ($this->shouldSkipCache($decoded)) {
                return $response;
            }

            Cache::put($cacheKey, $decoded, now()->addHours(24));

            $tagKeyList = Cache::get(AppConstant::CACHE_TAG_DATA_UI_FRONTEND, []);
            if (!is_array($tagKeyList)) {
                $tagKeyList = [];
            }
            if (!in_array($cacheKey, $tagKeyList, true)) {
                $tagKeyList[] = $cacheKey;
                Cache::put(AppConstant::CACHE_TAG_DATA_UI_FRONTEND, $tagKeyList, now()->addHours(24));
            }
        }

        return $response;
        */
    }

    /**
     * Bỏ qua cache khi list sản phẩm/danh mục trả về 0 phần tử.
     */
    private function shouldSkipCache(?array $decoded): bool
    {
        if (!is_array($decoded) || empty($decoded['STATUS'])) {
            return true;
        }

        $datas = $decoded['DATAS'] ?? null;
        if (!is_array($datas)) {
            return false;
        }

        foreach (['PRODUCT', 'CATEGORY_P', 'CATEGORY_N', 'NEWS', 'VIDEO'] as $resource) {
            if (!isset($datas[$resource]) || !is_array($datas[$resource])) {
                continue;
            }
            $block = $datas[$resource];
            if (array_key_exists('TOTAL_ITEM', $block) && (int) $block['TOTAL_ITEM'] === 0) {
                return true;
            }
            if (array_key_exists('DATA', $block) && is_array($block['DATA']) && count($block['DATA']) === 0) {
                return true;
            }
        }

        return false;
    }
}
