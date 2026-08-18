@php
  $wwUser = $storefrontUser ?? null;
  $wwInitial = $wwUser
    ? mb_strtoupper(mb_substr(trim((string) ($wwUser['FULL_NAME'] ?: $wwUser['EMAIL'] ?: '?')), 0, 1))
    : '';
@endphp
<div class="ww-account" data-ww-account>
  <a
    href="{{ url('/account/login') }}?redirect={{ urlencode(url()->current()) }}"
    title="Đăng nhập"
    class="ww-account__guest ww-account__login-btn"
    data-ww-account-guest
    @if($wwUser) hidden style="display:none" @endif
  >
    <i class="icon icon-user" aria-hidden="true"></i>
    <span>Đăng nhập</span>
  </a>

  <div class="ww-account__user" data-ww-account-user @unless($wwUser) hidden @endunless>
    <button
      type="button"
      class="ww-account__trigger"
      data-ww-account-trigger
      aria-haspopup="true"
      aria-expanded="false"
      title="Tài khoản của tôi"
    >
      <img
        class="ww-account__avatar"
        alt="Ảnh đại diện"
        data-ww-account-avatar
        referrerpolicy="no-referrer"
        @if($wwUser && $wwUser['AVATAR_URL']) src="{{ $wwUser['AVATAR_URL'] }}" @else hidden @endif
      >
      <span
        class="ww-account__initial"
        data-ww-account-initial
        @if($wwUser && $wwUser['AVATAR_URL']) hidden @endif
      >{{ $wwInitial }}</span>
    </button>

    <div class="ww-account__dropdown" data-ww-account-dropdown hidden>
      <div class="ww-account__head">
        <img
          class="ww-account__head-avatar"
          alt=""
          data-ww-account-head-avatar
          referrerpolicy="no-referrer"
          @if($wwUser && $wwUser['AVATAR_URL']) src="{{ $wwUser['AVATAR_URL'] }}" @else hidden @endif
        >
        <span
          class="ww-account__head-initial"
          data-ww-account-head-initial
          @if($wwUser && $wwUser['AVATAR_URL']) hidden @endif
        >{{ $wwInitial }}</span>
        <div class="ww-account__identity">
          <p class="ww-account__name" data-ww-account-name>{{ $wwUser['FULL_NAME'] ?? '' }}</p>
          <p class="ww-account__email" data-ww-account-email>{{ $wwUser['EMAIL'] ?? '' }}</p>
        </div>
      </div>

      <ul class="ww-account__menu">
        <li data-ww-account-admin @unless($wwUser && $wwUser['IS_ADMIN']) hidden @endunless>
          <a
            class="ww-account__menu-link ww-account__admin"
            href="{{ url('/admin/san-pham/danh-sach') }}"
            data-ww-account-admin-link
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 4H10V10H4V4ZM14 4H20V10H14V4ZM4 14H10V20H4V14ZM14 14H20V20H14V14Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
            <span>Quản lý</span>
          </a>
        </li>
        <li>
          <a class="ww-account__menu-link" href="{{ url('/account/orders') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 3H18V21L15 19L12 21L9 19L6 21V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
              <path d="M9 8H15M9 12H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <span>Lịch sử mua hàng</span>
          </a>
        </li>
        <li>
          <a class="ww-account__menu-link" href="{{ url('/cart') }}">
            <i class="icon icon-cart"></i>
            <span>Giỏ hàng của tôi</span>
          </a>
        </li>
        <li>
          <a class="ww-account__menu-link" href="{{ url('/account/profile') }}">
            <i class="icon icon-user"></i>
            <span>Thông tin cá nhân</span>
          </a>
        </li>
        <li>
          <button type="button" class="ww-account__menu-link ww-account__logout" data-ww-account-logout>
            <i class="icon icon-user"></i>
            <span>Đăng xuất</span>
          </button>
        </li>
      </ul>
    </div>
  </div>
</div>

