@php
  $seoTitle = 'Video — Win Win';
  $seoDescription = 'Video giới thiệu sản phẩm, giỏ quà và hoạt động tại Đồ Chơi Win Win.';
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
            <span class="text-neutral-100">Video</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section section-main-videos" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-4">
          <div class="text-center md:text-left mb-4 md:mb-6">
            <h1 class="text-h4 font-semibold mb-1">Video</h1>
            @if ($total > 0)
              <p class="text-neutral-400 text-sm md:text-base mb-0">
                {{ number_format($total, 0, ',', '.') }} video
              </p>
            @endif
          </div>

          @if ($total === 0)
            <p class="text-center text-sm text-neutral-400 py-10 mb-0">Chưa có video nào.</p>
          @else
            <div class="ww-home-videos__grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
              @foreach ($videoList as $videoItem)
                @include('UI-FRONTEND.video.partials.video-card', [
                  'video' => $videoItem,
                  'appUrl' => $appUrl,
                ])
              @endforeach
            </div>

            @if ($totalPages > 1)
              <nav class="flex justify-center gap-2 mt-6 pt-6 flex-wrap" aria-label="Phân trang">
                @for ($p = 1; $p <= $totalPages; $p++)
                  <a
                    href="{{ storefrontVideoListUrl($p) }}"
                    class="btn px-3 py-1.5 rounded-sm border border-neutral-50 text-sm font-semibold {{ $p === $page ? 'bg-primary text-white border-primary' : 'hover:bg-neutral-50' }}"
                  >{{ $p }}</a>
                @endfor
              </nav>
            @endif
          @endif
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.video.partials.video-player-modal')
  @include('UI-FRONTEND.common.theme-portals')
  <script src="100/531/894/themes/1018832/assets/main.js?ww-video-1" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="100/531/894/themes/1018832/assets/defer-scripts.js?ww-video-1" defer fetchpriority="low"></script>
</body>
</html>
