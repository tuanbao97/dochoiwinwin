<?php

namespace App\Http\Controllers\Auth;

use App\Enum\AppConstant;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;
use Throwable;

class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        try {
            $this->ensureProviderIsAvailable($provider);

            $driver = Socialite::driver($provider);

            if ($provider === 'facebook') {
                $driver->scopes(['email']);
            }

            return $driver->redirect();
        } catch (Throwable $e) {
            return redirect('/account/login')->with('social_error', $e->getMessage());
        }
    }

    public function callback(string $provider)
    {
        try {
            $this->ensureProviderIsAvailable($provider);
            $socialUser = Socialite::driver($provider)->user();
            $providerUserId = trim((string) $socialUser->getId());
            $email = Str::lower(trim((string) $socialUser->getEmail()));

            if ($providerUserId === '' || $email === '') {
                throw new RuntimeException(
                    'Tài khoản '.$this->providerLabel($provider).' chưa cung cấp email. Vui lòng cấp quyền email và thử lại.'
                );
            }

            $user = DB::transaction(function () use ($provider, $providerUserId, $email, $socialUser): User {
                $socialAccount = UserSocialAccount::query()
                    ->where('PROVIDER', $provider)
                    ->where('PROVIDER_USER_ID', $providerUserId)
                    ->first();

                $user = $socialAccount?->user;
                if (! $user) {
                    $user = User::query()
                        ->where('STATUS', AppConstant::STATUS_USING)
                        ->whereRaw('LOWER(EMAIL) = ?', [$email])
                        ->first();
                }

                if ($user && $user->hasStaffAccess()) {
                    throw new RuntimeException(
                        'Email này thuộc tài khoản quản trị. Vui lòng đăng nhập tại trang quản trị bằng mật khẩu.'
                    );
                }

                if (! $user) {
                    $name = trim((string) $socialUser->getName());
                    $user = User::query()->create([
                        'EMAIL' => $email,
                        'USERNAME' => $email,
                        'PASSWORD' => null,
                        'FULL_NAME' => $name !== '' ? Str::limit($name, 500, '') : Str::before($email, '@'),
                        'COUNT_LOGIN_FAIL' => 0,
                        'STATUS' => AppConstant::STATUS_USING,
                        'IS_ACTIVE' => true,
                        'CRT_NAME' => $this->providerLabel($provider),
                        'UPD_NAME' => $this->providerLabel($provider),
                    ]);
                }

                if ($user->STATUS !== AppConstant::STATUS_USING || ! $user->IS_ACTIVE) {
                    throw new RuntimeException('Tài khoản đang bị khóa. Vui lòng liên hệ Đồ Chơi Win Win.');
                }

                $roleId = $this->ensureStorefrontUserRole();
                $this->ensureStorefrontUserTitle($user, $roleId, $provider);

                UserSocialAccount::query()->updateOrCreate(
                    [
                        'USER_ID' => $user->ID,
                        'PROVIDER' => $provider,
                    ],
                    [
                        'PROVIDER_USER_ID' => $providerUserId,
                        'EMAIL' => $email,
                        'NAME' => trim((string) $socialUser->getName()) ?: null,
                        'AVATAR_URL' => trim((string) $socialUser->getAvatar()) ?: null,
                    ]
                );

                return $user;
            });

            $accessToken = $user->createToken('Storefront '.$this->providerLabel($provider))->accessToken;

            return view('UI-FRONTEND.account.social-auth-complete', [
                'accessToken' => $accessToken,
                'redirectUrl' => url('/'),
            ]);
        } catch (Throwable $e) {
            Log::warning('Social login failed', [
                'provider' => $provider,
                'message' => $e->getMessage(),
            ]);

            return redirect('/account/login')->with('social_error', $e->getMessage());
        }
    }

    private function ensureProviderIsAvailable(string $provider): void
    {
        if (! in_array($provider, self::PROVIDERS, true)) {
            abort(404);
        }

        $config = config('services.'.$provider, []);
        if (blank($config['client_id'] ?? null) || blank($config['client_secret'] ?? null)) {
            throw new RuntimeException(
                'Đăng nhập '.$this->providerLabel($provider).' chưa được cấu hình trên máy chủ.'
            );
        }
    }

    private function ensureStorefrontUserRole(): int
    {
        $roleId = DB::table('role')->where('CODE', 'USER')->value('ID');
        if ($roleId) {
            return (int) $roleId;
        }

        return (int) DB::table('role')->insertGetId([
            'CODE' => 'USER',
            'NAME' => 'Người dùng',
            'DESCRIPTION' => 'Tài khoản khách hàng trên giao diện cửa hàng',
            'CRT_DT' => now(),
            'UPD_DT' => now(),
            'STATUS' => AppConstant::STATUS_USING,
            'IS_ACTIVE' => true,
        ]);
    }

    private function ensureStorefrontUserTitle(User $user, int $roleId, string $provider): void
    {
        $exists = DB::table('title')
            ->where('USER_ID', $user->ID)
            ->where('ROLE_ID', $roleId)
            ->where('STATUS', AppConstant::STATUS_USING)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('title')->insert([
            'USER_ID' => $user->ID,
            'ROLE_ID' => $roleId,
            'DESCRIPTION' => 'Người dùng đăng nhập bằng '.$this->providerLabel($provider),
            'SORT_ORDER' => 1,
            'CRT_DT' => now(),
            'UPD_DT' => now(),
            'CRT_NAME' => $this->providerLabel($provider),
            'UPD_NAME' => $this->providerLabel($provider),
            'STATUS' => AppConstant::STATUS_USING,
            'IS_ACTIVE' => true,
        ]);
    }

    private function providerLabel(string $provider): string
    {
        return $provider === 'google' ? 'Google' : 'Facebook';
    }
}
