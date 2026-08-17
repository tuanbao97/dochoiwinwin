<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasStaffAccess()) {
            abort(403, 'Tài khoản người dùng không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}
