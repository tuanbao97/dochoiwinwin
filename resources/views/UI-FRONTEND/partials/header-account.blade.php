<div class="ww-account" data-ww-account>
  <a
    href="{{ url('/account/login') }}"
    title="Đăng nhập"
    class="ww-account__guest header-icon-group dnone md:flex gap-2 items-center cart-group hover:bg-neutral-50 active:scale-95 transition-all duration-150 md:px-2 px-1 py-1 rounded-sm"
    data-ww-account-guest
  >
    <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-full flex items-center justify-center relative border border-neutral-50">
      <i class="icon icon-user"></i>
    </div>
  </a>

  <div class="ww-account__user" data-ww-account-user hidden>
    <button
      type="button"
      class="ww-account__trigger"
      data-ww-account-trigger
      aria-haspopup="true"
      aria-expanded="false"
      title="Tài khoản của tôi"
    >
      <img class="ww-account__avatar" alt="Ảnh đại diện" data-ww-account-avatar hidden referrerpolicy="no-referrer">
      <span class="ww-account__initial" data-ww-account-initial></span>
    </button>

    <div class="ww-account__dropdown" data-ww-account-dropdown hidden>
      <div class="ww-account__head">
        <img class="ww-account__head-avatar" alt="" data-ww-account-head-avatar hidden referrerpolicy="no-referrer">
        <span class="ww-account__head-initial" data-ww-account-head-initial></span>
        <div class="ww-account__identity">
          <p class="ww-account__name" data-ww-account-name></p>
          <p class="ww-account__email" data-ww-account-email></p>
        </div>
      </div>

      <ul class="ww-account__menu">
        <li data-ww-account-admin hidden>
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

  var profileUrl = '{{ url('/api/auth/storefront-profile') }}';
  var logoutUrl = '{{ url('/api/auth/logout-user') }}';

  function drawer(selector) {
    return document.querySelector(selector);
  }

  function token() {
    try {
      return localStorage.getItem('ACCESS_TOKEN');
    } catch (e) {
      return null;
    }
  }

  function clearSession() {
    try {
      localStorage.removeItem('ACCESS_TOKEN');
      localStorage.removeItem('REFRESH_TOKEN');
      localStorage.removeItem('AUTH_SCOPE');
    } catch (e) {}
  }

  function firstLetter(user) {
    var source = (user.FULL_NAME || user.EMAIL || '?').trim();
    return source ? source.charAt(0).toUpperCase() : '?';
  }

  function render(user) {
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

      avatar.addEventListener('error', function () {
        avatar.hidden = true;
        headAvatar.hidden = true;
        initial.hidden = false;
        headInitial.hidden = false;
      });
    }

    guest.hidden = true;
    guest.classList.remove('md:flex');
    guest.style.display = 'none';
    box.hidden = false;

    if (user.IS_ADMIN === true) {
      adminItem.hidden = false;
    }

    renderDrawer(user, letter);
  }

  // Menu drawer trên mobile dùng chung dữ liệu tài khoản, nhưng nằm cuối trang
  function renderDrawer(user, letter) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function () {
        renderDrawer(user, letter);
      });
      return;
    }

    var link = drawer('[data-ww-drawer-account]');
    if (!link) return;

    var drawerName = drawer('[data-ww-drawer-name]');
    var drawerAvatar = drawer('[data-ww-drawer-avatar]');
    var drawerIcon = drawer('[data-ww-drawer-icon]');
    var drawerAdmin = drawer('[data-ww-drawer-admin]');
    var drawerLogout = drawer('[data-ww-drawer-logout]');

    link.setAttribute('href', '{{ url('/account/orders') }}');
    link.setAttribute('title', user.EMAIL || 'Tài khoản');
    if (drawerName) drawerName.textContent = user.FULL_NAME || letter;

    if (user.AVATAR_URL && drawerAvatar) {
      drawerAvatar.src = user.AVATAR_URL;
      drawerAvatar.hidden = false;
      if (drawerIcon) drawerIcon.hidden = true;
    }

    if (drawerLogout) {
      drawerLogout.hidden = false;
      drawerLogout.addEventListener('click', logout);
    }

    if (user.IS_ADMIN === true && drawerAdmin) {
      drawerAdmin.hidden = false;
      drawerAdmin.addEventListener('click', enterAdmin);
    }
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

  function logout() {
    var current = token();
    var done = function () {
      clearSession();
      window.location.reload();
    };

    if (!current) return done();

    fetch(logoutUrl, {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + current,
        'Accept': 'application/json'
      }
    }).then(done).catch(done);
  }

  function enterAdmin() {
    try {
      localStorage.setItem('AUTH_SCOPE', 'admin');
    } catch (e) {}
  }

  adminLink.addEventListener('click', enterAdmin);
  logoutBtn.addEventListener('click', logout);

  var current = token();
  if (!current) return;

  fetch(profileUrl, {
    headers: {
      'Authorization': 'Bearer ' + current,
      'Accept': 'application/json'
    }
  })
    .then(function (response) {
      if (!response.ok) throw new Error('unauthenticated');
      return response.json();
    })
    .then(function (payload) {
      var user = payload && payload.DATAS;
      if (!user || !user.ID) throw new Error('empty');
      render(user);
    })
    .catch(function () {
      clearSession();
    });
})();
</script>
