@php
  $seoTitle = 'Đăng nhập tài khoản — Win Win';
  $seoDescription = 'Đăng nhập tài khoản Đồ Chơi Win Win.';
  $loginRedirect = trim((string) request()->query('redirect', ''));
  $googleUrl = route('social.redirect', ['provider' => 'google']);
  $facebookUrl = route('social.redirect', ['provider' => 'facebook']);
  if ($loginRedirect !== '') {
    $googleUrl .= '?redirect='.urlencode($loginRedirect);
    $facebookUrl .= '?redirect='.urlencode($loginRedirect);
  }
  $staffLoginUrl = url('/account/login/quan-tri');
  if ($loginRedirect !== '') {
    $staffLoginUrl .= '?redirect='.urlencode($loginRedirect);
  }
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  @include('UI-FRONTEND.common.header')

  <main>
    <div class="breadcrumbs">
      <div class="container">
        <ul class="breadcrumb py-3 flex flex-wrap items-center text-xs md:text-sm">
          <li class="home">
            <a class="link" href="{{ url('/') }}" title="Trang chủ"><span>Trang chủ</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          <li>
            <span class="text-neutral-100">Đăng nhập tài khoản</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section section-main-login">
      <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-custom justify-center gap-gutter" style="--grid-col: 50%">
          <div class="bg-background rounded-lg px-3 py-4 md:p-6 mb-6">
            <div class="space-y-4">
              <div class="ww-login-invite">
                <h1 class="ww-login-invite__title">Đăng nhập</h1>
                <p class="ww-login-invite__text">bạn sẽ có nhiều voucher giảm giá</p>
              </div>
              <div>
                @if (session('social_error'))
                  <div class="text-sm text-error bg-red-50 border border-red-200 rounded px-3 py-2 mb-4" role="alert">
                    {{ session('social_error') }}
                  </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <a
                    href="{{ $googleUrl }}"
                    class="btn w-full font-semibold flex items-center justify-center gap-2 text-white"
                    style="background:#ea4335"
                    rel="nofollow"
                  >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.91h5.38a4.6 4.6 0 0 1-2 3.02v2.54h3.24c1.9-1.75 2.98-4.33 2.98-7.4Z"/>
                      <path d="M12 22c2.7 0 4.97-.9 6.62-2.37l-3.24-2.54c-.9.6-2.05.96-3.38.96-2.61 0-4.82-1.76-5.61-4.13H3.04v2.62A10 10 0 0 0 12 22Z"/>
                      <path d="M6.39 13.92A6.02 6.02 0 0 1 6.08 12c0-.67.11-1.32.31-1.92V7.46H3.04A10 10 0 0 0 2 12c0 1.61.39 3.14 1.04 4.54l3.35-2.62Z"/>
                      <path d="M12 5.95c1.47 0 2.79.51 3.83 1.5l2.87-2.87A9.63 9.63 0 0 0 12 2a10 10 0 0 0-8.96 5.46l3.35 2.62C7.18 7.71 9.39 5.95 12 5.95Z"/>
                    </svg>
                    Google
                  </a>
                  <a
                    href="{{ $facebookUrl }}"
                    class="btn w-full font-semibold flex items-center justify-center gap-2 text-white"
                    style="background:#1877f2"
                    rel="nofollow"
                  >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.03 1.79-4.7 4.53-4.7 1.31 0 2.69.24 2.69.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07Z"/>
                    </svg>
                    Facebook
                  </a>
                </div>

                <p class="text-center mt-6 mb-0">
                  <a href="{{ $staffLoginUrl }}" class="link underline text-sm text-neutral-200">Đăng nhập hệ thống quản trị</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.footer')
  @include('UI-FRONTEND.common.theme-portals')
  <script src="{{ storefrontThemeAsset('main.js') }}" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
</body>
</html>
