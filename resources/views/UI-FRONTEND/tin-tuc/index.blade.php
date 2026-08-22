@php
  $seoTitle = 'Tin tức — Win Win';
  $seoDescription = 'Tin tức, mẹo hay và chia sẻ về cách chọn đồ chơi an toàn, quà tặng cho bé từ Win Win.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme blog">
  <link rel="stylesheet" href="{{ storefrontThemeAsset('article-style.css') }}" media="all">
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
            <span class="text-neutral-100">Tin tức</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section section-main-blog" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-[calc(100%_-_32.8rem)_32rem] gap-5">
          <div>
            <div class="text-center md:text-left">
              <h1 class="heading font-semibold text-secondary">Tin tức</h1>
            </div>

            <div class="article-list">
              @if ($total === 0)
                <p class="text-center text-sm text-neutral-400 py-10">Chưa có bài viết nào.</p>
              @else
                <div class="grid grid-cols-2 xl:grid-cols-3 lg:grid-cols-2 gap-2 mt-2">
                  @foreach ($newsList as $newsItem)
                    @include('UI-FRONTEND.tin-tuc.partials.news-card', ['news' => $newsItem])
                  @endforeach
                </div>

                @if ($totalPages > 1)
                  <nav class="flex justify-center gap-2 mt-6 pt-6 flex-wrap" aria-label="Phân trang" style="grid-column: 1 / -1">
                    @for ($p = 1; $p <= $totalPages; $p++)
                      <a
                        href="{{ storefrontNewsListUrl($p, $categoryKey ?? null) }}"
                        class="btn px-3 py-1.5 rounded-sm border border-neutral-50 text-sm font-semibold {{ $p === $page ? 'bg-primary text-white border-primary' : 'hover:bg-neutral-50' }}"
                      >{{ $p }}</a>
                    @endfor
                  </nav>
                @endif
              @endif
            </div>
          </div>

          @include('UI-FRONTEND.tin-tuc.partials.news-sidebar', [
            'categories' => $categories,
            'hotNews' => $hotNews,
            'activeCategoryId' => $categoryId,
          ])
        </div>
      </div>
    </section>
  </main>

  <script src="{{ storefrontThemeAsset('main.js') }}"></script>
  @include('UI-FRONTEND.common.theme-portals')
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
</body>
</html>
