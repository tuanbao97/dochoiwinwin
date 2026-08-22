@php
  $ww = wwWebContact();
  $wwZalo = $ww['zaloUrl'] ?: $ww['zaloPageUrl'];
  $wwFacebookMsg = $ww['messengerUrl'] ?: $ww['facebookUrl'];
  $wwMapLink = $ww['mapShareUrl']
    ?: ($ww['address'] !== ''
      ? 'https://maps.google.com/?q=' . rawurlencode($ww['address'])
      : ($ww['mapUrl'] ?: '#'));
  $wwHotlineMeta = collect($ww['hotlines'])->pluck('display')->implode(' - ');
  $seoTitle = 'Liên hệ — ' . ($ww['storeName'] ?: 'Win Win');
  $seoDescription = 'Liên hệ ' . ($ww['storeName'] ?: 'Win Win')
    . ($ww['address'] !== '' ? '. Địa chỉ ' . $ww['address'] : '')
    . ($wwHotlineMeta !== '' ? '. Hotline ' . $wwHotlineMeta : '')
    . '.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  <link rel="stylesheet" href="{{ storefrontThemeAsset('contact-page.css') }}" media="all">
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
            <span class="text-neutral-100">Liên hệ</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-page" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-6">
          <div class="page-content ww-contact">
            <header class="ww-contact__header">
              <h1 class="text-h4 font-semibold mb-2">Liên hệ</h1>
              <p class="text-base text-primary font-semibold mb-0" data-ww-contact-slot="store-name">{{ $ww['storeName'] }}</p>
              @if($ww['description'] !== '')
              <p class="ww-contact__lead ww-store-description text-neutral-200 mt-2 mb-0" data-ww-contact-slot="store-description">{{ $ww['description'] }}</p>
              @endif
            </header>

            <div class="ww-contact__cards">
              @if($ww['address'] !== '')
              <a class="ww-contact__card" href="{{ $wwMapLink }}" data-ww-contact="map-link" target="_blank" rel="noopener noreferrer" title="Xem bản đồ">
                <span class="ww-contact__card-icon" aria-hidden="true"><i class="icon icon-location"></i></span>
                <span class="ww-contact__card-body">
                  <span class="ww-contact__card-label">Địa chỉ</span>
                  <span class="ww-contact__card-value" data-ww-contact-slot="address">{{ $ww['address'] }}</span>
                </span>
              </a>
              @endif

              @if(count($ww['hotlines']) > 0)
              <div class="ww-contact__card">
                <span class="ww-contact__card-icon" aria-hidden="true"><i class="icon icon-calling-phone"></i></span>
                <span class="ww-contact__card-body">
                  <span class="ww-contact__card-label">Hotline</span>
                  <span class="ww-contact__card-value" data-ww-contact-slot="hotline-list">
                    @foreach($ww['hotlines'] as $i => $hl)
                      @if($i > 0)<span> · </span>@endif
                      <a class="link text-primary font-semibold" href="{{ $hl['tel'] }}" title="{{ $hl['display'] }}">{{ $hl['display'] }}</a>
                    @endforeach
                  </span>
                </span>
              </div>
              @endif

              @if($ww['email'] !== '')
              <a class="ww-contact__card" href="mailto:{{ $ww['email'] }}" data-ww-contact="email" title="Gửi email">
                <span class="ww-contact__card-icon" aria-hidden="true"><i class="icon icon-sms"></i></span>
                <span class="ww-contact__card-body">
                  <span class="ww-contact__card-label">Email</span>
                  <span class="ww-contact__card-value" data-ww-contact-slot="email">{{ $ww['email'] }}</span>
                </span>
              </a>
              @endif

              @if($ww['workingHours'] !== '')
              <div class="ww-contact__card">
                <span class="ww-contact__card-icon" aria-hidden="true"><i class="icon icon-time"></i></span>
                <span class="ww-contact__card-body">
                  <span class="ww-contact__card-label">Giờ làm việc</span>
                  <span class="ww-contact__card-value" data-ww-contact-slot="working-hours">{{ $ww['workingHours'] }}</span>
                </span>
              </div>
              @endif
            </div>

            <div class="ww-contact__grid">
              <div class="ww-contact__form-wrap">
                <h2 class="text-base font-semibold mb-3">Gửi tin nhắn cho chúng tôi</h2>
                <div class="ww-contact__actions">
                  @if($wwZalo !== '')
                  <a
                    href="{{ $wwZalo }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    data-ww-contact="zalo"
                    data-ww-social
                    class="btn ww-contact__submit ww-contact__submit--zalo"
                    title="Gửi đến Zalo"
                  >
                    <img src="100/531/894/themes/1018832/assets/addthis-zalo.svg" width="20" height="20" alt="" decoding="async" loading="lazy">
                    <span>Gửi đến Zalo</span>
                  </a>
                  @endif
                  @if($wwFacebookMsg !== '')
                  <a
                    href="{{ $wwFacebookMsg }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    data-ww-contact="messenger"
                    data-ww-social
                    class="btn ww-contact__submit ww-contact__submit--facebook"
                    title="Gửi đến Facebook"
                  >
                    <img src="100/531/894/themes/1018832/assets/social-facebook.svg" width="20" height="20" alt="" decoding="async" loading="lazy">
                    <span>Gửi đến Facebook</span>
                  </a>
                  @endif
                </div>
                <p class="ww-contact__form-note text-sm text-neutral-200 mt-3 mb-0">
                  Chat trực tiếp qua Zalo hoặc Facebook. Hoặc gọi hotline để được hỗ trợ nhanh hơn.
                </p>
              </div>

              <div class="ww-contact__map-wrap">
                <h2 class="text-base font-semibold mb-3">Bản đồ cửa hàng</h2>
                @if($ww['mapUrl'] !== '')
                <div class="ww-contact__map">
                  <iframe
                    title="{{ $ww['storeName'] }} — Google Maps"
                    data-ww-contact="map"
                    src="{{ $ww['mapUrl'] }}"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen
                  ></iframe>
                </div>
                @endif
                @if($ww['address'] !== '')
                <p class="ww-contact__map-caption text-sm text-neutral-200 mt-2 mb-0" data-ww-contact-slot="address">
                  {{ $ww['address'] }}
                </p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.theme-portals')
  <script src="{{ storefrontThemeAsset('main.js') }}" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
</body>
</html>
