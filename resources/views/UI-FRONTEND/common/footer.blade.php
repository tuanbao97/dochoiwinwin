@php
  $ww = wwWebContact();
  $wwZalo = $ww['zaloUrl'] ?: $ww['zaloPageUrl'];
  $wwMessenger = $ww['messengerUrl'] ?: $ww['facebookUrl'];
  $hasSocial = $ww['facebookUrl'] !== '' || $wwMessenger !== '' || $wwZalo !== '' || $ww['tiktokUrl'] !== '' || $ww['youtubeUrl'] !== '';
@endphp
<footer class="ww-ft">
  <div class="container ww-ft__inner">
    <div class="ww-ft__grid">
      {{-- Brand + liên hệ --}}
      <div class="ww-ft__col ww-ft__col--brand">
        <a class="ww-ft__brand" href="{{ url('/') }}" title="{{ $ww['storeName'] }}">
          <img
            class="ww-ft__logo"
            loading="lazy"
            src="{{ storefrontLogoUrl() }}"
            alt="{{ $ww['storeName'] }}"
            width="72"
            height="72"
          >
          <span class="ww-ft__brand-text">
            <strong class="ww-ft__name" data-ww-contact-slot="store-name">{{ $ww['storeName'] }}</strong>
            @if($ww['taxCode'] !== '')
              <span class="ww-ft__tax">MST: <span data-ww-contact-slot="tax-code">{{ $ww['taxCode'] }}</span></span>
            @endif
          </span>
        </a>

        @if($ww['description'] !== '')
          <div class="ww-ft__desc ww-store-description" data-ww-contact-slot="store-description">{{ $ww['description'] }}</div>
        @endif

        <ul class="ww-ft__contacts">
          @if($ww['address'] !== '')
            <li>
              <span class="ww-ft__ico" aria-hidden="true"><i class="icon icon-location"></i></span>
              <div>
                <span class="ww-ft__label">Địa chỉ</span>
                <p class="ww-ft__value" data-ww-contact-slot="address">{{ $ww['address'] }}</p>
              </div>
            </li>
          @endif

          @if(count($ww['hotlines']) > 0)
            <li>
              <span class="ww-ft__ico" aria-hidden="true"><i class="icon icon-call"></i></span>
              <div>
                <span class="ww-ft__label">Hotline</span>
                <div class="ww-ft__phones" data-ww-contact-slot="hotline-list">
                  @foreach($ww['hotlines'] as $i => $hl)
                    <a
                      class="ww-ft__phone"
                      href="{{ $hl['tel'] }}"
                      @if($i === 0) data-ww-contact="hotline" @endif
                      title="{{ $hl['display'] }}"
                    >{{ $hl['display'] }}</a>
                  @endforeach
                </div>
              </div>
            </li>
          @endif

          @if($ww['email'] !== '')
            <li>
              <span class="ww-ft__ico" aria-hidden="true"><i class="icon icon-sms"></i></span>
              <div>
                <span class="ww-ft__label">Email</span>
                <a
                  class="ww-ft__value ww-ft__link"
                  href="mailto:{{ $ww['email'] }}"
                  data-ww-contact="email"
                  data-ww-contact-fill-text
                  title="{{ $ww['email'] }}"
                >{{ $ww['email'] }}</a>
              </div>
            </li>
          @endif
        </ul>

        @if($hasSocial)
          <div class="ww-ft__social">
            <p class="ww-ft__heading">Mạng xã hội</p>
            <div class="ww-ft__social-list">
              @if($ww['facebookUrl'] !== '')
                <a
                  href="{{ $ww['facebookUrl'] }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  data-ww-contact="facebook"
                  data-ww-social
                  class="ww-ft__social-btn"
                  aria-label="Facebook"
                  title="Facebook"
                >
                  <img src="100/531/894/themes/1018832/assets/social-facebook.svg" width="22" height="22" alt="" decoding="async" loading="lazy">
                </a>
              @endif

              @if($wwMessenger !== '')
                <a
                  href="{{ $wwMessenger }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  data-ww-contact="messenger"
                  data-ww-social
                  class="ww-ft__social-btn"
                  aria-label="Messenger"
                  title="Messenger"
                >
                  <img src="100/531/894/themes/1018832/assets/addthis-messenger.svg" width="22" height="22" alt="" decoding="async" loading="lazy">
                </a>
              @endif

              @if($wwZalo !== '')
                <a
                  href="{{ $wwZalo }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  data-ww-contact="zalo"
                  data-ww-social
                  class="ww-ft__social-btn"
                  aria-label="Zalo"
                  title="Zalo"
                >
                  <img src="100/531/894/themes/1018832/assets/addthis-zalo.svg" width="22" height="22" alt="" decoding="async" loading="lazy">
                </a>
              @endif

              @if($ww['tiktokUrl'] !== '')
                <a
                  href="{{ $ww['tiktokUrl'] }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  data-ww-contact="tiktok"
                  data-ww-social
                  class="ww-ft__social-btn"
                  aria-label="TikTok"
                  title="TikTok"
                >
                  <img src="100/531/894/themes/1018832/assets/social-tiktok.svg" width="22" height="22" alt="" decoding="async" loading="lazy">
                </a>
              @endif

              @if($ww['youtubeUrl'] !== '')
                <a
                  href="{{ $ww['youtubeUrl'] }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  data-ww-contact="youtube"
                  data-ww-social
                  class="ww-ft__social-btn"
                  aria-label="YouTube"
                  title="YouTube"
                >
                  <img src="100/531/894/themes/1018832/assets/social-youtube.svg" width="22" height="22" alt="" decoding="async" loading="lazy">
                </a>
              @endif
            </div>
          </div>
        @endif
      </div>

      {{-- Chính sách + hỗ trợ --}}
      <div class="ww-ft__col ww-ft__col--links">
        <details open class="ww-ft__details footer-details">
          <summary class="ww-ft__heading">
            Chính sách
            <i class="icon icon-carret-right ww-ft__caret md:hidden" aria-hidden="true"></i>
          </summary>
          <ul class="ww-ft__menu">
            <li>
              <a class="ww-ft__menu-link" href="{{ url('/chinh-sach-bao-hanh') }}" title="Chính sách bảo hành">Chính sách bảo hành</a>
            </li>
            <li>
              <a class="ww-ft__menu-link" href="{{ url('/chinh-sach-thanh-toan') }}" title="Chính sách thanh toán">Chính sách thanh toán</a>
            </li>
          </ul>
        </details>

        @if(count($ww['hotlines']) > 0)
          <div class="ww-ft__support">
            <p class="ww-ft__heading">Tổng đài hỗ trợ</p>
            <div class="ww-ft__support-phones">
              @foreach($ww['hotlines'] as $i => $hl)
                <a
                  class="ww-ft__support-phone"
                  href="{{ $hl['tel'] }}"
                  @if($i === 0) data-ww-contact="hotline" @endif
                  title="{{ $hl['display'] }}"
                >
                  <i class="icon icon-call" aria-hidden="true"></i>
                  <span @if($i === 0) data-ww-contact-slot="hotline-number" @endif>{{ $hl['display'] }}</span>
                </a>
              @endforeach
            </div>
            @if($ww['workingHours'] !== '')
              <p class="ww-ft__hours" data-ww-contact-slot="working-hours">{{ $ww['workingHours'] }}</p>
            @endif
          </div>
        @endif

        <div class="ww-ft__moit">
          <span
            class="ww-ft__moit-link"
            title="Đã thông báo Bộ Công Thương"
            aria-label="Đã thông báo Bộ Công Thương"
          >
            <img
              src="{{ asset('UI-FRONTEND/images/logo-bo-cong-thuong.png') }}"
              alt="Đã thông báo Bộ Công Thương"
              width="150"
              height="57"
              loading="lazy"
              decoding="async"
            >
          </span>
        </div>
      </div>

      {{-- Bản đồ --}}
      <div class="ww-ft__col ww-ft__col--map">
        <p class="ww-ft__heading">Bản đồ cửa hàng</p>
        @if($ww['mapUrl'] !== '')
          <div class="ww-ft__map ww-footer-map">
            <iframe
              data-ww-contact="map"
              src="{{ $ww['mapUrl'] }}"
              width="600"
              height="300"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="{{ $ww['storeName'] }} — Google Maps"
            ></iframe>
          </div>
        @endif

        @if($ww['address'] !== '')
          <div class="ww-ft__map-address">
            <span class="ww-ft__ico" aria-hidden="true"><i class="icon icon-location"></i></span>
            <p>
              <span class="ww-ft__label">Địa chỉ</span>
              <span class="ww-ft__value" data-ww-contact-slot="address">{{ $ww['address'] }}</span>
            </p>
          </div>
        @endif
      </div>
    </div>

    <div class="ww-ft__bottom footer-copyright">
      <span class="ww-ft__commitment" data-ww-contact-slot="commitment-text">
        {{ $ww['commitmentText'] !== '' ? $ww['commitmentText'] : $ww['storeName'] }}
      </span>
    </div>
  </div>
</footer>
@include('UI-FRONTEND.common.winwin-contact-settings')
