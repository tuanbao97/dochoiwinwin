@php
  $seoTitle = 'Giới thiệu — Win Win';
  $seoDescription = '🎁 ĐỒ CHƠI WIN WIN — Chuyên đồ chơi chính hãng, chất lượng. Xe điều khiển • Máy bay RC • Đồ chơi giáo dục • Đồ chơi Búp bê • Đồ chơi vỉ giá rẻ. Hotline 0905 454 775 - 0905 09 09 10.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  <link rel="stylesheet" href="{{ storefrontThemeAsset('about-page.css') }}" media="all">
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
            <span class="text-neutral-100">Giới thiệu</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-page" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-custom justify-center gap-gutter" style="--grid-col: 80%">
          <div>
            <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-6">
              <div class="page-content ww-about">
                <div class="rte">
                  <div class="prose text-base w-full max-w-full content">
                    <header class="ww-about__header">
                      <h1 class="text-h4 font-semibold mb-2">Giới thiệu</h1>
                      <p class="text-base text-primary font-semibold mb-0" data-ww-contact-slot="store-name">Đồ Chơi Win Win</p>
                    </header>

                    <div class="ww-about__grid">
                      <figure class="ww-about__media">
                        <div class="ww-about__img-wrap">
                          <img
                            src="{{ asset('UI-FRONTEND/images/win-win-cua-hang.png') }}"
                            alt="Đồ Chơi Win Win — chuyên đồ chơi chính hãng, chất lượng"
                            loading="lazy"
                            width="1200"
                            height="1600"
                          >
                        </div>
                        <figcaption class="ww-about__caption text-sm text-neutral-200" data-ww-contact-slot="address">
                          Cửa hàng Win Win — Đường DT605, xã Hòa Tiến, Đà Nẵng
                        </figcaption>
                      </figure>

                      <div class="ww-about__body">
                        <div class="ww-about__intro" data-ww-contact-slot="about-html">
                          <p>🎁 <strong>ĐỒ CHƠI WIN WIN</strong></p>
                          <p>🚀 Chuyên đồ chơi chính hãng, chất lượng</p>
                          <p>🧸 Xe điều khiển • Máy bay RC • Đồ chơi giáo dục • Đồ chơi Búp bê • Đồ chơi vỉ giá rẻ</p>
                          <p>📦 Hàng sẵn kho – Giao nhanh toàn quốc</p>
                          <p>💬 Tư vấn tận tâm – Hỗ trợ nhiệt tình</p>
                        </div>

                        <div class="ww-about__contact">
                          <h2 class="text-base font-semibold mb-3">Thông tin liên hệ</h2>
                          <ul class="ww-about__contact-list">
                            <li>
                              <span class="ww-about__label">Địa chỉ</span>
                              <span class="ww-about__value" data-ww-contact-slot="address">Đường DT605, xã Hòa Tiến, Đà Nẵng (đối diện Trường Tiểu học số 2 Hòa Tiến)</span>
                            </li>
                            <li>
                              <span class="ww-about__label">Hotline</span>
                              <span class="ww-about__value" data-ww-contact-slot="hotline-list">
                                @foreach(wwWebContact()['hotlines'] as $i => $hl)
                                  @if($i > 0)<span> · </span>@endif
                                  <a class="link text-primary font-semibold" href="{{ $hl['tel'] }}" title="{{ $hl['display'] }}">{{ $hl['display'] }}</a>
                                @endforeach
                              </span>
                            </li>
                            <li>
                              <span class="ww-about__label">Email</span>
                              <span class="ww-about__value">
                                <a class="link text-primary font-semibold" href="mailto:dochoiwinwin@gmail.com" data-ww-contact="email" data-ww-contact-fill-text title="dochoiwinwin@gmail.com">dochoiwinwin@gmail.com</a>
                              </span>
                            </li>
                            <li>
                              <span class="ww-about__label">Website</span>
                              <span class="ww-about__value">
                                <a class="link text-primary font-semibold" href="https://dochoiwinwin.com" data-ww-contact="website" data-ww-contact-fill-text target="_blank" rel="noopener noreferrer" title="dochoiwinwin.com">dochoiwinwin.com</a>
                              </span>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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
