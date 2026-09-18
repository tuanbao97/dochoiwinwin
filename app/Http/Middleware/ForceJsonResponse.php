<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accept = strtolower((string) $request->header('Accept', ''));
        $forceJson = $request->is('api/*')
            || str_contains($accept, 'application/json');

        // Không ép Accept: JSON lên HTML (giỏ /cart?view=data, trang cửa hàng).
        if (! $forceJson) {
            return $next($request);
        }

        $request->headers->set('Accept', 'application/json');

        // Chặn notice/warning PHP (XAMPP display_errors) không lọt vào body JSON.
        $prev = ini_get('display_errors');
        ini_set('display_errors', '0');
        ob_start();
        try {
            $response = $next($request);
        } finally {
            $noise = ob_get_clean();
            if ($prev !== false) {
                ini_set('display_errors', (string) $prev);
            }
        }

        if (is_string($noise) && trim($noise) !== '') {
            Log::warning('Stray output discarded before JSON response', [
                'path' => $request->path(),
                'noise' => mb_substr($noise, 0, 500),
            ]);
        }

        return $response;
    }
}
