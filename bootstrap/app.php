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
        
        // 401 - Unauthorized
        $exceptions->render(function (AuthenticationException $e, $request) use ($errors){
            $errors['MSG'] = 'Token không hợp lệ hoặc đã hết hạn. Vui lòng đăng nhập lại.';
            return response()->json([
                'ERRORS' => $errors,
                'STATUS' => AppConstant::STATUS_FAILURE,
                'CODE' => JsonResponse::HTTP_UNAUTHORIZED,
                'STATUS_DETAIL' => $errors['MSG']
            ], JsonResponse::HTTP_UNAUTHORIZED);
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
        $exceptions->render(function (HttpException $e, $request) use ($errors) {
            $errors['MSG'] = 'Bạn không có quyền truy cập tài nguyên này.';
            if ($e->getStatusCode() === 403) {
                return response()->json([
                    'ERRORS' => $errors,
                    'STATUS' => AppConstant::STATUS_FAILURE,
                    'CODE' => JsonResponse::HTTP_FORBIDDEN,
                    'STATUS_DETAIL' => $errors['MSG']
                ], JsonResponse::HTTP_FORBIDDEN);
            }
        });

        // 500 - Server Error (và các lỗi chưa được catch)
        $exceptions->render(function (Throwable $e, $request) {
            $errors['MSG'] = $e->getMessage();
            return response()->json([
                'ERRORS' => $errors,
                'STATUS' => AppConstant::STATUS_FAILURE,
                'CODE' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'STATUS_DETAIL' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        });

    })->create();
