<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Không lưu response API công khai. Cửa hàng luôn đọc dữ liệu mới.
 */
class PublicApiResponseCacheMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
