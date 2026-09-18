@php
  $wwInviteUser = $storefrontUser ?? null;
  $wwIsLoginPage = request()->is('account/login*');
  $wwShowLoginInvite = ! $wwInviteUser && ! $wwIsLoginPage;

  // Tạo/chạm session Laravel ngay khi khách mới vào (chưa đăng nhập).
  if ($wwShowLoginInvite && request()->hasSession()) {
      request()->session()->put('ww_storefront_guest', true);
  }

  $wwInviteRedirect = url()->current();
  $wwInviteGoogle = route('social.redirect', ['provider' => 'google']).'?redirect='.urlencode($wwInviteRedirect);
  $wwInviteFacebook = route('social.redirect', ['provider' => 'facebook']).'?redirect='.urlencode($wwInviteRedirect);
@endphp

@if ($wwShowLoginInvite)
<login-invite-popup
  class="portal portal--modal"
  id="login-invite-popup"
  data-type="modal"
  data-animation="fade-in"
  data-storage-key="wwLoginInviteSeen"
>
  <dialog class="portal-dialog">
    <div class="ww-login-invite-popup__wrap flex items-center justify-center w-full h-full p-3">
      <div class="portal-overlay"></div>
      <div class="portal-inner animation ww-login-invite-popup">
        <button
          type="button"
          id="PortalClose-login-invite-popup"
          class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform"
          title="Đóng"
          aria-label="Đóng"
        >
          <i class="icon icon-cross"></i>
        </button>

        <div class="ww-login-invite-popup__body">
          <div class="ww-login-invite">
            <h2 class="ww-login-invite__title ww-login-invite__title--popup">
              ĐĂNG NHẬP MUA SẮM
            </h2>
            <p class="ww-login-invite__text ww-login-invite__text--orange">NHẬN NGAY VOUCHER GIẢM GIÁ</p>
          </div>

          <div class="ww-login-invite-vouchers" aria-hidden="true">
            <div class="ww-login-invite-vouchers__track">
              @php
                $wwInviteVouchers = ['FREESHIP', 'GIẢM 10%', 'GIẢM 20%', 'GIẢM 50%', 'GIẢM 100K'];
              @endphp
              @foreach ([1, 2] as $loopPass)
                @foreach ($wwInviteVouchers as $voucherLabel)
                  <span class="ww-login-invite-vouchers__item">{{ $voucherLabel }}</span>
                @endforeach
              @endforeach
            </div>
          </div>

          <div class="ww-login-invite-popup__actions grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a
              href="{{ $wwInviteGoogle }}"
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
              href="{{ $wwInviteFacebook }}"
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
        </div>
      </div>
    </div>
  </dialog>
</login-invite-popup>
@endif
