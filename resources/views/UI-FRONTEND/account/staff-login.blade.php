@php
  $seoTitle = 'Đăng nhập hệ thống quản trị — Win Win';
  $seoDescription = 'Đăng nhập hệ thống quản trị Đồ Chơi Win Win.';
  $loginRedirect = trim((string) request()->query('redirect', ''));
  $ssoLoginUrl = url('/account/login');
  if ($loginRedirect !== '') {
    $ssoLoginUrl .= '?redirect='.urlencode($loginRedirect);
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
            <a class="link" href="{{ $ssoLoginUrl }}" title="Đăng nhập tài khoản"><span>Đăng nhập</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          <li>
            <span class="text-neutral-100">Hệ thống quản trị</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section section-main-login">
      <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-custom justify-center gap-gutter" style="--grid-col: 50%">
          <div class="bg-background rounded-lg px-3 py-4 md:p-6 mb-6">
            <div class="space-y-4">
              <div class="text-center">
                <h1 class="text-h4 font-semibold mb-2">Đăng nhập hệ thống quản trị</h1>
                <p class="mb-0 text-neutral-200">Dành cho nhân viên, dùng email và mật khẩu nội bộ.</p>
              </div>
              <div>
                <div id="login" class="space-y-3">
                  <div id="login-error" class="hidden text-sm text-error bg-red-50 border border-red-200 rounded px-3 py-2" role="alert"></div>
                  <form method="post" action="#" id="customer_login" accept-charset="UTF-8" novalidate>
                    <div class="form-signup space-y-4">
                      <fieldset class="form-group">
                        <label class="label block mb-1" for="customer_email">Email <span class="required">*</span></label>
                        <input type="email" class="form-input" value="" name="email" id="customer_email" placeholder="Email" autocomplete="username" required>
                      </fieldset>
                      <fieldset class="form-group">
                        <label class="label block mb-1" for="customer_password">Mật khẩu <span class="required">*</span></label>
                        <input type="password" class="form-input" value="" name="password" id="customer_password" placeholder="Mật khẩu" autocomplete="current-password" required>
                        <span class="block mt-2">Quên mật khẩu?
                          <a href="#recover" class="link underline" onclick="showRecoverPasswordForm();return false;">Nhấn vào đây</a>
                        </span>
                      </fieldset>

                      <div class="mb-3 text-center pt-3">
                        <button class="btn bg-primary text-white btn-login w-full font-semibold max-w-[32rem]" type="submit">
                          Đăng nhập
                        </button>
                      </div>
                    </div>
                  </form>
                </div>

                <div id="recover-password" style="display:none;" class="form-signup page-login text-center">
                  <div class="mb-3">
                    <h2 class="text-base font-semibold mb-2">Đặt lại mật khẩu</h2>
                    <p>Chúng tôi sẽ gửi cho bạn một email để kích hoạt việc đặt lại mật khẩu.</p>
                  </div>

                  <form method="post" action="#" id="recover_customer_password" accept-charset="UTF-8" novalidate>
                    <div class="form-signup clearfix">
                      <div id="recover-error" class="hidden text-sm text-error bg-red-50 border border-red-200 rounded px-3 py-2 mb-3" role="alert"></div>
                      <div id="recover-success" class="hidden text-sm text-success bg-green-50 border border-green-200 rounded px-3 py-2 mb-3" role="status"></div>
                      <fieldset class="form-group">
                        <input type="email" class="form-input" value="" name="Email" id="recover-email" placeholder="Email" required>
                      </fieldset>
                    </div>

                    <div class="action_bottom my-3 flex flex-col justify-center items-center gap-3 mt-5">
                      <button class="btn bg-primary text-white w-full font-semibold max-w-[32rem] btn-recover" type="submit">
                        Lấy lại mật khẩu
                      </button>
                      <a href="#" class="link underline block" onclick="hideRecoverPasswordForm();return false;">Quay lại</a>
                    </div>
                  </form>
                </div>

                <p class="text-center mt-5 mb-0">
                  <a href="{{ $ssoLoginUrl }}" class="link underline text-sm text-neutral-200">Quay lại đăng nhập Google / Facebook</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <script>
      function showRecoverPasswordForm() {
        document.getElementById('recover-password').style.display = 'block';
        document.getElementById('login').style.display = 'none';
      }

      function hideRecoverPasswordForm() {
        document.getElementById('recover-password').style.display = 'none';
        document.getElementById('login').style.display = 'block';
      }

      if (window.location.hash === '#recover') {
        showRecoverPasswordForm();
      }

      (function () {
        function parseApiError(request, fallback) {
          var msg = fallback || 'Đã có lỗi xảy ra. Vui lòng thử lại.';
          try {
            var json = request.responseJSON;
            if (!json && request.responseText) {
              json = JSON.parse(request.responseText);
            }
            if (json && json.STATUS_DETAIL) {
              msg = String(json.STATUS_DETAIL).replace(/<br\s*\/?>/gi, ' ');
            }
          } catch (e) {}
          return msg;
        }

        function showBox(id, message) {
          var el = document.getElementById(id);
          if (!el) return;
          el.textContent = message;
          el.classList.remove('hidden');
        }

        function hideBox(id) {
          var el = document.getElementById(id);
          if (!el) return;
          el.classList.add('hidden');
          el.textContent = '';
        }

        function setSubmitting(form, on) {
          var btn = form.querySelector('button[type="submit"]');
          if (!btn) return;
          btn.disabled = on;
          btn.style.opacity = on ? '0.75' : '';
        }

        function redirectTarget() {
          var params = new URLSearchParams(window.location.search);
          var target = params.get('redirect');
          if (!target) return @json(url('/admin/san-pham/danh-sach'));
          try {
            var url = new URL(target, window.location.origin);
            if (url.origin !== window.location.origin) return @json(url('/admin/san-pham/danh-sach'));
            return url.href;
          } catch (e) {
            return @json(url('/admin/san-pham/danh-sach'));
          }
        }

        if (typeof jQuery === 'undefined') return;

        jQuery('#customer_login').on('submit', function (e) {
          e.preventDefault();
          hideBox('login-error');
          setSubmitting(this, true);

          var csrf = (typeof window.__csrfToken === 'function' && window.__csrfToken()) || '';
          var payload = {
            EMAIL: jQuery('#customer_email').val() || null,
            PASSWORD: jQuery('#customer_password').val() || null,
          };

          jQuery.ajax({
            type: 'POST',
            url: @json(url('/account/login')),
            contentType: 'application/json',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest'
            },
            data: JSON.stringify(payload),
            success: function (data) {
              var token = data && ((data.DATAS && data.DATAS.access_token) || data.ACCESS_TOKEN);
              if (!token) {
                showBox('login-error', 'Đăng nhập thất bại.');
                return;
              }
              localStorage.setItem('ACCESS_TOKEN', token);
              localStorage.removeItem('REFRESH_TOKEN');
              localStorage.setItem('AUTH_SCOPE', 'admin');
              window.location.href = redirectTarget();
            },
            error: function (request) {
              if (request.status === 401 || request.status === 403) return;
              showBox('login-error', parseApiError(request, 'Email hoặc mật khẩu không đúng.'));
            },
            complete: function () {
              setSubmitting(document.getElementById('customer_login'), false);
            },
          });
        });

        jQuery('#recover_customer_password').on('submit', function (e) {
          e.preventDefault();
          hideBox('recover-error');
          hideBox('recover-success');
          setSubmitting(this, true);

          jQuery.ajax({
            type: 'POST',
            url: '{{ url('/api/auth/forgot-password') }}',
            contentType: 'application/json',
            data: JSON.stringify({ EMAIL: jQuery('#recover-email').val() || null }),
            success: function (data) {
              var msg = (data && data.STATUS_DETAIL)
                ? String(data.STATUS_DETAIL).replace(/<br\s*\/?>/gi, ' ')
                : 'Đã gửi email đặt lại mật khẩu.';
              showBox('recover-success', msg);
            },
            error: function (request) {
              showBox('recover-error', parseApiError(request, 'Không thể gửi email đặt lại mật khẩu.'));
            },
            complete: function () {
              setSubmitting(document.getElementById('recover_customer_password'), false);
            },
          });
        });
      })();
    </script>
  </main>

  @include('UI-FRONTEND.common.footer')
  @include('UI-FRONTEND.common.theme-portals')
  <script src="{{ storefrontThemeAsset('main.js') }}" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
</body>
</html>
