<?php

use App\Enum\AppConstant;
use App\Http\Middleware\EvictCachePublicApiMiddleware;
use App\Http\Middleware\PublicApiResponseCacheMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => Illuminate\Auth\Middleware\Authenticate::class,
            'auth:api' => Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
            'custom-validate-oauth-token' => App\Http\Middleware\CustomValidateOauthTokenMiddleware::class,
            'require-admin-access' => App\Http\Middleware\RequireAdminAccessMiddleware::class,
            'count-client-view-website' => App\Http\Middleware\CountClientViewWebsiteMiddleware::class,
            'cache-public-api-response' => PublicApiResponseCacheMiddleware::class,
            'evict-cache-public-api' => EvictCachePublicApiMiddleware::class,
        ]);
        
        // Middleware toàn cục cho web và các route khác
        $middleware->web(append: [
            \App\Http\Middleware\Cors::class,
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\ShareStorefrontIdentityMiddleware::class,
        ]);

        $middleware->group('api', [
            \App\Http\Middleware\Cors::class,
            \App\Http\Middleware\ForceJsonResponse::class
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        /* Custom response 1 số lỗi từ hệ thống. */
        $errors = [
            'MSG' => null
        ];

        // Tránh render JSON lỗi 2 lần (thiếu APP_KEY khi gắn cookie session
        // vào response lỗi → client nhận 2 object JSON dính nhau).
        $jsonError = static function (string $message, int $code) use (&$errors): JsonResponse {
            static $rendering = false;
            if ($rendering) {
                return new JsonResponse([
                    'ERRORS' => ['MSG' => $message],
                    'STATUS' => AppConstant::STATUS_FAILURE,
                    'CODE' => $code,
                    'STATUS_DETAIL' => $message,
                ], $code);
            }

            $rendering = true;
            try {
                $errors['MSG'] = $message;

                return response()->json([
                    'ERRORS' => $errors,
                    'STATUS' => AppConstant::STATUS_FAILURE,
                    'CODE' => $code,
                    'STATUS_DETAIL' => $message,
                ], $code);
            } finally {
                $rendering = false;
            }
        };
        
        // 401 - Unauthorized
        $exceptions->render(function (AuthenticationException $e, $request) use ($jsonError) {
            return $jsonError(
                'Token không hợp lệ hoặc đã hết hạn. Vui lòng đăng nhập lại.',
                JsonResponse::HTTP_UNAUTHORIZED
            );
        });

        // 422 - Dữ liệu không hợp lệ (bao gồm kiểm tra tồn kho).
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'ERRORS' => $e->errors(),
                'STATUS' => AppConstant::STATUS_FAILURE,
                'CODE' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'STATUS_DETAIL' => collect($e->errors())->flatten()->first()
                    ?: 'Dữ liệu không hợp lệ.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        });


        // 403 - Forbidden
        $exceptions->render(function (HttpException $e, $request) use ($jsonError) {
            if ($e->getStatusCode() === 403) {
                return $jsonError(
                    'Bạn không có quyền truy cập tài nguyên này.',
                    JsonResponse::HTTP_FORBIDDEN
                );
            }
        });

        // 500 - Server Error (và các lỗi chưa được catch)
        $exceptions->render(function (Throwable $e, $request) use ($jsonError) {
            $message = trim($e->getMessage()) !== ''
                ? $e->getMessage()
                : 'Lỗi máy chủ.';

            // Thiếu APP_KEY: trả đúng 1 JSON, không đi qua EncryptCookies lần 2.
            if (str_contains($message, 'No application encryption key has been specified')) {
                return new JsonResponse([
                    'ERRORS' => ['MSG' => 'Thiếu APP_KEY. Chạy: php artisan key:generate'],
                    'STATUS' => AppConstant::STATUS_FAILURE,
                    'CODE' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'STATUS_DETAIL' => 'Thiếu APP_KEY. Chạy: php artisan key:generate',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $jsonError($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        });

    })->create();
