<?php

namespace App\Http\Middleware;

use App\Support\StorefrontIdentity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chia sẻ thông tin đăng nhập cho mọi view web để header, trang tài khoản và
 * trang thanh toán render sẵn dữ liệu, không phải chờ JS gọi API.
 */
class ShareStorefrontIdentityMiddleware
{
    public function __construct(private readonly StorefrontIdentity $identity) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ nạp khi thực sự render HTML. Endpoint JSON (giỏ hàng, session)
        // không phải trả thêm truy vấn tài khoản.
        View::composer('*', function ($view): void {
            if (array_key_exists('storefrontUser', $view->getData())) {
                return;
            }

            $view->with('storefrontUser', $this->identity->payload());
        });

        return $next($request);
    }
}
