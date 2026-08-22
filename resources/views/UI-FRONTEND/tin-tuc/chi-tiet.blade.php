@php
  $newsTitle = $news['TIEU_DE_TIN_TUC'] ?? 'Chi tiết tin tức';
  $newsSummary = $news['TOM_TAT_TIN_TUC'] ?? '';
  $newsContent = $news['NOI_DUNG_TIN_TUC'] ?? '';
  $dateRaw = $news['NGAY_XUAT_BAN'] ?? $news['UPD_DT'] ?? $news['CRT_DT'] ?? null;
  $dateText = $dateRaw ? \Carbon\Carbon::parse($dateRaw)->format('d/m/Y') : '';
  $categoryName = $news['DANH_MUC_TIN_TUC']['TEN_DANH_MUC_TIN_TUC'] ?? '';

  $heroImg = null;
  $avatars = $news['DANH_SACH_HINH_ANH_DAI_DIEN'] ?? [];
  $updTime = $news['UPD_DT'] ?? $news['CRT_DT'] ?? null;
  if (is_array($avatars) && count($avatars) > 0) {
    $avatar = $avatars[0];
    $bust = $updTime ?? ($avatar['UPD_DT'] ?? null);
    if (! empty($avatar['PATH'])) {
      $heroImg = storefrontImageUrl($avatar['PATH'], $bust);
    } else {
      $ratio = $avatar['ASPECT_RATIO'] ?? '1x1';
      $name = $avatar['NAME'] ?? '';
      $dir = $avatar['DIRECTORY'] ?? '';
      if ($dir !== '' && $name !== '') {
        $heroImg = storefrontImageUrl($dir . '/' . $ratio . '_' . $name, $bust);
      }
    }
  }

  $attachments = $news['DANH_SACH_FILE_DINH_KEM'] ?? [];

  $seoTitle = $newsTitle . ' — Win Win';
  $seoDescription = $newsSummary !== ''
    ? $newsSummary
    : 'Tin tức Đồ Chơi Win Win';
  $seoImage = $heroImg
    ? storefrontAbsoluteUrl($heroImg)
    : (is_array($avatars) && isset($avatars[0]) && is_array($avatars[0])
      ? storefrontMediaImageUrl($avatars[0], $updTime, true)
      : null);
  $seoType = 'article';
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
            <a class="link" href="{{ url('tin-tuc') }}" title="Tin tức"><span>Tin tức</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          <li>
            <span class="text-neutral-100 line-clamp-1">{{ $newsTitle }}</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-article" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container md:px-gutter px-0 article-wraper" itemscope itemtype="https://schema.org/Article">
        <meta itemprop="headline" content="{{ $newsTitle }}">
        @if ($newsSummary !== '')
          <meta itemprop="description" content="{{ $newsSummary }}">
        @endif
        @if ($heroImg)
          <meta itemprop="image" content="{{ $heroImg }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[calc(100%_-_32.8rem)_32rem] gap-5">
          <div class="w-full">
            <div class="bg-background md:rounded-sm p-3 md:p-6 mb-6">
              <h1 class="text-h4 font-semibold mb-2">{{ $newsTitle }}</h1>

              <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
                @if ($dateText !== '')
                  <div class="cart-article__date text-neutral-200 flex gap-1 items-center whitespace-nowrap">
                    <i class="icon icon-calendar"></i>
                    {{ $dateText }}
                  </div>
                @endif
                @if ($categoryName !== '')
                  <span class="text-xs md:text-sm text-secondary font-semibold">{{ $categoryName }}</span>
                @endif
              </div>

              @if ($heroImg)
                <figure class="ww-news-hero mb-5 rounded-sm overflow-hidden">
                  <img
                    src="{{ $heroImg }}"
                    alt="{{ $newsTitle }}"
                    class="w-full h-auto object-contain"
                    loading="eager"
                  >
                </figure>
              @endif

              @if ($newsSummary !== '')
                <p class="text-base text-neutral-300 font-medium mb-4">{{ $newsSummary }}</p>
              @endif

              <div class="rte prose text-base w-full max-w-full content article-content">
                {!! $newsContent !!}
              </div>

              @if (is_array($attachments) && count($attachments) > 0)
                <div class="mt-8 pt-6 border-t border-neutral-50">
                  <h2 class="text-base font-semibold mb-3">Tài liệu đính kèm</h2>
                  <ul class="flex flex-wrap gap-2">
                    @foreach ($attachments as $file)
                      @php
                        $fileName = $file['ORIGINAL_NAME'] ?? $file['NAME'] ?? 'Tải file';
                        $filePath = $file['PATH'] ?? '';
                        $fileUrl = $filePath !== '' ? asset($filePath) : '#';
                      @endphp
                      <li>
                        <a
                          href="{{ $fileUrl }}"
                          class="btn text-sm font-semibold text-secondary border border-secondary hover:bg-secondary hover:text-background px-3 py-2 rounded-sm"
                          target="_blank"
                          rel="noopener noreferrer"
                          download
                        >{{ $fileName }}</a>
                      </li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>

            @if (!empty($relatedNews))
              <div class="bg-background md:rounded-sm p-3 md:p-6">
                <h2 class="text-base font-semibold mb-4">Tin liên quan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  @foreach ($relatedNews as $relatedItem)
                    @include('UI-FRONTEND.tin-tuc.partials.news-card-compact', ['news' => $relatedItem])
                  @endforeach
                </div>
              </div>
            @endif
          </div>

          @include('UI-FRONTEND.tin-tuc.partials.news-sidebar', [
            'categories' => $categories,
            'hotNews' => $hotNews,
            'activeCategoryId' => (int) ($news['DANH_MUC_TIN_TUC']['ID'] ?? 0) ?: null,
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
