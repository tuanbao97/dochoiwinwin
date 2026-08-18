<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Nguồn dữ liệu đăng nhập phía máy chủ cho giao diện cửa hàng.
 *
 * Trang web dựng sẵn thông tin tài khoản khi render nên UI không phải chờ
 * gọi API mới biết khách đã đăng nhập hay chưa. Token Passport vẫn dùng cho
 * các API, phiên (session) chỉ đóng vai trò nhận diện khi render.
 */
class StorefrontIdentity
{
    private const GUARD = 'web';

    private bool $resolved = false;

    private bool $payloadResolved = false;

    private ?User $user = null;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $payload = null;

    public function user(): ?User
    {
        if ($this->resolved) {
            return $this->user;
        }

        $this->resolved = true;
        $guard = Auth::guard(self::GUARD);

        /** @var User|null $user */
        $user = $guard->user();
        if (! $user instanceof User) {
            return $this->user = null;
        }

        // Tài khoản bị khóa giữa chừng thì phiên phải mất hiệu lực ngay.
        if ($user->STATUS !== AppConstant::STATUS_USING || ! $user->IS_ACTIVE) {
            $guard->logout();

            return $this->user = null;
        }

        $user->loadMissing(['profile', 'socialAccounts']);

        return $this->user = $user;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Dữ liệu tài khoản dùng cho view và cho JS đồng bộ trạng thái.
     *
     * @return array<string, mixed>|null
     */
    public function payload(): ?array
    {
        if ($this->payloadResolved) {
            return $this->payload;
        }

        $this->payloadResolved = true;
        $user = $this->user();
        if (! $user) {
            return $this->payload = null;
        }

        $profile = $user->profile;
        $social = $user->socialAccounts
            ->sortByDesc(static fn ($account): string => (string) ($account->UPD_DT ?? ''))
            ->first();
        $roles = $this->roleCodes($user);

        return $this->payload = [
            'ID' => (int) $user->ID,
            'FULL_NAME' => $user->FULL_NAME ?: $user->USERNAME,
            'EMAIL' => $user->EMAIL,
            'PHONE' => $profile?->MOBILE,
            'ADDRESS' => $profile?->ADDRESS,
            'AVATAR_URL' => $social?->AVATAR_URL,
            'PROVIDER' => $social?->PROVIDER,
            'IS_STAFF' => in_array('ADMIN', $roles, true) || in_array('CHUYEN_VIEN', $roles, true),
            'IS_ADMIN' => in_array('ADMIN', $roles, true),
        ];
    }

    /**
     * Lấy quyền trong một truy vấn thay vì gọi từng hàm kiểm tra riêng lẻ.
     *
     * @return array<int, string>
     */
    private function roleCodes(User $user): array
    {
        return DB::table('title as t')
            ->join('role as r', 'r.ID', '=', 't.ROLE_ID')
            ->where('t.USER_ID', $user->ID)
            ->where('t.STATUS', AppConstant::STATUS_USING)
            ->where('t.IS_ACTIVE', true)
            ->where('r.STATUS', AppConstant::STATUS_USING)
            ->where('r.IS_ACTIVE', true)
            ->pluck('r.CODE')
            ->map(static fn ($code): string => (string) $code)
            ->all();
    }

    public function login(User $user, Request $request): void
    {
        // Model đi qua tầng service có thể mang theo thuộc tính ảo (join/alias);
        // lấy bản sạch từ DB để lưu remember token không đụng cột không tồn tại.
        $fresh = User::query()->whereKey($user->getKey())->first() ?? $user;

        Auth::guard(self::GUARD)->login($fresh, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->forget();
    }

    public function logout(Request $request): void
    {
        Auth::guard(self::GUARD)->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $this->forget();
    }

    /**
     * Cấp token Passport mới cho phiên hiện tại (khi trình duyệt mất token cũ).
     */
    public function issueToken(User $user): string
    {
        return $user->createToken('Storefront Web')->accessToken;
    }

    private function forget(): void
    {
        $this->resolved = false;
        $this->payloadResolved = false;
        $this->user = null;
        $this->payload = null;
    }
}
