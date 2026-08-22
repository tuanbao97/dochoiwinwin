@php
  $seoTitle = ($categoryName !== '' ? $categoryName : ($query !== '' ? 'Tìm kiếm: ' . $query : 'Tìm kiếm')) . ' — Win Win';
  $seoDescription = $categoryName !== ''
    ? ('Sản phẩm ' . $categoryName . ' tại Đồ Chơi Win Win.')
    : ($query !== ''
      ? ('Kết quả tìm kiếm “' . $query . '” tại Đồ Chơi Win Win.')
      : 'Tìm kiếm sản phẩm tại Đồ Chơi Win Win.');
  $seoImage = storefrontDefaultShareImageUrl();
  $seoImageWidth = 1024;
  $seoImageHeight = 618;
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme search">@include('UI-FRONTEND.common.header')

  <main>
    <div class="breadcrumbs">
      <div class="container">
        <ul class="breadcrumb py-3 flex flex-wrap items-center text-xs md:text-sm">
          <li class="home">
            <a class="link" href="{{ url('/') }}" title="Trang chủ"><span>Trang chủ</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          @if ($categoryName !== '')
            <li>
              <span class="text-neutral-100">{{ $categoryName }}</span>
            </li>
          @else
            <li><span class="text-neutral-100">Tìm kiếm</span></li>
          @endif
        </ul>
      </div>
    </div>

    <div class="index-container container py-6 px-0 xl:grid xl:grid-cols-[300px_calc(100%-312px)] gap-3">
      @include('UI-FRONTEND.partials.home-category-sidebar')

      <div class="min-w-0 px-3 xl:px-0">
        @include('UI-FRONTEND.partials.listing-topics')

        <section class="section section-main-search" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
          <div class="bg-background rounded-lg px-3 py-4 md:p-6 mb-4 text-center">
            <h1 class="text-h4 font-semibold mb-2">
              @if ($categoryName !== '')
                {{ $categoryName }}
              @else
                Kết quả tìm kiếm
              @endif
            </h1>
            @if ($query !== '')
              <p class="text-neutral-400 text-sm md:text-base">
                Từ khóa: <strong class="text-foreground">&ldquo;{{ $query }}&rdquo;</strong>
                <span data-search-total-wrap class="{{ $total > 0 ? '' : 'hidden' }}">
                  — <span id="search-total-count">{{ $total > 0 ? number_format($total, 0, ',', '.') : '' }}</span> sản phẩm
                </span>
              </p>
            @else
              <p class="text-neutral-400 text-sm md:text-base {{ $total > 0 ? '' : 'hidden' }}" data-search-total-wrap>
                <span id="search-total-count">{{ $total > 0 ? number_format($total, 0, ',', '.') : '' }}</span> sản phẩm
              </p>
            @endif
          </div>

          @if ($query !== '' || $categoryId || !empty($productHot) || !empty($productVip) || !empty($listAll))
          <div class="ww-search-layout">
            {{-- Filter giá danh sách sản phẩm (tạm ẩn)
            <div class="ww-search-filter-card ww-search-filter-card--inline mb-4 md:mb-5">
              <div class="ww-search-filter-card__row">
                <div class="ww-search-filter-card__head">
                  <span class="ww-search-filter-card__icon" aria-hidden="true">
                    <i class="icon icon-dollar-circle"></i>
                  </span>
                  <h2 class="ww-search-filter-card__title">Chọn mức giá</h2>
                </div>

                <ul class="ww-search-price-filters ww-search-price-filters--inline">
                  @foreach ($priceFilterOptions as $option)
                    <li>
                      <label class="ww-search-price-chip">
                        <input
                          type="checkbox"
                          class="ww-search-price-checkbox"
                          name="gia"
                          value="{{ $option['id'] }}"
                          data-min="{{ $option['min'] ?? '' }}"
                          data-max="{{ $option['max'] ?? '' }}"
                          @checked(in_array($option['id'], $selectedPriceFilterIds, true))
                        >
                        <span class="ww-search-price-chip__inner">
                          <span class="ww-search-price-chip__label">{{ $option['label'] }}</span>
                          <span class="ww-search-price-chip__tick" aria-hidden="true">
                            <i class="icon icon-tick"></i>
                          </span>
                        </span>
                      </label>
                    </li>
                  @endforeach
                </ul>

                <button type="button" class="ww-search-price-clear" id="ww-search-price-clear" hidden>
                  <i class="icon icon-refresh" aria-hidden="true"></i>
                  <span class="ww-search-price-clear__text">Xóa</span>
                </button>
              </div>
            </div>
            --}}

            <ul class="heading-tabs heading-tabs--scroll mb-4 md:mb-6 w-full max-w-full overflow-x-auto list-none flex md:gap-3 gap-2 font-semibold whitespace-nowrap" id="search-sort-tabs">
              <li
                class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2 {{ ($boLoc ?? 'default') === 'default' ? 'active' : '' }}"
                data-bo-loc="default"
              >Tất cả</li>
              <li
                class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2 {{ ($boLoc ?? '') === 'gia-tang' ? 'active' : '' }}"
                data-bo-loc="gia-tang"
              >Giá tăng dần</li>
              <li
                class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2 {{ ($boLoc ?? '') === 'gia-giam' ? 'active' : '' }}"
                data-bo-loc="gia-giam"
              >Giá giảm dần</li>
              <li
                class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2 {{ ($boLoc ?? '') === 'a-z' ? 'active' : '' }}"
                data-bo-loc="a-z"
              >Tên từ A-Z</li>
              <li
                class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2 {{ ($boLoc ?? '') === 'z-a' ? 'active' : '' }}"
                data-bo-loc="z-a"
              >Tên từ Z-A</li>
            </ul>

            <div id="search-results-grid" class="product-list grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-2">
              @if ($total === 0)
                <p class="col-span-full text-center text-sm text-slate-600 py-8">Không tìm thấy sản phẩm phù hợp.</p>
              @else
                <p class="col-span-full text-center text-sm text-slate-500 py-4">Đang tải sản phẩm...</p>
              @endif
            </div>

            <nav id="search-pagination" class="flex justify-center gap-2 mt-6 flex-wrap" aria-label="Phân trang"></nav>
          </div>
          @else
          <div id="search-results-grid" class="product-list grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-2">
            <p class="col-span-full text-center text-sm text-slate-600 py-8">Nhập từ khóa để tìm sản phẩm.</p>
          </div>
          @endif
        </section>
      </div>
    </div>
  </main>

  @include('UI-FRONTEND.common.theme-portals')
  @include('UI-FRONTEND.partials.search-products-ajax')
  <script src="{{ storefrontThemeAsset('main.js') }}" defer fetchpriority="low"></script>
  <script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
</body>
</html>