<script>
(function () {
  var root = document.querySelector('[data-ww-account]');
  if (!root) return;

  var urls = {
    session: @json(url('/account/session')),
    token: @json(url('/account/session/token')),
    logout: @json(url('/account/logout')),
    orders: @json(url('/account/orders'))
  };

  var guest = root.querySelector('[data-ww-account-guest]');
  var box = root.querySelector('[data-ww-account-user]');
  var trigger = root.querySelector('[data-ww-account-trigger]');
  var dropdown = root.querySelector('[data-ww-account-dropdown]');
  var avatar = root.querySelector('[data-ww-account-avatar]');
  var initial = root.querySelector('[data-ww-account-initial]');
  var headAvatar = root.querySelector('[data-ww-account-head-avatar]');
  var headInitial = root.querySelector('[data-ww-account-head-initial]');
  var nameEl = root.querySelector('[data-ww-account-name]');
  var emailEl = root.querySelector('[data-ww-account-email]');
  var adminItem = root.querySelector('[data-ww-account-admin]');
  var adminLink = root.querySelector('[data-ww-account-admin-link]');
  var logoutBtn = root.querySelector('[data-ww-account-logout]');

  // Máy chủ đã biết khách là ai ngay lúc render nên UI không phải chờ API.
  var serverUser = @json($storefrontUser ?? null);

  function csrf() {
    return (
      (typeof window.__csrfToken === 'function' && window.__csrfToken()) ||
      (document.querySelector('meta[name="csrf-token"]') || {}).content ||
      ''
    );
  }

  function readToken() {
    try {
      return localStorage.getItem('ACCESS_TOKEN');
    } catch (e) {
      return null;
    }
  }

  function writeToken(value) {
    try {
      if (value) {
        localStorage.setItem('ACCESS_TOKEN', value);
        localStorage.setItem('AUTH_SCOPE', 'storefront');
      } else {
        localStorage.removeItem('ACCESS_TOKEN');
        localStorage.removeItem('REFRESH_TOKEN');
        localStorage.removeItem('AUTH_SCOPE');
      }
    } catch (e) {}
  }

  function postJson(url, body, token) {
    var headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrf()
    };
    if (token) headers.Authorization = 'Bearer ' + token;

    return fetch(url, {
      method: 'POST',
      headers: headers,
      credentials: 'same-origin',
      body: JSON.stringify(body || {})
    }).then(function (response) {
      if (!response.ok) throw new Error('request-failed');
      return response.json();
    });
  }

  function firstLetter(user) {
    var source = String((user && (user.FULL_NAME || user.EMAIL)) || '?').trim();
    return source ? source.charAt(0).toUpperCase() : '?';
  }

  function paint(user) {
    var letter = firstLetter(user);
    initial.textContent = letter;
    headInitial.textContent = letter;
    nameEl.textContent = user.FULL_NAME || 'Khách hàng';
    emailEl.textContent = user.EMAIL || '';

    if (user.AVATAR_URL) {
      avatar.src = user.AVATAR_URL;
      headAvatar.src = user.AVATAR_URL;
      avatar.hidden = false;
      headAvatar.hidden = false;
      initial.hidden = true;
      headInitial.hidden = true;
    } else {
      avatar.removeAttribute('src');
      headAvatar.removeAttribute('src');
      avatar.hidden = true;
      headAvatar.hidden = true;
      initial.hidden = false;
      headInitial.hidden = false;
    }

    guest.hidden = true;
    guest.style.display = 'none';
    box.hidden = false;
    if (user.IS_ADMIN === true) adminItem.hidden = false;

    paintDrawer(user, letter);
  }

  // Menu drawer trên mobile nằm cuối trang nên có thể chưa tồn tại lúc chạy.
  function onReady(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback);
      return;
    }
    callback();
  }

  function paintDrawer(user, letter) {
    onReady(function () {
      paintDrawerNow(user, letter);
    });
  }

  function paintDrawerNow(user, letter) {
    var link = document.querySelector('[data-ww-drawer-account]');
    if (!link) return;

    var drawerName = document.querySelector('[data-ww-drawer-name]');
    var drawerAvatar = document.querySelector('[data-ww-drawer-avatar]');
    var drawerIcon = document.querySelector('[data-ww-drawer-icon]');
    var drawerAdmin = document.querySelector('[data-ww-drawer-admin]');
    var drawerLogout = document.querySelector('[data-ww-drawer-logout]');

    link.setAttribute('href', urls.orders);
    link.setAttribute('title', user.EMAIL || 'Tài khoản');
    if (drawerName) drawerName.textContent = user.FULL_NAME || letter;

    if (user.AVATAR_URL && drawerAvatar) {
      drawerAvatar.src = user.AVATAR_URL;
      drawerAvatar.hidden = false;
      if (drawerIcon) drawerIcon.hidden = true;
    }
    if (drawerLogout) drawerLogout.hidden = false;
    if (user.IS_ADMIN === true && drawerAdmin) drawerAdmin.hidden = false;
  }

  function enterAdmin() {
    try {
      localStorage.setItem('AUTH_SCOPE', 'admin');
    } catch (e) {}
  }

  function logout() {
    var done = function () {
      writeToken(null);
      // Trang tài khoản không còn xem được sau khi đăng xuất nên đưa về trang chủ.
      if (window.location.pathname.indexOf('/account/') !== -1) {
        window.location.href = @json(url('/'));
        return;
      }
      window.location.reload();
    };

    postJson(urls.logout, {}, readToken()).then(done).catch(done);
  }

  function closeDropdown() {
    dropdown.hidden = true;
    trigger.setAttribute('aria-expanded', 'false');
  }

  trigger.addEventListener('click', function (event) {
    event.stopPropagation();
    var willOpen = dropdown.hidden;
    dropdown.hidden = !willOpen;
    trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  });
  document.addEventListener('click', function (event) {
    if (!dropdown.hidden && !root.contains(event.target)) closeDropdown();
  });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeDropdown();
  });

  if (adminLink) adminLink.addEventListener('click', enterAdmin);
  if (logoutBtn) logoutBtn.addEventListener('click', logout);

  onReady(function () {
    var drawerLogout = document.querySelector('[data-ww-drawer-logout]');
    var drawerAdmin = document.querySelector('[data-ww-drawer-admin]');
    if (drawerLogout) drawerLogout.addEventListener('click', logout);
    if (drawerAdmin) drawerAdmin.addEventListener('click', enterAdmin);
  });

  /* ------------------------------------------------------------------ *
   * Đồng bộ hai chiều: phiên máy chủ ↔ token trong localStorage
   * ------------------------------------------------------------------ */

  var pendingToken = null;
  var reloadFlag = 'ww-auth-reloading';

  // Xin token mới từ phiên máy chủ (dùng khi trình duyệt mất token hoặc token hết hạn).
  function refreshToken() {
    if (pendingToken) return pendingToken;

    pendingToken = postJson(urls.token, {})
      .then(function (payload) {
        if (!payload || !payload.ACCESS_TOKEN) return null;
        writeToken(payload.ACCESS_TOKEN);
        return payload.ACCESS_TOKEN;
      })
      .catch(function () {
        return null;
      })
      .finally(function () {
        pendingToken = null;
      });

    return pendingToken;
  }

  function ensureToken() {
    var token = readToken();
    if (token) return Promise.resolve(token);
    if (!window.wwAuth.user) return Promise.resolve(null);

    return refreshToken();
  }

  window.wwAuth = {
    user: serverUser,
    loginUrl: @json(storefrontLoginUrl(url()->current())),
    token: readToken,
    ensureToken: ensureToken,
    refreshToken: refreshToken,
    logout: logout,
    paint: paint
  };

  try { sessionStorage.removeItem(reloadFlag); } catch (e) {}

  if (serverUser) {
    // HTML đã có sẵn thông tin tài khoản. Chỉ lấy token nền cho các API.
    if (!readToken()) ensureToken();
  } else if (readToken()) {
    // Còn token nhưng phiên máy chủ đã mất: dựng lại phiên rồi tải HTML đã có user.
    var shouldReload = true;
    try { shouldReload = sessionStorage.getItem(reloadFlag) !== '1'; } catch (e) {}
    if (shouldReload) {
      try { sessionStorage.setItem(reloadFlag, '1'); } catch (e) {}
      postJson(urls.session, {}, readToken())
        .then(function (payload) {
          if (payload && payload.AUTHENTICATED) {
            window.location.reload();
            return;
          }
          try { sessionStorage.removeItem(reloadFlag); } catch (e) {}
          writeToken(null);
        })
        .catch(function () {
          try { sessionStorage.removeItem(reloadFlag); } catch (e) {}
          writeToken(null);
        });
    }
  }

  // Đăng nhập/đăng xuất ở tab khác thì tab này cập nhật theo ngay.
  window.addEventListener('storage', function (event) {
    if (event.key !== 'ACCESS_TOKEN') return;
    if (!!event.newValue === !!serverUser) return;
    window.location.reload();
  });

  // Quay lại trang từ bộ nhớ đệm của trình duyệt: xác minh lại trạng thái.
  window.addEventListener('pageshow', function (event) {
    if (!event.persisted) return;
    if (!!readToken() !== !!serverUser) window.location.reload();
  });
})();
</script>
