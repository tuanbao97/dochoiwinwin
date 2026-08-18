<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\StorefrontIdentity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Các trang tài khoản lấy thông tin đăng nhập từ phiên máy chủ rồi mới render.
 * Header / form không phải chờ JS gọi API mới biết khách là ai.
 */
class StorefrontAccountPageController extends Controller
{
    public function __construct(private readonly StorefrontIdentity $identity) {}

    public function login(Request $request): View|RedirectResponse
    {
        if ($this->identity->check()) {
            $target = $this->safeRedirect($request, url('/'));

            return redirect($target);
        }

        return view('UI-FRONTEND.account.login', $this->pageData());
    }

    public function staffLogin(Request $request): View|RedirectResponse
    {
        if ($this->identity->check()) {
            $fallback = $this->identity->payload()['IS_ADMIN'] ?? false
                ? url('/admin/san-pham/danh-sach')
                : url('/');

            return redirect($this->safeRedirect($request, $fallback));
        }

        return view('UI-FRONTEND.account.staff-login', $this->pageData());
    }

    public function orders(Request $request): View|RedirectResponse
    {
        if (! $this->identity->check()) {
            return $this->guestRedirect($request, '/account/orders');
        }

        return view('UI-FRONTEND.account.orders', $this->pageData());
    }

    public function profile(Request $request): View|RedirectResponse
    {
        if (! $this->identity->check()) {
            return $this->guestRedirect($request, '/account/profile');
        }

        return view('UI-FRONTEND.account.profile', $this->pageData());
    }

    /**
     * @return array<string, mixed>
     */
    private function pageData(): array
    {
        return [
            'productId' => 0,
            'storefrontUser' => $this->identity->payload(),
        ];
    }

    private function guestRedirect(Request $request, string $path): RedirectResponse
    {
        return redirect('/account/login?redirect='.urlencode(url($path)));
    }

    private function safeRedirect(Request $request, string $fallback): string
    {
        $target = trim((string) $request->query('redirect', ''));
        if ($target === '') {
            return $fallback;
        }

        $host = parse_url($target, PHP_URL_HOST);
        if ($host !== null && $host !== $request->getHost()) {
            return $fallback;
        }

        return $target;
    }
}
