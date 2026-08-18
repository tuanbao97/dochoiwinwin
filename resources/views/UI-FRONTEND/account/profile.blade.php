@php
  $seoTitle = 'Thông tin cá nhân — Đồ Chơi Win Win';
  $seoDescription = 'Cập nhật thông tin cá nhân và địa chỉ nhận hàng.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  @include('UI-FRONTEND.common.header')

  <main>
    <div class="breadcrumbs">
      <div class="container">
        <ul class="breadcrumb py-3 flex flex-wrap items-center text-xs md:text-sm">
          <li>
            <a class="link" href="{{ url('/') }}">Trang chủ</a>
            <span class="mx-1 md:mx-2">&nbsp;/&nbsp;</span>
          </li>
          <li><span class="text-neutral-100">Thông tin cá nhân</span></li>
        </ul>
      </div>
    </div>

    <section class="section">
      <div class="container">
        <div class="ww-profile">
          <div class="ww-profile__heading">
            <h1 class="text-h4 font-semibold mb-1">Thông tin cá nhân</h1>
            <p class="text-neutral-200 mb-0">Thông tin này sẽ tự động điền khi bạn đặt hàng lần sau.</p>
          </div>

          <div class="ww-profile__message" data-profile-message hidden></div>

          <form class="ww-profile__form" data-profile-form>
            <div>
              <label for="profile-full-name">Họ và tên <span class="text-error">*</span></label>
              <input id="profile-full-name" name="FULL_NAME" class="form-input w-full rounded border-neutral-50" autocomplete="name" required value="{{ $storefrontUser['FULL_NAME'] ?? '' }}">
              <span class="ww-profile__error" data-error="FULL_NAME"></span>
            </div>
            <div class="ww-profile__grid">
              <div>
                <label for="profile-phone">Số điện thoại <span class="text-error">*</span></label>
                <input id="profile-phone" name="PHONE" class="form-input w-full rounded border-neutral-50" autocomplete="tel" inputmode="tel" required value="{{ $storefrontUser['PHONE'] ?? '' }}">
                <span class="ww-profile__error" data-error="PHONE"></span>
              </div>
              <div>
                <label for="profile-email">Email</label>
                <input id="profile-email" name="EMAIL" type="email" class="form-input w-full rounded border-neutral-50" readonly value="{{ $storefrontUser['EMAIL'] ?? '' }}">
                <small>Email đăng nhập không thể thay đổi tại đây.</small>
              </div>
            </div>
            <div>
              <label for="profile-address">Địa chỉ nhận hàng <span class="text-error">*</span></label>
              <textarea id="profile-address" name="ADDRESS" rows="4" class="form-textarea w-full rounded border-neutral-50" autocomplete="street-address" required>{{ $storefrontUser['ADDRESS'] ?? '' }}</textarea>
              <span class="ww-profile__error" data-error="ADDRESS"></span>
            </div>
            <button type="submit" class="btn bg-primary text-white font-semibold" data-profile-submit>Lưu thông tin</button>
          </form>
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.footer')
  @include('UI-FRONTEND.common.theme-portals')

  <style>
    .ww-profile{max-width:760px;margin:0 auto;background:#fff;border:1px solid #eee;border-radius:16px;padding:24px}
    .ww-profile__heading{margin-bottom:22px}
    .ww-profile__form{display:flex;flex-direction:column;gap:18px}
    .ww-profile__form label{display:block;font-weight:600;margin-bottom:6px}
    .ww-profile__grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .ww-profile__form small{display:block;margin-top:5px;color:#777}
    .ww-profile__error{display:block;color:#dc2626;font-size:13px;margin-top:5px;min-height:18px}
    .ww-profile__message{padding:12px 14px;border-radius:8px;margin-bottom:16px}
    .ww-profile__message.is-success{background:#ecfdf5;color:#047857}
    .ww-profile__message.is-error{background:#fef2f2;color:#b91c1c}
    .ww-profile__form .btn{align-self:flex-start;display:inline-flex;padding:10px 24px;border-radius:8px}
    @media(max-width:767px){.ww-profile{padding:18px}.ww-profile__grid{grid-template-columns:1fr}}
  </style>

  <script>
  (function () {
    var apiUrl = @json(url('/api/auth/storefront-profile'));
    var form = document.querySelector('[data-profile-form]');
    var message = document.querySelector('[data-profile-message]');
    var submit = document.querySelector('[data-profile-submit]');

    // Máy chủ đã chặn khách chưa đăng nhập, ở đây chỉ cần token để gọi API lưu.
    function authToken() {
      if (window.wwAuth && window.wwAuth.ensureToken) return window.wwAuth.ensureToken();
      try {
        return Promise.resolve(localStorage.getItem('ACCESS_TOKEN'));
      } catch (e) {
        return Promise.resolve(null);
      }
    }

    function headers(token) {
      return {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      };
    }

    function showMessage(text, success) {
      message.textContent = text;
      message.className = 'ww-profile__message ' + (success ? 'is-success' : 'is-error');
      message.hidden = false;
    }

    function fill(user) {
      form.FULL_NAME.value = user.FULL_NAME || '';
      form.EMAIL.value = user.EMAIL || '';
      form.PHONE.value = user.PHONE || '';
      form.ADDRESS.value = user.ADDRESS || '';
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      message.hidden = true;
      form.querySelectorAll('[data-error]').forEach(function (el) { el.textContent = ''; });
      submit.disabled = true;
      submit.textContent = 'Đang lưu…';

      authToken()
        .then(function (token) {
          return fetch(apiUrl, {
            method: 'PUT',
            headers: headers(token),
            body: JSON.stringify({
              FULL_NAME: form.FULL_NAME.value,
              PHONE: form.PHONE.value,
              ADDRESS: form.ADDRESS.value
            })
          });
        })
        .then(function (response) {
          return response.json().then(function (payload) {
            return { ok: response.ok, payload: payload };
          });
        })
        .then(function (result) {
          if (!result.ok || result.payload.STATUS === false) {
            var errors = result.payload.errors || result.payload.ERRORS || {};
            Object.keys(errors).forEach(function (key) {
              var el = form.querySelector('[data-error="' + key + '"]');
              var value = errors[key];
              if (el) el.textContent = Array.isArray(value) ? value[0] : String(value);
            });
            throw new Error('Vui lòng kiểm tra lại thông tin.');
          }
          fill(result.payload.DATAS || {});
          showMessage('Đã lưu thông tin cá nhân.', true);
          if (window.wwAuth && window.wwAuth.paint && result.payload.DATAS) {
            window.wwAuth.user = Object.assign({}, window.wwAuth.user || {}, result.payload.DATAS);
            window.wwAuth.paint(window.wwAuth.user);
          }
        })
        .catch(function (error) {
          showMessage(error.message || 'Không thể lưu thông tin.', false);
        })
        .finally(function () {
          submit.disabled = false;
          submit.textContent = 'Lưu thông tin';
        });
    });
  })();
  </script>
</body>
</html>
