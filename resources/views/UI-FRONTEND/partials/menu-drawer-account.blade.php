@php
  $wwDrawerUser = $storefrontUser ?? null;
  $wwDrawerInitial = $wwDrawerUser
    ? mb_strtoupper(mb_substr(trim((string) ($wwDrawerUser['FULL_NAME'] ?: $wwDrawerUser['EMAIL'] ?: '?')), 0, 1))
    : '';
@endphp

<div class="ww-drawer-account" data-ww-drawer-account-root>
  <a
    href="{{ url('/account/login') }}?redirect={{ urlencode(url()->current()) }}"
    title="Đăng nhập"
    class="ww-drawer-account__guest"
    data-ww-drawer-guest
    @if($wwDrawerUser) hidden @endif
  >
    <span class="ww-drawer-account__avatar ww-drawer-account__avatar--icon">
      <i class="icon icon-user" aria-hidden="true"></i>
    </span>
    <span class="ww-drawer-account__meta">
      <span class="ww-drawer-account__label">Tài khoản</span>
      <span class="ww-drawer-account__name">Đăng nhập</span>
    </span>
  </a>

  <div class="ww-drawer-account__user" data-ww-drawer-user @unless($wwDrawerUser) hidden @endunless>
    <div class="ww-drawer-account__head">
      <span class="ww-drawer-account__avatar">
        <img
          class="ww-drawer-account__avatar-img"
          alt=""
          data-ww-drawer-avatar
          referrerpolicy="no-referrer"
          @if($wwDrawerUser && $wwDrawerUser['AVATAR_URL']) src="{{ $wwDrawerUser['AVATAR_URL'] }}" @else hidden @endif
        >
        <span
          class="ww-drawer-account__initial"
          data-ww-drawer-initial
          @if($wwDrawerUser && $wwDrawerUser['AVATAR_URL']) hidden @endif
        >{{ $wwDrawerInitial }}</span>
      </span>
      <div class="ww-drawer-account__meta">
        <p class="ww-drawer-account__name" data-ww-drawer-name>{{ $wwDrawerUser['FULL_NAME'] ?? '' }}</p>
        <p class="ww-drawer-account__email" data-ww-drawer-email>{{ $wwDrawerUser['EMAIL'] ?? '' }}</p>
      </div>
    </div>

    <ul class="ww-drawer-account__menu">
      <li data-ww-drawer-admin @unless($wwDrawerUser && $wwDrawerUser['IS_ADMIN']) hidden @endunless>
        <a
          class="ww-drawer-account__link ww-drawer-account__link--admin"
          href="{{ url('/admin/san-pham/danh-sach') }}"
          data-ww-drawer-admin-link
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 4H10V10H4V4ZM14 4H20V10H14V4ZM4 14H10V20H4V14ZM14 14H20V20H14V14Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          </svg>
          <span>Quản lý</span>
        </a>
      </li>
      <li>
        <a class="ww-drawer-account__link" href="{{ url('/account/orders') }}" data-ww-drawer-account>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 3H18V21L15 19L12 21L9 19L6 21V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 8H15M9 12H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          <span>Lịch sử mua hàng</span>
        </a>
      </li>
      <li>
        <a class="ww-drawer-account__link" href="{{ url('/cart') }}">
          <i class="icon icon-cart" aria-hidden="true"></i>
          <span>Giỏ hàng của tôi</span>
        </a>
      </li>
      <li>
        <a class="ww-drawer-account__link" href="{{ url('/account/profile') }}">
          <i class="icon icon-user" aria-hidden="true"></i>
          <span>Thông tin cá nhân</span>
        </a>
      </li>
      <li>
        <button type="button" class="ww-drawer-account__link ww-drawer-account__link--logout" data-ww-drawer-logout>
          <i class="icon icon-user" aria-hidden="true"></i>
          <span>Đăng xuất</span>
        </button>
      </li>
    </ul>
  </div>
</div>
