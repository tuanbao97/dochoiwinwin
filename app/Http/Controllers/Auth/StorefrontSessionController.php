<?php

namespace App\Http\Controllers\Auth;

use App\Enum\AppConstant;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\AuthService;
use App\Support\StorefrontIdentity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

/**
 * Đồng bộ hai chiều giữa token Passport (localStorage) và phiên đăng nhập
 * phía máy chủ, để trang nào cũng biết ngay khách là ai lúc render.
 */
class StorefrontSessionController extends Controller
{
    public function __construct(
        private readonly StorefrontIdentity $identity,
        private readonly AuthService $authService
    ) {}

    /**
     * Đăng nhập email/mật khẩu: mở phiên máy chủ ngay, rồi trả token cho API.
     * Trang sau render sẵn thông tin tài khoản, UI không phải fetch thêm.
     */
    public function login(Request $request): JsonResponse
    {
        $email = Str::lower(trim((string) $request->input('EMAIL', $request->input('email', ''))));
        $password = (string) $request->input('PASSWORD', $request->input('password', ''));

        if ($email === '' || $password === '') {
            return $this->loginFailed('Vui lòng nhập email và mật khẩu.');
        }

        $user = User::query()
            ->whereRaw('LOWER(EMAIL) = ?', [$email])
            ->where('STATUS', AppConstant::STATUS_USING)
            ->first();

        if (! $user || blank($user->PASSWORD) || ! Hash::check($password, $user->PASSWORD)) {
            return $this->loginFailed('Email hoặc mật khẩu không đúng.');
        }

        if (! $user->IS_ACTIVE) {
            return $this->loginFailed('Tài khoản đang bị khóa. Vui lòng liên hệ Đồ Chơi Win Win.');
        }

        $this->identity->login($user, $request);
        $accessToken = $this->identity->issueToken($user);

        return response()->json([
            'STATUS' => AppConstant::STATUS_SUCCESS,
            'STATUS_DETAIL' => 'Đăng nhập thành công.',
            'AUTHENTICATED' => true,
            'ACCESS_TOKEN' => $accessToken,
            'USER' => $this->identity->payload(),
            'DATAS' => [
                'access_token' => $accessToken,
            ],
        ]);
    }

    /**
     * Trình duyệt có token nhưng máy chủ chưa có phiên: dựng lại phiên từ token.
     */
    public function store(Request $request): JsonResponse
    {
        $token = $this->bearerToken($request);
        if ($token === '') {
            return $this->guest();
        }

        try {
            $validated = $this->authService->validateAccessToken($token);
            if (! $validated->isValid) {
                return $this->guest();
            }

            $userId = (int) $validated->validRequest->getAttribute('oauth_user_id');
            $user = $this->authService->validateUser($userId);
        } catch (Throwable) {
            return $this->guest();
        }

        $this->identity->login($user, $request);

        return response()->json([
            'AUTHENTICATED' => true,
            'USER' => $this->identity->payload(),
        ]);
    }

    /**
     * Máy chủ còn phiên nhưng trình duyệt mất token: cấp token mới.
     */
    public function token(Request $request): JsonResponse
    {
        $user = $this->identity->user();
        if (! $user) {
            return $this->guest();
        }

        return response()->json([
            'AUTHENTICATED' => true,
            'ACCESS_TOKEN' => $this->identity->issueToken($user),
            'USER' => $this->identity->payload(),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $this->identity->user();
        $this->revokeToken($user, $this->bearerToken($request));
        $this->identity->logout($request);

        return response()->json([
            'AUTHENTICATED' => false,
            'CSRF_TOKEN' => $request->hasSession() ? $request->session()->token() : null,
        ]);
    }

    private function bearerToken(Request $request): string
    {
        $header = (string) $request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            return trim(Str::after($header, 'Bearer '));
        }

        return trim((string) $request->input('ACCESS_TOKEN', ''));
    }

    /**
     * Chỉ thu hồi đúng token của thiết bị đang đăng xuất, thiết bị khác giữ nguyên.
     */
    private function revokeToken(?User $user, string $token): void
    {
        if (! $user || $token === '') {
            return;
        }

        try {
            $validated = $this->authService->validateAccessToken($token);
            if (! $validated->isValid) {
                return;
            }

            $tokenId = (string) $validated->validRequest->getAttribute('oauth_access_token_id');
            if ($tokenId === '') {
                return;
            }

            DB::table('oauth_refresh_tokens')->where('access_token_id', $tokenId)->delete();
            DB::table('oauth_access_tokens')->where('id', $tokenId)->delete();
        } catch (Throwable) {
            // Token hỏng thì phiên vẫn phải được đóng, không cần báo lỗi cho khách.
        }
    }

    private function loginFailed(string $message): JsonResponse
    {
        return response()->json([
            'STATUS' => AppConstant::STATUS_FAILURE,
            'STATUS_DETAIL' => $message,
            'AUTHENTICATED' => false,
            'USER' => null,
            'ERRORS' => ['MSG' => $message],
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    private function guest(): JsonResponse
    {
        return response()->json(['AUTHENTICATED' => false, 'USER' => null]);
    }
}
