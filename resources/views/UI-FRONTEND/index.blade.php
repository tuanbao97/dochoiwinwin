<!doctype html>
<html lang="vi">
  <head>
    <base href="{{ rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') }}/UI-FRONTEND/">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="canonical" href="{{ url('/') }}">
    <meta name="revisit-after" content="2 days">

    <meta name="robots" content="noodp,index,follow">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @php
      $wwHome = wwWebContact();
      $seoTitle = $wwHome['storeName'] ?: 'Đồ Chơi Win Win';
      $seoDescription = $wwHome['description'] !== ''
        ? $wwHome['description']
        : '🎁 ĐỒ CHƠI WIN WIN — 🚀 Chuyên đồ chơi chính hãng, chất lượng. 🧸 Xe điều khiển • Máy bay RC • Đồ chơi giáo dục • Đồ chơi Búp bê • Đồ chơi vỉ giá rẻ. 📦 Hàng sẵn kho – Giao nhanh toàn quốc. 💬 Tư vấn tận tâm – Hỗ trợ nhiệt tình';
      $seoType = 'website';
      $seoUrl = url('/');
      $seoImage = storefrontDefaultShareImageUrl();
      $seoImageWidth = 1024;
      $seoImageHeight = 618;
    @endphp
    @include('UI-FRONTEND.common.og-meta')

    <script>
      var Bizweb = Bizweb || {};
      Bizweb.store = @json(config('services.sapo.store') ?: 'thegioisuawinwin.mysapo.net');
      Bizweb.id = 531894;
      Bizweb.theme = { id: 1018832, name: 'Đồ Chơi Win Win', role: 'main' };
      Bizweb.template = 'index';
      if (!Bizweb.fbEventId) {
        Bizweb.fbEventId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
          var r = (Math.random() * 16) | 0;
          var v = c == 'x' ? r : (r & 0x3) | 0x8;
          return v.toString(16);
        });
      }
    </script>
    {{-- Tắt Sapo Apps (reviews/aff/combo): CDN store cũ gây ERR_CONNECTION_RESET --}}
    <script>
      window.BizwebAnalytics = window.BizwebAnalytics || {};
      window.BizwebAnalytics.meta = window.BizwebAnalytics.meta || {};
      window.BizwebAnalytics.meta.currency = 'VND';
      window.BizwebAnalytics.tracking_url = '/s';
      var meta = {};
      for (var attr in meta) {
        window.BizwebAnalytics.meta[attr] = meta[attr];
      }
    </script>


    {{-- Tắt stats.min.js (Bizweb): gọi /s/api/v1/* và /cart/add.js không tồn tại trên Laravel --}}
    @if (false && !app()->environment('local'))
      <script src="dist/js/stats.min.js?v=96f2ff2"></script>
    @endif

    <meta
      name="keywords"
      content="đồ chơi chính hãng, xe điều khiển, máy bay RC, đồ chơi giáo dục, đồ chơi vỉ giá rẻ, Đồ Chơi Win Win"
    >
    <link rel="preconnect" href="https://bizweb.dktcdn.net">
    <link rel="preconnect" href="https://egany.com">
    <link rel="preconnect" href="https://egany.com" crossorigin="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preload" as="script" media="all" href="100/531/894/themes/1018832/assets/vendors.js?1768901692132">

    <script src="100/531/894/themes/1018832/assets/vendors.js?1768901692132" type="text/javascript"></script>
    <link rel="preload" as="script" media="all" href="100/531/894/themes/1018832/assets/jquery.js?1768901692132">
    <script src="100/531/894/themes/1018832/assets/jquery.js?1768901692132" type="text/javascript"></script>
    <script src="assets/themes_support/api.jquery.js" type="text/javascript"></script>

    <!-- Bizweb javascript customer -->
    <script>
      // Prefix absolute theme API paths (e.g. "/cart/add.js") with the app base URL.
      // Prevents 404 when the app is served from a subdirectory.
      window.themeUrl = function (path) {
        const base = "{{ rtrim(url('/'), '/') }}";
        if (!path) return base;
        if (typeof path !== 'string') return base;
        if (/^https?:\/\//i.test(path)) return path;
        return path.startsWith('/') ? (base + path) : (base + '/' + path);
      };
      window.__csrfToken = function () {
        return document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute('content') || '';
      };

      window.themeConfigs = {
        mbBreakpoint: window.matchMedia('(max-width: 767px)'),
        lgBreakpoint: window.matchMedia('(max-width: 975px)'),
        addToCartAction: 'drawer',
        cartAction: 'drawer',
        compareProStorage: 'egaCompareProducts',
        searchStorage: 'egaSearchtorage',
        recentStorage: 'egaRecentProduct',
        quantityUpdate: 'EGA:quantity-update',
        productAddEvent: 'EGA:product-add',
        cartUpdateEvent: 'EGA:cart-update',
        copmareProUpdate: 'EGA:compare-update',
        firstInteraction: 'EGA:first-interaction',
        countdownUpdate: 'EGA:countdown-update',
        productLoaded: 'EGA:product-loaded',
        facetUpdate: 'EGA:facet-update',
        tabUpdate: 'EGA:tab-update',
        error: 'EGA:on-error',
        quickViewShow: 'EGA:quickview-show',
        variantChanged: 'EGA:variant-changed',
        newsletterFormAction:
          'https://EGANY.us12.list-manage.com/subscribe/post?u=8ee70b5e0117f78874c2059a2&id=f1d2c30cf1&f_id=009547e0f0',
        vendorsJSLink: '//bizweb.dktcdn.net/100/531/894/themes/1018832/assets/defer-vendors.js?1768901692132',
        vendorsCssLink: '//bizweb.dktcdn.net/100/531/894/themes/1018832/assets/defer-vendors.css?1768901692132',
        defaultTransitionTime: 400,
      };
      window.flashsaleConfigs = {
        openingText: 'Vừa mở bán',
        soldText: 'Đã bán [soluongdaban] sản phẩm',
        runOutText: 'Chỉ còn [soluongtonkho] sản phẩm',
        runOutQty: 10,
        randomtMin: 0,
        randomMax: 100,
      };
      const { publish, subscribe, validateInput, convertTime, defineElement, playAnimation, serializeForm } =
        window.EGATheme;
      window.EGATheme.showQuickView = function (productHandle) {
        if (!productHandle) return;
        let opener = {
          dataset: {
            product: productHandle,
          },
        };
        document.querySelector('quick-view').show(opener);
      };
    </script>
    


    @include('UI-FRONTEND.common.theme-head-styles')
  </head>
  <body class="ega-theme index ">@include('UI-FRONTEND.common.header')

	 <main>
    <h1 class="hidden">🎁 ĐỒ CHƠI WIN WIN — Chuyên đồ chơi chính hãng, chất lượng. Xe điều khiển • Máy bay RC • Đồ chơi giáo dục • Đồ chơi Búp bê • Đồ chơi vỉ giá rẻ. Hàng sẵn kho – Giao nhanh toàn quốc.</h1>


<div class="index-container  container py-6 px-0  xl:grid   xl:grid-cols-[300px_calc(100%-312px)] gap-3  ">
  @include('UI-FRONTEND.partials.home-category-sidebar')
  <div>


<section class="section-home-banner section " style="--section-padding: 0;--section-margin: 0 0 12px;--section-padding-mb: 0;--section-margin-mb: 0;">
	<div class="container">
  <slideshow-component>
    <div class="relative slideshow-wrapper bg-no-repeat  bg-cover bg-none md:bg-image transition-all ease-linear duration-300">
      <div class="  relative ">
        <div class="w-full">
          <carousel-slider data-autoplay="5000">
            <div class="embla relative w-full overflow-hidden">
              <div
                class="embla__viewport w-full overflow-hidden"
                role="region"
                aria-roledescription="carousel"
                aria-label="Banner trang chủ"
              >
                <div class="embla__container flex">
                  <div class="embla__slide flex-[0_0_100%] overflow-hidden rounded">
                    <a class="block" href="https://dochoiwinwin.com/" title="Đồ chơi an toàn — Giao nhanh">
                      <picture>
                        <source media="(max-width: 480px)" srcset="{{ asset('UI-FRONTEND/images/Banner do choi Win Win.png') }}">
                        <img class="block mx-auto w-full object-contain" loading="eager" width="1920" height="624" src="{{ asset('UI-FRONTEND/images/Banner do choi Win Win.png') }}" alt="Đồ chơi an toàn — Giao nhanh" fetchpriority="high">
                      </picture>
                    </a>
                  </div>
                  {{-- Banner quà tặng thiếu nhi — tạm ẩn
                  <div class="embla__slide flex-[0_0_100%] overflow-hidden rounded">
                    <a class="block" href="https://dochoiwinwin.com/" title="Giỏ quà &amp; combo — Ưu đãi">
                      <picture>
                        <source media="(max-width: 480px)" srcset="{{ asset('UI-FRONTEND/images/Banner do choi Win Win.png') }}">
                        <img class="block mx-auto w-full object-contain" loading="lazy" width="1920" height="624" src="{{ asset('UI-FRONTEND/images/Banner do choi Win Win.png') }}" alt="Đồ chơi và quà tặng cho bé — Ưu đãi" fetchpriority="high">
                      </picture>
                    </a>
                  </div>
                  --}}
                </div>
              </div>
              <div class="embla__dots bottom-2 md:absolute md:left-1/2 mt-3 md:mt-0 md:-translate-x-1/2"></div>
              <div class="embla__buttons md:block dnone">
                <button class="embla__button embla__button--prev" type="button" aria-label="Banner trước">
                  <i class="icon icon-carret-left" aria-hidden="true"></i>
                </button>
                <button class="embla__button embla__button--next" type="button" aria-label="Banner sau">
                  <i class="icon icon-carret-right" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </carousel-slider>
        </div>

      </div>
    </div>
  </slideshow-component>
</div>
</section>


<section class="section section-polices overflow-hidden  " style="--section-padding: 0;--section-margin: 24px 0 24px;--section-padding-mb: 0;--section-margin-mb: 24px 0 24px;">
  <div class="container  ">

    <div class="text-w-icon text-w-icon--style-1  md:px-0 px-container w-auto bg-background rounded overflow-hidden ">
      <carousel-slider>

        <div class="embla">
          <div class="embla__viewport w-full overflow-hidden">
          <div class="embla__container flex gap-2
             [&>div]:pl-2 [&>div]:w-[66.666%]  md:[&>div]:w-[40%] lg:[&>div]:w-[29%]   xl:[&>div]:w-1/4 [&>div]:flex_0 [&>div]:shrink-0 h-auto ">


				   <div class="embla__slide  mx-auto flex h-inherit flex-nowrap item relative  gap-4 py-3 md:py-4 px-4  ">
					<div class="w-6  flex-grow-0 flex-shrink-0">
					  <img class="object-contain" loading="lazy" width="24" height="24" src="100/531/894/themes/1018832/assets/policies_icon_1.png?1768901692132" alt="Giao hàng miễn phí trong 24h (chỉ áp dụng khu vực nội thành)">
					</div>
					<div class="">
					  <span class="font-semibold block">Giao hỏa tốc</span>
					  <span class="text-xs">Tối đa trong vòng 1 giờ</span>
					</div>
				  </div>


				   <div class="embla__slide  mx-auto flex h-inherit flex-nowrap item relative  gap-4 py-3 md:py-4 px-4  ">
					<div class="w-6  flex-grow-0 flex-shrink-0">
					  <img class="object-contain" loading="lazy" width="24" height="24" src="100/531/894/themes/1018832/assets/policies_icon_2.png?1768901692132" alt="Giao hàng miễn phí trong 24h (chỉ áp dụng khu vực nội thành)">
					</div>
					<div class="">
					  <span class="font-semibold block">Bảo hành rõ ràng</span>
					  <span class="text-xs">Đảm bảo quyền lợi cho khách hàng</span>
					</div>
				  </div>


				   <div class="embla__slide  mx-auto flex h-inherit flex-nowrap item relative  gap-4 py-3 md:py-4 px-4  ">
					<div class="w-6  flex-grow-0 flex-shrink-0">
					  <img class="object-contain" loading="lazy" width="24" height="24" src="100/531/894/themes/1018832/assets/policies_icon_3.png?1768901692132" alt="Giao hàng miễn phí trong 24h (chỉ áp dụng khu vực nội thành)">
					</div>
					<div class="">
					  <span class="font-semibold block">Tư vấn nhanh chóng</span>
					  <span class="text-xs">Hỗ trợ khách hàng 24/7</span>
					</div>
				  </div>


				   <div class="embla__slide  mx-auto flex h-inherit flex-nowrap item relative  gap-4 py-3 md:py-4 px-4  ">
					<div class="w-6 flex-grow-0 flex-shrink-0">
					  <img class="object-contain" loading="lazy" width="24" height="24" src="100/531/894/themes/1018832/assets/policies_icon_chinh-hang.svg?ww-1" alt="Hàng chính hãng 100%">
					</div>
					<div class="">
					  <span class="font-semibold block">Hàng chính hãng 100%</span>
					  <span class="text-xs invisible block" aria-hidden="true">&#8203;</span>
					</div>
				  </div>


          </div>
        </div>
        </div>
      </carousel-slider>
    </div>
  </div>
</section>


@if(false)
{{-- Section Voucher giảm giá (tắt) --}}
<section class="section section-coupons" style="--section-padding: 0;--section-margin: 30px 0 30px;--section-padding-mb: 0;--section-margin-mb: 30px 0 30px;">
  <div class="container ">
    <div class="section-card">


  <div class="heading-bar">
    <h2 class=" heading w-auto font-semibold  ">
      Voucher giảm giá
    </h2>
  </div>

      <carousel-slider>

        <div class="embla relative overflow-hidden w-full">
          <div class="embla__viewport w-full overflow-hidden">
          <div class="embla__container flex max-w-max h-auto -ml-gutter md:[&>div]:w-[45%]">


                              <div class="embla__slide grow-0 shrink-0 w-full  md:w-[40%] pl-gutter  ">
                  <coupon-item>
  <div class="coupon-item cursor-pointer" data-portal="#coupon-modal">
    <div class="  h-inhiret grid grid-cols-[30%_auto] ">
      <div class=" flex  justify-center items-center p-3 rounded text-primary bg-relative  relative">
        <div class="coupon-item__code  font-bold text-secondary whitespace-nowrap text-ellipsis overflow-hidden ">
          <span class="swiper-no-swiping">
            EGA50
          </span>
        </div>
        <div class="absolute opacity-50 w-[1px] h-[calc(100%-40px)] border border-dashed border-primary -right-[1px] top-1/2 -translate-y-1/2  "></div>

      </div>
      <div class="p-3  rounded text-primary bg-relative relative ">
        <div class="coupon-item__summary mb-2.5 text-xs text-neutral-400 font-semibold line-clamp-2">Giảm 15% cho đơn hàng giá trị tối thiểu 500.000 ₫. Mã giảm tối đa 250.000 ₫</div>

        <div class="coupon-item__rules mb-2.5 hidden ">- Đồng giá 2 triệu cho nhóm sản phẩm Set combo<br>
- Tổng giá trị sản phẩm từ 5 triệu trở lên</div>
        <div class="coupon-item__action grid grid-cols-2 gap-3 ">
          <div class="coupon-item__end-date text-xs ">

              <span class="text-neutral-300" data-expired-date="28/12/2024-16:00:00">28/12/2024 </span>

          </div>

          <copy-button data-copied-text="Đã sao chép" onclick="event.stopPropagation()">
            <input type="hidden" value="EGA50">
            <button type="button" class="btn text-xs relative z-[1] font-semibold copy-button w-full  text-white border border-primary bg-primary  py-1.5 whitespace-nowrap px-2 ">
              Sao chép
            </button>
          </copy-button>
        </div>
      </div>
    </div>
  </div>
</coupon-item>                </div>


                              <div class="embla__slide grow-0 shrink-0 w-full  md:w-[40%] pl-gutter  ">
                  <coupon-item>
  <div class="coupon-item cursor-pointer" data-portal="#coupon-modal">
    <div class="  h-inhiret grid grid-cols-[30%_auto] ">
      <div class=" flex  justify-center items-center p-3 rounded text-primary bg-relative  relative">
        <div class="coupon-item__code  font-bold text-secondary whitespace-nowrap text-ellipsis overflow-hidden ">
          <span class="swiper-no-swiping">
            EGAT10
          </span>
        </div>
        <div class="absolute opacity-50 w-[1px] h-[calc(100%-40px)] border border-dashed border-primary -right-[1px] top-1/2 -translate-y-1/2  "></div>

      </div>
      <div class="p-3  rounded text-primary bg-relative relative ">
        <div class="coupon-item__summary mb-2.5 text-xs text-neutral-400 font-semibold line-clamp-2">Giảm 15% cho đơn hàng giá trị tối thiểu 500.000 ₫. Mã giảm tối đa 250.000 ₫</div>

        <div class="coupon-item__rules mb-2.5 hidden "></div>
        <div class="coupon-item__action grid grid-cols-2 gap-3 ">
          <div class="coupon-item__end-date text-xs ">

              <span class="text-neutral-300" data-expired-date="20/12/2024-">20/12/2024 </span>

          </div>

          <copy-button data-copied-text="Đã sao chép" onclick="event.stopPropagation()">
            <input type="hidden" value="EGAT10">
            <button type="button" class="btn text-xs relative z-[1] font-semibold copy-button w-full  text-white border border-primary bg-primary  py-1.5 whitespace-nowrap px-2 ">
              Sao chép
            </button>
          </copy-button>
        </div>
      </div>
    </div>
  </div>
</coupon-item>                </div>


                              <div class="embla__slide grow-0 shrink-0 w-full  md:w-[40%] pl-gutter  ">
                  <coupon-item>
  <div class="coupon-item cursor-pointer" data-portal="#coupon-modal">
    <div class="  h-inhiret grid grid-cols-[30%_auto] ">
      <div class=" flex  justify-center items-center p-3 rounded text-primary bg-relative  relative">
        <div class="coupon-item__code  font-bold text-secondary whitespace-nowrap text-ellipsis overflow-hidden ">
          <span class="swiper-no-swiping">
            FREESHIP
          </span>
        </div>
        <div class="absolute opacity-50 w-[1px] h-[calc(100%-40px)] border border-dashed border-primary -right-[1px] top-1/2 -translate-y-1/2  "></div>

      </div>
      <div class="p-3  rounded text-primary bg-relative relative ">
        <div class="coupon-item__summary mb-2.5 text-xs text-neutral-400 font-semibold line-clamp-2">Mã giảm 99.000 ₫ cho đơn hàng tối thiểu 1 triệu. Tối đa 1 mã giảm giá/đơn hàng.</div>

        <div class="coupon-item__rules mb-2.5 hidden "></div>
        <div class="coupon-item__action grid grid-cols-2 gap-3 ">
          <div class="coupon-item__end-date text-xs ">

              <span class="text-neutral-300" data-expired-date="30/12/2024-20:18:00">30/12/2024 </span>

          </div>

          <copy-button data-copied-text="Đã sao chép" onclick="event.stopPropagation()">
            <input type="hidden" value="FREESHIP">
            <button type="button" class="btn text-xs relative z-[1] font-semibold copy-button w-full  text-white border border-primary bg-primary  py-1.5 whitespace-nowrap px-2 ">
              Sao chép
            </button>
          </copy-button>
        </div>
      </div>
    </div>
  </div>
</coupon-item>                </div>


                              <div class="embla__slide grow-0 shrink-0 w-full  md:w-[40%] pl-gutter  ">
                  <coupon-item>
  <div class="coupon-item cursor-pointer" data-portal="#coupon-modal">
    <div class="  h-inhiret grid grid-cols-[30%_auto] ">
      <div class=" flex  justify-center items-center p-3 rounded text-primary bg-relative  relative">
        <div class="coupon-item__code  font-bold text-secondary whitespace-nowrap text-ellipsis overflow-hidden ">
          <span class="swiper-no-swiping">
            EGA500K
          </span>
        </div>
        <div class="absolute opacity-50 w-[1px] h-[calc(100%-40px)] border border-dashed border-primary -right-[1px] top-1/2 -translate-y-1/2  "></div>

      </div>
      <div class="p-3  rounded text-primary bg-relative relative ">
        <div class="coupon-item__summary mb-2.5 text-xs text-neutral-400 font-semibold line-clamp-2">Miễn phí vận chuyển cho đơn hàng từ 500.000 ₫. Áp dụng cho khu vực Tp.HCM</div>

        <div class="coupon-item__rules mb-2.5 hidden "></div>
        <div class="coupon-item__action grid grid-cols-2 gap-3 ">
          <div class="coupon-item__end-date text-xs ">

          </div>

          <copy-button data-copied-text="Đã sao chép" onclick="event.stopPropagation()">
            <input type="hidden" value="EGA500K">
            <button type="button" class="btn text-xs relative z-[1] font-semibold copy-button w-full  text-white border border-primary bg-primary  py-1.5 whitespace-nowrap px-2 ">
              Sao chép
            </button>
          </copy-button>
        </div>
      </div>
    </div>
  </div>
</coupon-item>                </div>


                                    </div>
			<div class="embla__dots  mt-3 md:hidden"></div>

          <div class="embla__buttons dnone md:block">
            <button class="embla__button embla__button--prev" type="button">
              <i class="icon icon-carret-left"></i>
            </button>

            <button class="embla__button embla__button--next" type="button">
              <i class="icon icon-carret-right"></i>
            </button>
            </div>
			</div>
        </div>
      </carousel-slider>
    </div>
  </div>
</section>
@endif


@if(false)
{{-- Section Gợi ý cho bạn (tắt) --}}
<section class="section  section-collection-list relative" style="--section-padding: 0;--section-margin: 24px 0 24px;--section-padding-mb: 0;--section-margin-mb: 24px 0 24px;     color: var(--color-foreground);	--color-collection-item: transparent;	--grid-col-desktop:repeat(auto-fit, minmax(calc(100% / 7 - 1px ), 1fr));	--grid-col-mobile:repeat(3,1fr); ">
  <div class="container overflow-hidden md:px-container relative z-[1]   ">
    <div class="section-card ">


  <div class="heading-bar">
    <h2 class=" heading w-auto font-semibold  ">
      Gợi ý cho bạn
    </h2>
  </div>

      <carousel-slider>
        <div class="embla flex">
          <div class="embla__viewport mx-auto w-full max-w-full overflow-hidden">
            <div class="collection-list flex items-center h-full embla__container -ml-2">


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Đồ chơi mới — vui mỗi ngày" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_1.png?1768901692132" alt="Đồ chơi mới — vui mỗi ngày" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Giỏ quà sinh nhật" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_2.png?1768901692132" alt="Giỏ quà sinh nhật" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Quà tặng" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_3.png?1768901692132" alt="Quà tặng" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Combo văn phòng" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_4.png?1768901692132" alt="Combo văn phòng" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Đồ chơi chính hãng" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_5.png?1768901692132" alt="Đồ chơi chính hãng" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Đồ chơi vận động" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_6.png?1768901692132" alt="Đồ chơi vận động" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Đồ chơi cao cấp" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_7.png?1768901692132" alt="Đồ chơi cao cấp" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Quà Tết &amp; lễ hội" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_8.png?1768901692132" alt="Quà Tết &amp; lễ hội" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Quà khai trương" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_9.png?1768901692132" alt="Quà khai trương" loading="lazy">
                      </div>

                    </a>
                  </div>


                  <div class="embla__slide h-full w-[130px] md:w-1/5  grow-0 shrink-0 pl-2 ">
                    <a href="collections/all.html" title="Set biếu đối tác" class="collection-item  text-center flex flex-col items-center gap-1 lg:gap-3 group hover:brightness-[0.98] ">
                      <div class="collection-item-thumb   flex items-center justify-center overflow-hidden">
                        <img class="object-contain transition-transform relative z-10  duration-200" width="180" height="215" src="thumb/large/100/531/894/themes/1018832/assets/coll_10.png?1768901692132" alt="Set biếu đối tác" loading="lazy">
                      </div>

                    </a>
                  </div>


            </div>
          </div>
          <div class="embla__buttons">
            <button class="embla__button embla__button--prev md:block dnone" type="button">
              <i class="icon icon-carret-left"></i>
            </button>

            <button class="embla__button embla__button--next md:block dnone" type="button">
              <i class="icon icon-carret-right"></i>
            </button>
          </div>
        </div>
      </carousel-slider>
    </div>
  </div>
</section>
@endif


<section class="section section-flashsale  relative  overflow-hidden  w-full" id="section-flashsale-0" hidden aria-hidden="true" style="display:none;--section-padding: 0;--section-margin: 0 0 0;--section-padding-mb: 0;--section-margin-mb: 0 0 0;--color-flashsale-bg: #e0f2fe;--color-flashsale-title: #0284c7;--color-flashsale-timer-bg: #0284c7;--color-flashsale-timer: #ffffff;--color-flashsale-process: #0284c7;--color-flashsale-title-hover: #0369a1;">
  <flashsale-section data-not-started="show" data-id="section-flashsale-0" data-ended="show" data-random="true">
    <div class="container px-0 lg:px-[var(--container-padding)]">
    <div class="bg-[var(--color-flashsale-bg)] lg:rounded-lg overflow-hidden ">
      <div class=" -mx-[var(--container-padding)]  text-[var(--color-flashsale-title)] px-4 md:px-6 py-4">
        <div class="flex  flex-wrap lg:flex-nowrap items-center justify-center lg:justify-between  mx-auto mb-4 gap-3 md:gap-4">
          <div class="flex items-center gap-2 md:gap-3">

                          <h2 class="text-h4 text-center md:text-left lg:text-h3  font-bold flashsale-heading md:px-0 px-[var(--container-padding)]">
                <a href="{{ storefrontListingUrl(['mode' => 'vip']) }}" title="Chớp thời cơ. Giá như mơ!">
                  Chớp thời cơ. Giá như mơ!                </a>
              </h2>
                      </div>


<countdown-timer data-id="section-flashsale-0" data-countdown-type="hours" data-start-date="04/12/2023" data-start-time="08:00:00" data-end-time="23:59:59" data-week="0,1,2,3,4,5,6">
      <div class="flashsale__countdown-timer  flex-wrap  flashsale__countdown-wrapper flex items-center gap-2 md:gap-5 lg:w-auto w-full justify-center">
        <span class="flashsale__countdown-label text-center hidden" data-label="not-started">
			Chương trình sẽ bắt đầu sau
        </span>
		    <span class="flashsale__countdown-label text-center hidden" data-label="ongoing">
			Nhanh lên nào! <br> <b>Sự kiện sẽ kết thúc sau</b>
        </span>
		    <span class="flashsale__countdown-label  text-center hidden" data-label="ended">
			Chương trình đã kết thúc
        </span>
        <div class="flashsale__countdown hidden">

        </div>
      </div>
</countdown-timer>
                  </div>
      </div>

      <div class="relative items-start  pb-0.5 rounded-[16px] overflow-hidden p-0.5 ">

        <div class="overflow-hidden  pb-[var(--container-padding)] px-[var(--container-padding)]">
          <carousel-slider>


              <div class="embla container px-0 overflow-hidden rounded-sm ">
    <div class="embla__viewport w-full overflow-hidden">
      <div class="embla__container flex h-inherit md:flex-nowrap -ml-2" id="home-flashsale-products">
        @for ($i = 0; $i < 12; $i++)
        <div class="relative h-inherit flashsale__item embla__slide w-[65.5%] md:w-1/3 lg:w-1/5 flex-shrink-0 flex-grow-0 pl-2">
          @include('UI-FRONTEND.partials.product-card-skeleton')
        </div>
        @endfor
      </div>
    </div>

      <div class="embla__buttons dnone md:block">
        <button class="embla__button embla__button--prev " type="button">
          <i class="icon icon-carret-left"></i>
        </button>

        <button class="embla__button embla__button--next " type="button">
          <i class="icon icon-carret-right"></i>
        </button>
      </div>
      </div>
</carousel-slider>        </div>
      </div>


          </div>
  </div>

  </flashsale-section>

</section>


@if(false)
{{-- Section Ưu Đãi Từ Thương Hiệu (tắt) --}}
<section class="section section-brand" style="--section-padding: 0;--section-margin: 24px 0 24px;--section-padding-mb: 0;--section-margin-mb: 24px 0 24px;">
  <div class="container">

    <div class="overflow-hidden section-card  ">


  <div class="heading-bar">
    <h2 class=" heading w-auto font-semibold  ">
      Ưu Đãi Từ Thương Hiệu
    </h2>
  </div>


    <div class="brand-list overflow-auto no-scrollbar flex flex-nowrap lg:grid lg:grid-cols-custom" style="--grid-col:repeat(8,1fr)">


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-1">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_1.png?1768901692132" alt="brand-1">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-2">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_2.png?1768901692132" alt="brand-2">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-3">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_3.png?1768901692132" alt="brand-3">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-4">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_4.png?1768901692132" alt="brand-4">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-5">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_5.png?1768901692132" alt="brand-5">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-6">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_6.png?1768901692132" alt="brand-6">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-7">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_7.png?1768901692132" alt="brand-7">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-8">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_8.png?1768901692132" alt="brand-8">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-9">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_9.png?1768901692132" alt="brand-9">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-10">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_10.png?1768901692132" alt="brand-10">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-11">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_11.png?1768901692132" alt="brand-11">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-12">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_12.png?1768901692132" alt="brand-12">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-13">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_13.png?1768901692132" alt="brand-13">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-14">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_14.png?1768901692132" alt="brand-14">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-15">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_15.png?1768901692132" alt="brand-15">
                </a>
            </div>


            <div class="brand-item p-2 w-auto lg:w-full flex-shrink-0 flex-grow-0 overflow-hidden">
                <a href="collections/all.html" title="brand-16">

                    <img class="object-contain mx-auto transition-transform hover:scale-105 duration-300 ease-out" width="120" height="72" loading="lazy" src="100/531/894/themes/1018832/assets/brand_16.png?1768901692132" alt="brand-16">
                </a>
            </div>


    </div>
 </div>
</div>
</section>
@endif


{{-- Banner ngang (block 1) — tạm ẩn
            <div class=" position-relative" style="background: transparent">
	<div class="text-center  container">
				<a class="position-relative block " href="https://dochoiwinwin.com/" title="banner ngang">
			<picture>
				<source media="(max-width: 480px)" srcset="thumb/large/100/531/894/themes/1018832/assets/banner_image.jpg?1768901692132">
				<img class='img-fluid position-absolute mx-auto' src="100/531/894/themes/1018832/assets/banner_image.jpg?1768901692132" style="left:0" alt="banner ngang" width="984" height="164" loading="lazy">
			</picture>
		</a>

	</div>
</div>
--}}


{{-- Banner group 3 ô (block 2, 3, 4) — tạm ẩn
<section class="section section-banner-group" style="--section-padding: 0;--section-margin: 24px 0 24px;--section-padding-mb: 0;--section-margin-mb: 24px 0 24px;">
  <div class="container">
    <carousel-slider>

      <div class="embla">


        <div class="embla__viewport inline-flex mx-auto w-full max-w-full overflow-hidden">
          <div class="embla__container flex max-w-max h-auto -ml-gutter [&>div]:pl-gutter [&>div]:w-[61.3%] md:[&>div]:w-1/3 [&>div]:flex_0 [&>div]:shrink-0">


                <div class="embla__slide">
                  <a href="https://dochoiwinwin.com/" title="GIẢM GIÁ NHẬP HỌC 30%" class="block">
                    <picture>
                      <source media="(max-width: 480px)" srcset="thumb/large/100/531/894/themes/1018832/assets/banner_group_1.jpg?1768901692132">

                      <img src="100/531/894/themes/1018832/assets/banner_group_1.jpg?1768901692132" alt="Giỏ quà tặng — Ưu đãi đến 30%" loading="lazy" width="408" height="232" class="object-cover  rounded-lg transition-transform duration-300 group-hover:scale-105 mx-auto ">
                    </picture>
                  </a>
                </div>


                <div class="embla__slide">
                  <a href="https://dochoiwinwin.com/" title="ƯU ĐÃI MÙA HÈ ĐẾN 60%" class="block">
                    <picture>
                      <source media="(max-width: 480px)" srcset="thumb/large/100/531/894/themes/1018832/assets/banner_group_2.jpg?1768901692132">

                      <img src="100/531/894/themes/1018832/assets/banner_group_2.jpg?1768901692132" alt="Đồ chơi mùa hè — Giá tốt đến 60%" loading="lazy" width="408" height="232" class="object-cover  rounded-lg transition-transform duration-300 group-hover:scale-105 mx-auto ">
                    </picture>
                  </a>
                </div>


                <div class="embla__slide">
                  <a href="https://dochoiwinwin.com/" title="GIẢM 500.000 ₫ cho đơn 2tr" class="block">
                    <picture>
                      <source media="(max-width: 480px)" srcset="thumb/large/100/531/894/themes/1018832/assets/banner_group_3.jpg?1768901692132">

                      <img src="100/531/894/themes/1018832/assets/banner_group_3.jpg?1768901692132" alt="Combo đồ chơi — Giảm đến 500.000 ₫" loading="lazy" width="408" height="232" class="object-cover  rounded-lg transition-transform duration-300 group-hover:scale-105 mx-auto ">
                    </picture>
                  </a>
                </div>


          </div>
        </div>
        <div class="embla__dots  mt-2 mx-auto"></div>
      </div>
    </carousel-slider>
  </div>
</section>
--}}


@include('UI-FRONTEND.partials.home-videos')

@include('UI-FRONTEND.partials.home-products-skeleton')
<div id="home-category-section-anchor" hidden></div>




</div>
</div>

	  </main>


<link href="100/531/894/themes/1018832/assets/bpr-products-module.css?1768901692132" rel="stylesheet" type="text/css" media="all">
<div class="sapo-product-reviews-module"></div>
@php
  $ww = wwWebContact();
  $wwZalo = $ww['zaloUrl'] ?: $ww['zaloPageUrl'];
  $wwMessenger = $ww['messengerUrl'] ?: $ww['facebookUrl'];
  $wwHotline = $ww['hotline'];
@endphp
@include('UI-FRONTEND.common.footer')


@if(false)
			{{-- Popup Sapo gợi ý ứng dụng (tắt) --}}
			<link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/sapo-popup.css?1768901692132">
<link rel="stylesheet" href="100/531/894/themes/1018832/assets/sapo-popup.css?1768901692132" media="all">
<div class="popup-sapo active">
	<div class="icon">
		<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewbox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M224 0c-17.7 0-32 14.3-32 32V51.2C119 66 64 130.6 64 208v18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416H416c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8V208c0-77.4-55-142-128-156.8V32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3H224 160c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"></path></svg>
	</div>
	<div class="content">
		<div class="title">Có thể tích hợp thêm các ứng dụngg</div>
		<ul>
			<li>

				<a href="https://apps.sapo.vn/danh-gia-san-pham-v2" target="_blank" title="Đánh giá sản phẩm" aria-label="Đánh giá sản phẩm">- Đánh giá sản phẩm</a>
			</li>

			<li>
				<a href="https://apps.sapo.vn/quan-ly-affiliate-v2" target="_blank">- Ứng dụng Affiliate</a>
			</li>

			<li>
				<a href="https://apps.sapo.vn/mua-x-tang-y-v2" target="_blank">- Mua X Tặng Y
 </a>
			</li>
			<li>
				<a href="https://apps.sapo.vn/app-combo" target="_blank">- Combo sản phẩm</a>
			</li>

		</ul>
		<div class="ghichu">Lưu ý với các ứng dụng trả phí bạn cần cài đặt và mua ứng dụng này trên App store Sapo để sử dụng ngay</div>
		<span title="Đóng" class="close-popup-sapo">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewbox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001;" xml:space="preserve"> <g> <g> <path d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717    L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859    c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287    l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285    L284.286,256.002z"></path> </g> </g> </svg>
		</span>
	</div>
</div>

@if (!app()->environment('local'))
  <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
@endif
<script>
	$('.popup-sapo .icon').click(function() {
		$(".popup-sapo").toggleClass("active");
	});
	$('.close-popup-sapo').click(function() {
		$(".popup-sapo").toggleClass("active");
	});
	setTimeout(()=>{
	$(".popup-sapo").removeClass("active");

	}, 15000)
</script>
@endif
			  	<menu-drawer class="portal portal--drawer" id="menu-drawer" data-type="drawer" data-animation="slide-in-left">
  <dialog class="portal-dialog">
    <div class="portal-overlay"></div>
    <div class="portal-inner animation bg-background h-full grid grid-rows-[auto_1fr_auto]">
      <div class="navigation-header pt-4 flex justify-between items-center border-b pb-3 border-neutral-50 px-4">

          <a href="{{ url('/account/login') }}" title="Đăng nhập" class="header-icon-group flex gap-2 items-center account-group  hover:bg-neutral-50 active:scale-95 transition-all duration-150 px-2 py-1 rounded-sm ">
            <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-sm flex items-center justify-center border border-neutral-50">
              <i class="icon icon-user"></i>
            </div>
            <div class=" ">
              <span class="text-xs">Tài khoản</span>
              <span class="font-semibold block">Đăng nhập</span>
            </div>
          </a>

        <button type="button" id="PortalClose-menu-crawer" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin" title="Đóng" aria-label="Đóng">
          <i class="icon icon-cross"></i>
        </button>
      </div>
      <nav class="navigation-vertical overflow-y-auto no-scrollbar ">
        @include('UI-FRONTEND.partials.menu-category-nav')
      </nav>
      <div class="navigation-footer 4 border-t border-neutral-50 flex">

          <div class="w-1/2">
            <a href="{{ url('/lien-he') }}" title="Địa chỉ cửa hàng" class="header-icon-group flex gap-2 items-center  hover:bg-neutral-50 transition-all duration-150 px-2 py-4 store-group">
              <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-sm flex items-center justify-center border border-neutral-50">
                <i class="icon icon-store"></i>
              </div>
              <div>
                <span class="text-xs">Địa chỉ cửa hàng</span>
              </div>
            </a>
          </div>


          <div class="w-1/2">
            <a class="header-icon-group flex gap-2 items-center  hover:bg-neutral-50  transition-all duration-150 px-2 py-4  phone-group " href="{{ $wwHotline['tel'] ?? 'tel:' }}" data-ww-contact="hotline" title="{{ $wwHotline['display'] ?? 'Hotline' }}">
              <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-sm flex items-center justify-center border border-neutral-50">
                <i class="icon icon-calling-phone"></i>
              </div>
              <div>
                <span class="text-xs">Hotline: <b data-ww-contact-slot="hotline-number">{{ $wwHotline['display'] ?? '' }}</b> </span>
              </div>
            </a>
          </div>

      </div>
    </div>
  </dialog>
</menu-drawer>


@if(false)
{{-- Popup ega-sale-pop / sales-pop (tắt) --}}
<link rel="stylesheet" href="100/531/894/themes/1018832/assets/sales-pop.css?1768901692132" media="print" onload="this.media='all'">

<noscript><link href="100/531/894/themes/1018832/assets/sales-pop.css?1768901692132" rel="stylesheet" type="text/css" media="all"></noscript>
<div id="ega-sale-pop" class="sales-pop hidden" style="--sale-pop-color: #0284c7">
	<div class="sale-pop-wrap">

	</div>
	<div class="sale-pop-close">
		<i class="icon icon-cross"></i>
	</div>

</div>
<script>
	var salePopArr = [];
	let timePerPop = 15000;
	let timeDelay = 15000;
	let salesPopDesc = `Khách hàng [name] tại [address] vừa mua sản phẩm cách đây [time]`;
	let count = 0;
	let fakerScript ="https://mixcdn.egany.com/themes/assets/faker.js"
	let customerGender = undefined;

		salePopArr = [		{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/ta-dan-merries-size-l-54-mieng-cho-be-9-14kg-1-1.jpg?v=1730192677887",
		"pd_title": "Tã dán Merries size L 54 miếng (9 - 14 kg)",
		"pd_url": "/ta-dan-merries-size-l-54-mieng-9-14-kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/goi-memory-foam-cao-su-non-chong-mop-dau-so-sinh-animo-b2305-dq001-pub121-xanh.jpg?v=1730771679073",
		"pd_title": "Gối Cao Su Non Size To Cho Bé",
		"pd_url": "/goi-cao-su-non-size-to-cho-be",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/7ed6a7bb-cebd-4e89-a980-348773a2f7c7.png?v=1730772153303",
		"pd_title": "Chiếu điều hoà cao su non cho bé sơ sinh - 5 tuối",
		"pd_url": "/chieu-dieu-hoa-cao-su-non-cho-be-so-sinh-5-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/goi-chong-trao-nguoc-cho-be-animo-b2210-ar001-xanh-65x65x20cm.jpg?v=1730772364247",
		"pd_title": "Gối chữ U chống giật mình cho bé, có định hình chống bẹp đầu cho trẻ sơ sinh",
		"pd_url": "/goi-chu-u-chong-giat-minh-cho-be-co-dinh-hinh-chong-bep-dau-cho-tre-so-sinh",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/vn-11134207-7r98o-ltnk1p0ceya223.jpg?v=1730772583673",
		"pd_title": "Khăn quấn mùa hè thay thế túi ngủ cho bé 0-1 tuổi",
		"pd_url": "/khan-quan-mua-he-thay-the-tui-ngu-cho-be-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/30320141497na-pearl-velvet-blanket-autumn-rose-detail-aw22-pp.jpg?v=1730773092447",
		"pd_title": "Chăn Xô Muslin, Chăn xô nhung hạt đậu đắp thu đông",
		"pd_url": "/chan-xo-muslin-chan-xo-nhung-hat-dau-dap-thu-dong",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-7ce8db775f0b487d9b20a6fb956f92d4.jpg?v=1730192674797",
		"pd_title": "Miếng lót sơ sinh Huggies Skin Perfect NB1 64+6 miếng (Dưới 5kg)",
		"pd_url": "/mieng-lot-so-sinh-huggies-skin-perfect-nb1-64-6-mieng-duoi-5kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/26c387112ec8b14e5ae1b6b5d59a7a54-e11c3e563b564df49e695288e26740f4.jpg?v=1730192671777",
		"pd_title": "Bỉm Goldgi Eco Dán L56",
		"pd_url": "/bim-goldgi-eco-dan-l56-9-14kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-9c5c0eedf5594a579b367b1cab0f0060.jpg?v=1730192670537",
		"pd_title": "Bỉm Bobby quần L68 (9-13kg)",
		"pd_url": "/bim-bobby-quan-l68-9-13kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/untitled-6-989b0ea3da524744909b957947b30571.png?v=1730192669953",
		"pd_title": "Bỉm Goldgi Eco Quần XXL32",
		"pd_url": "/bim-goldgi-eco-quan-xxl32-15kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/untitled-6-d9aedadd2e3540f785b8352c45dcf122.png?v=1730192666927",
		"pd_title": "Bỉm Goldgi ECO dán M66 (6-11kg)",
		"pd_url": "/bim-goldgi-eco-dan-m66-6-11kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/l-56-1c4632cf6e2246acac0c61942679538d.jpg?v=1730192665683",
		"pd_title": "Bỉm Goldgi+ X5 (Tã dán)",
		"pd_url": "/bim-goldgi-x5-dan",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/mieng-lot-molfix-thien-nhien-newborn-1-1-thang-90-mieng-664a6665b0994b47b55a7cc9b930234f.jpg?v=1730192664110",
		"pd_title": "Bỉm Molfix dán NB1 90+10 (<1 tháng)",
		"pd_url": "/bim-molfix-dan-nb1-90-10-1-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/bim-goldgi-eco-quan-xl44-12-17kg-5a0aa6d1dfda4639a8934facca9b1941.png?v=1730192662390",
		"pd_title": "Bỉm Goldgi ECO quần XL44 (12-17kg)",
		"pd_url": "/bim-goldgi-eco-quan-xl44-12-17kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/cb8f2a1a334e4811a13d89ad6a9a4094-418fd93e208d42d981fb1267d2aac39b.jpg?v=1730192661427",
		"pd_title": "BIM Bỉm Huggies XL 60 miếng (cho bé 11 - 16kg)",
		"pd_url": "/bim-bim-huggies-xl-60-mieng-cho-be-11-16kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/19d37b25d3c24fc4bf55edf13b14a56d-bc59a1d4701c4946ad962a80d2258b9f.png?v=1730192660477",
		"pd_title": "Bỉm Meries nội địa quần XXL 26+2 (15-28kg)",
		"pd_url": "/bim-meries-noi-dia-quan-xxl-26-2-15-28kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-f6e18460adcf46eb8fc8381d1831eeda.jpg?v=1730192659267",
		"pd_title": "Bỉm Moony dán M56 (6-11kg)",
		"pd_url": "/bim-moony-dan-m56-6-11kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/untitled-6321-541e0aff85044904b153f83186f1dd76.png?v=1730192657537",
		"pd_title": "Bỉm Goldgi ECO quần L48 (9-14kg)",
		"pd_url": "/bim-goldgi-eco-quan-l48-9-14kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/bim-ta-quan-merries-size-xl-38-6-mieng-cho-be-12-22kg-06953f90a5ac4b00a3cd39cb0965fcc2.jpg?v=1730192654073",
		"pd_title": "Bỉm Merries nội địa quần XL38 (12-22kg) - Cộng miếng",
		"pd_url": "/bim-bim-merries-noi-dia-quan-xl38-12-22kg-cong-mieng",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/7-5cd5b7f5bc7746a48bafc0809999c094.png?v=1730192652887",
		"pd_title": "Bỉm Goldgi ECO quần M54 (6-11kg)",
		"pd_url": "/bim-goldgi-eco-quan-m54-6-11kg",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-bot-meiji-so-9-nhat-800g-cho-be-1y-3y-14-4d04684b0a2f4291964ca30ac41af599.jpg?v=1730192650747",
		"pd_title": "Sữa Meiji nội địa Nhật Bản số 9, 800g (1 - 3 tuổi)",
		"pd_url": "/sua-meiji-noi-dia-so-9-1-3-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-meiji-so-0-noi-dia-nhat-800g-cho-be-0-12m-2c6a66c942fc470b89c092cdd17464d7.jpg?v=1730192648710",
		"pd_title": "Sữa Meiji nội địa Nhật Bản số 0, 800g (0 -  1 tuổi)",
		"pd_url": "/sua-meiji-noi-dia-nhat-ban-so-0-800g-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-thanh-meiji-so-0-noi-dia-nhat-648g-cho-be-0-1y-6-35d5c162e3814f5d86b5a0265d9fdf3f.jpg?v=1730802987087",
		"pd_title": "Sữa Meiji nội địa Nhật Bản dạng thanh số 0, (0 - 1 tuổi)",
		"pd_url": "/sua-meiji-noi-dia-nhat-ban-dang-thanh-so-0-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-30ad4f905b9f480eadfdd56f417b7951.jpg?v=1730192642667",
		"pd_title": "Sữa Aptamil Profutura Úc số 1 (0-6 tháng)",
		"pd_url": "/sua-aptamil-profutura-uc-so-1-0-6-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-meiji-growing-up-formula-448g-dang-thanh-cho-be-1y-3y-10-536a8136be7545c4ab3dfc4e784c4732.jpg?v=1730192642067",
		"pd_title": "Sữa Meiji thanh nhập khẩu Growing Up Formula (1 - 3 tuổi)",
		"pd_url": "/sua-sua-meiji-nhap-khau-dang-thanh-1-3-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/8384c62aad3d40b997d7d81d73e4d165-d3795f2537734b9ea143a62ee6433c05.jpg?v=1730192640737",
		"pd_title": "Sữa Meiji Kids Formula 900g (3 - 10 tuổi)",
		"pd_url": "/sua-meiji-kids-formula-900g-3-10-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-sua-meiji-nhap-khau-dang-thanh-0-1-tuoi-5-6e359e2124074560b86f9ec08168dd33.jpg?v=1730192639857",
		"pd_title": "Sữa Meiji thanh nhập khẩu Infant Formula EZcube (0-1 tuổi)",
		"pd_url": "/sua-meiji-thanh-nhap-khau-0-1-tuoi-thanh-le",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/2-caeddf76b3a0438c83f8ffd2529fcb05.jpg?v=1730192635770",
		"pd_title": "Sữa Aptamil Profutura Úc số 2 (6-12 tháng)",
		"pd_url": "/sua-aptamil-profutura-uc-so-2-6-12-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-meiji-infant-formula-nhap-khau-so-0-800g-0-1-tuoi-748b9bb0d02f43cdba7af74c86824203.jpg?v=1730192634477",
		"pd_title": "Sữa Meiji nhập khẩu số 0 800g (0 - 1 tuổi)",
		"pd_url": "/sua-meiji-nhap-khau-so-0-800g-0-1-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/sua-bot-meiji-growing-up-formula-800g-cho-be-1y-3y-1-1-078ff8bd992147d0ac074d87e70a666c.jpg?v=1730192633040",
		"pd_title": "Sữa Meiji nhập khẩu số 9 lon 800g (1-3 tuổi)",
		"pd_url": "/sua-meiji-nhap-khau-so-1-800g-1-3-tuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1-74b4489cc5c4432fb1c6da1b66d99e78.jpg?v=1730192630720",
		"pd_title": "Sữa Nan Nga số 1 Optipro (0-6 tháng)",
		"pd_url": "/sua-nan-nga-so-1-optipro-0-6-thang",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/f062b1b54fa2abb3417466a43265-90674bff370d4d519f24c7e79a104863-1-copy-cfdfbb979a1641348c3f8a327e476e4a.jpg?v=1730192629650",
		"pd_title": "Máy hút sữa điện đôi Galena GA-01",
		"pd_url": "/may-hut-sua-dien-doi-galena-ga-01",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-dien-doi-gluck-gp39-1-412bff21184e4eeab01c8b439d9c5653.jpg?v=1730192627767",
		"pd_title": "Máy hút sữa điện đôi Gluck Baby GP39",
		"pd_url": "/may-hut-sua-dien-doi-gluck-gp39",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/061d95f5f4ac45788cec7d3e7569f815-65321850c2e1473a82f949b9a3007c29.jpg?v=1730192625160",
		"pd_title": "Máy hút sữa điện đôi Gluck GP38 Plus",
		"pd_url": "/may-hut-sua-dien-doi-gluck-gp38plus-bh-1-nam",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/666174d31794401abcc8a63f91268bb5-ecfc33b70164490886fe747a0896be9a.png?v=1730192623383",
		"pd_title": "Máy hút sữa điện đôi Gluck GP60",
		"pd_url": "/may-hut-sua-dien-doi-gluck-gp60",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/a874ed4acf238c3637a7dbf270aefdc7.webp?v=1730779334893",
		"pd_title": "Máy hút sữa điện đôi Kamidi Max",
		"pd_url": "/may-hut-sua-dien-doi-kamidi-max",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-doi-ranh-tay-3-che-do-chibe-cb010-pin-sac-646af12691e5d-22052023113550.png?v=1730779418513",
		"pd_title": "Máy hút sữa điện đôi rảnh tay ChiBé CB010",
		"pd_url": "/may-hut-sua-dien-doi-chibe-cb010",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/b8ff741412f247fa8edb90e98981cb2c-661446b802a944b2bf5189cbec26f044.jpg?v=1730192615120",
		"pd_title": "Máy hút sữa điện đôi Spectra 9 Plus",
		"pd_url": "/may-hut-sua-dien-doi-spectra-9-plus",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/12-54b71f98d7da4775884556aadd47a22f.jpg?v=1730192614230",
		"pd_title": "Máy hút sữa điện đơn Gluck GP31 Plus",
		"pd_url": "/ddcm-may-hut-sua-dien-don-gluck-gp31-plus-bh-1-nam",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-dien-don-spectra-q.jpg?v=1730778453563",
		"pd_title": "Máy hút sữa điện đơn Spectra Q (BH 2 năm)",
		"pd_url": "/may-hut-sua-dien-don-spectra-q-bh-2-nam",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/may-hut-sua-dien-don-spectra-m1-afa095a8f82b4f4081dbb1e63f666029-jpeg.jpg?v=1730192611980",
		"pd_title": "Máy hút sữa điện M1 Spectra",
		"pd_url": "/may-hut-sua-dien-m1-spectra",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/template-web-48eeae2b48064b3b8c118dbd070dea42.jpg?v=1730192608820",
		"pd_title": "Bánh ăn dặm Gerber",
		"pd_url": "/banh-an-dam-gerber",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/ebf1858459584b22912942c768ca794e-99a3b46e209e4bb29e2cc4a3f6323e69.jpg?v=1730192607690",
		"pd_title": "Bánh gạo hữu cơ Pororo - vị Táo&Cà rốt - 25g/túi",
		"pd_url": "/banh-gao-huu-co-pororo-vi-tao-ca-rot",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/9a485408666249de89f409e3d38f4235-1785c3e5687b4c6684f56588c86e575a.png?v=1730192607197",
		"pd_title": "Bánh gạo Pigeon",
		"pd_url": "/banh-an-dam-pigeon",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/1064d32b83e54b7680a8a9c04c49f7f8-bdeb44c0d63e469aaee391a8d568f067.jpg?v=1730192604997",
		"pd_title": "Bánh gạo vị táo và cherry Agusha (1Y+)",
		"pd_url": "/banh-gao-vi-tao-va-cherry-agusha-1y",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/aac0f8ba01ea4559bf8eb8ed937b6f7e-0c95cf4c66364041b23f467f92f0e745.jpg?v=1730192604117",
		"pd_title": "Bánh gạo vị táo, chuối và lê Agusha (1Y+)",
		"pd_url": "/banh-gao-vi-tao-chuoi-va-le-agusha-1y",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/vn-11134207-7qukw-li727huwgn4c03.webp?v=1730779808387",
		"pd_title": "Bánh hữu cơ gạo lứt Pororo 25g",
		"pd_url": "/banh-huu-co-gao-lut-pororo-25g",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/z4898643141335-5de11f84ed92485e3f8fbd7f5871d1af-fb3c7fc591ac4bcaa3829456ff8b6706-master.jpg?v=1730779591473",
		"pd_title": "Bánh hữu cơ vị khoai lang, bí đỏ Pororo 25g",
		"pd_url": "/banh-huu-co-vi-khoai-lang-bi-do-pororo-25g",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/681b03e9db154b6ea9395daaf7c619b3-aaa1fb414779474e984eb550db79272d.jpg?v=1730192601387",
		"pd_title": "Bánh Pororo vị chuối - 10g/túi",
		"pd_url": "/banh-pororo-vi-chuoi",
	}, 			{
		"img_url": "//bizweb.dktcdn.net/thumb/medium/100/531/894/products/c0c8397a57bf43eebbb3a6f9420fdde1-07bec5287b6648568da17cc4b85281d9.jpg?v=1730192600643",
		"pd_title": "Bánh Pororo vị sữa chua dâu tây - 10g/túi",
		"pd_url": "/banh-huu-co-pororo-vi-sua-chua-dau-tay",
	}				]

		function showSalePop() {
			if(!faker) return
			$('#ega-sale-pop.salepop-show').removeClass('salepop-show').addClass('salespop-close');
			let pdRanIndex = Math.floor(Math.random() * salePopArr.length),
				salePopProduct = salePopArr[pdRanIndex],
				randomMin = `${Math.floor(Math.random() * 59) + 1} phút`;
			const name= `${faker.name.lastName(customerGender)} ${faker.name.firstName(customerGender)}`
			const phone = `${faker.phone.phoneNumber().replace(' ')}`
			const address = faker.random.arrayElement(faker.locales.vi.address.city_root)
			const desc = salesPopDesc
			.replace(`[name]`, name)
			.replace(`[phone]`, `xxx${phone.substr(phone.length - 8)}`)
			.replace(`[time]`, randomMin)
			.replace('[address]', address)
			const salesPopContent = `
			<div class="sale-pop-img">
			<img src="${salePopProduct.img_url}" class="img-fluid" loading="lazy" alt="${salePopProduct.title}" width="180" height="180"/>
				</div>
			<div class="sale-pop-body">
			<div class="sale-pop-name">
			<a href="${salePopProduct.pd_url}" title="${salePopProduct.pd_title}">${salePopProduct.pd_title}</a>
				</div>
			<span class="sale-pop-desc">${desc}</span>
				</div>
`
			$('.sale-pop-wrap').html(salesPopContent)
			setTimeout(()=>{
				$('#ega-sale-pop').addClass('salepop-show').removeClass('salespop-close');
				setTimeout(()=>{
					$('#ega-sale-pop.salepop-show').removeClass('salepop-show').addClass('salespop-close');
					setTimeout(()=>{
						showSalePop()
					},timeDelay)
				},10000)
			},3000)


		}


	function initSalesPop() {
		if(!salePopArr.length) return;
		setTimeout(()=>{
		$.getScript(fakerScript).then(()=>{
		if(faker){
				showSalePop()
			}
		})

		},timeDelay)


	}

	$(document).ready(() => {
		$('#ega-sale-pop').removeClass('hidden');
			initSalesPop()
			$(".sale-pop-close").click(function () {
				$("#ega-sale-pop").removeClass('salepop-show').removeClass('salespop-close');
			})
			$(".sale-pop-cta").click(function (e) {
				e.preventDefault();
				$(".sale-pop-regis").show();
			})
			$(".regis-close").click(function () {
				$(".sale-pop-regis").hide();
			})
	})


</script>
@endif


	<link rel="stylesheet" href="100/531/894/themes/1018832/assets/addthis-sharing.css?1768901692132" media="print" onload="this.media='all'">

<noscript><link href="100/531/894/themes/1018832/assets/addthis-sharing.css?1768901692132" rel="stylesheet" type="text/css" media="all"></noscript>
<div class="addThis_listSharing">
			{{--
			<div class="floating_banner relative">
			<a class="block p-2 hover:scale-105 transition-all" href="https://dochoiwinwin.com/" title="sticky banner">
				<img src="100/531/894/themes/1018832/assets/floating-banner.png?1768901692132" alt="sticky banner" width="100" height="100">
			</a>
			<button class="btn p-0  absolute -top-3 right-1 link">
				 <i class="icon icon-cross"></i>
			</button>
		</div>
			--}}

<a href="#" id="back-to-top" class="backtop back-to-top flex items-center justify-center" title="Lên đầu trang">
	<i class="icon icon-carret-up"></i>
</a>

					<div class="header-box-live dnone" aria-hidden="true">
						<button type="button" data-mobile-link="https://fb.watch/t6bCpir7ve/" style="--header-live-color:#ff0000" class="btn-live">
							<span class="relative z-[1] font-semibold">LIVE</span></button>
					</div>
	<call-center-group class="addThis_group active" style="--color-primary: #0ea5e9">


	<ul class="addThis_listing list-unstyled hidden md:block">

		@if($wwHotline)
		<li class="addThis_item" data-ww-social>
			<a class="addThis_item--icon" href="{{ $wwHotline['tel'] }}" data-ww-contact="hotline" rel="nofollow">
				<img class="img-fluid" src="100/531/894/themes/1018832/assets/addthis-phone.svg?1768901692132" alt="Gọi điện thoại" loading="lazy" width="44" height="44">
				<span class="tooltip-text">Gọi điện thoại</span>
			</a>
		</li>
		@endif
		@if($wwZalo !== '')
		<li class="addThis_item" data-ww-social>
			<a class="addThis_item--icon" href="{{ $wwZalo }}" data-ww-contact="zalo" target="_blank" rel="nofollow">
				<img class="img-fluid" src="100/531/894/themes/1018832/assets/addthis-zalo.svg?1768901692132" alt="Chat với chúng tôi qua Zalo" loading="lazy" width="44" height="44">
				<span class="tooltip-text">Chat với chúng tôi qua Zalo</span>
			</a>
		</li>
		@endif
		@if($wwMessenger !== '')
			<li class="addThis_item" data-ww-social>
				<a class="addThis_item--icon" href="{{ $wwMessenger }}" data-ww-contact="messenger" target="_blank" rel="nofollow">
					<img class="img-fluid" src="100/531/894/themes/1018832/assets/addthis-messenger.svg?1768901692132" alt="Chat với chúng tôi qua Messenger" loading="lazy" width="44" height="44">
					<span class="tooltip-text">Chat với chúng tôi qua Messenger</span>
				</a>
			</li>
		@endif

	</ul>
	{{-- Nút toggle (addThis_item--toggle): ẩn — hiển thị luôn phone / Zalo / Messenger ở trên
	<div class="addThis_item relative z-[1]" data-toggle="">
			<div class="addThis_item--icon addThis_item--toggle rounded-full">
			<img src="100/531/894/themes/1018832/assets/call-center.png?1768901692132" alt="call-center" width="30" height="30">
								 <i class="icon icon-cross"></i>

			</div>
		</div>
	--}}
		</call-center-group>
</div>
<script>
		class CallCenterGroup extends HTMLElement {
				constructor() {
					super();
					this.toggleButton = this.querySelector('[data-toggle]');
					this.listing = this.querySelector('.addThis_listing');
					if (this.toggleButton && this.listing) {
						this.addClickListener();
					}
				}

				addClickListener() {
					this.toggleButton.addEventListener('click', () => this.toggleListing());
				}

				toggleListing() {
					const isShowing = this.listing.classList.toggle('show');
					const animations = {
						show: [
							{ transform: 'translateY(60%)', opacity: 0 },
							{ transform: 'translateY(0)', opacity: 1 }
						],
						hide: [
							{ transform: 'translateY(0)', opacity: 1 },
							{ transform: 'translateY(60%)', opacity: 0 }
						]
					};

					if (isShowing) {
						this.listing.classList.remove('dnone');
						this.classList.add('active')
						this.animate(animations.show);
					} else {
						this.classList.remove('active');
						this.animate(animations.hide, () => this.listing.classList.add('dnone'));
					}
				}

				animate(keyframes, onFinish) {
					this.listing.animate(keyframes, {
						duration: 300,
						easing: 'ease',
						fill: 'forwards'
					}).onfinish = onFinish;
				}
			}

			customElements.define('call-center-group', CallCenterGroup);
		</script>
	<div data-template="index" class="cro-btns sticky md:hidden block z-10 min-h-[5.6rem] bottom-0 left-0  slide-in-bottom  ">
  <div class=" bg-background rounded-t-sm w-full min-h-[5.6rem] px-2 justify-between items-center inline-flex slide-in-bottom" style="box-shadow:var(--shadow-l)">
        <div class="cro-btns-container w-full h-full justify-center items-center gap-0.5 grid grid-cols-[repeat(auto-fit,minmax(0,1fr))]">

      @if($wwZalo !== '')
                                                            	              <a data-ww-social class="cro-btn-item cro-btn-item--1 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5  text-foreground h-full flex flex-col justify-center items-center gap-0.5" title="Zalo" href="{{ $wwZalo }}" data-ww-contact="zalo" target="_blank" rel="noopener noreferrer" style="order:4">
        <div class="w-4 h-4 relative ">
          <img class="w-full h-full object-contain" alt="Zalo" src="100/531/894/themes/1018832/assets/addthis-zalo.svg?1768901692132" loading="lazy" width="16" height="16">

        </div>
        <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Zalo</div>
      </a>
      @endif


                                                      	              <portal-opener class="cro-btn-item cro-btn-item--2 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5 text-foreground h-full flex flex-col justify-center items-center gap-0.5" style="order:1">
        <a class="w-full h-full flex flex-col justify-center items-center gap-0.5" title="Sản phẩm" href="javascript:void(0)" data-portal="#menu-drawer" role="button">
          <div class="w-4 h-4 relative ">
            <img class="w-full h-full object-contain" alt="Sản phẩm" src="thumb/small/100/531/894/themes/1018832/assets/cro-btn-2-icon.png?1768901692132" loading="lazy">
          </div>
          <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Sản phẩm</div>
        </a>
      </portal-opener>

      @php
        $themeCartQty = $themeCartQty ?? collect(session('theme_storefront_cart', []))->sum(fn ($line) => (int) ($line['quantity'] ?? 0));
      @endphp
      <portal-opener class="cro-btn-item cro-btn-item--cart w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5 text-foreground h-full flex flex-col justify-center items-center gap-0.5" style="order:2">
        <a class="w-full h-full flex flex-col justify-center items-center gap-0.5" title="Giỏ hàng" href="{{ url('/cart') }}" data-portal="#cart-drawer" role="button">
          <div class="w-4 h-4 relative flex items-center justify-center">
            <i class="icon icon-cart cro-btn-cart-icon" aria-hidden="true"></i>
            <span class="cart-count flex items-center count_item count_item_pr justify-center rounded-full absolute font-semibold"><span class="cart-count__num">{{ $themeCartQty }}</span></span>
          </div>
          <div class="text-ellipsis overflow-hidden max-w-full text-xs text-center line-clamp-1">Giỏ hàng</div>
        </a>
      </portal-opener>


      @if($wwMessenger !== '')
                                                      	              <a data-ww-social class="cro-btn-item cro-btn-item--3 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5  text-foreground h-full flex flex-col justify-center items-center gap-0.5" title="Messenger" href="{{ $wwMessenger }}" data-ww-contact="messenger" target="_blank" rel="noopener noreferrer" style="order:3">
        <div class="w-4 h-4 relative ">
          <img class="w-full h-full object-contain" alt="Messenger" src="100/531/894/themes/1018832/assets/addthis-messenger.svg?1768901692132" loading="lazy" width="16" height="16">

        </div>
        <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Messenger</div>
      </a>
      @endif


      @if($wwHotline)
                                                      	              <a data-ww-social class="cro-btn-item cro-btn-item--4 w-auto flex-shrink-0 flex-grow-0 h-full py-0.5 px-0.5  text-foreground h-full flex flex-col justify-center items-center gap-0.5" title="Điện thoại — {{ $wwHotline['display'] }}" href="{{ $wwHotline['tel'] }}" data-ww-contact="hotline" style="order:5">
        <div class="w-4 h-4 relative ">
          <img class="w-full h-full object-contain" alt="Điện thoại" src="100/531/894/themes/1018832/assets/addthis-phone.svg?1768901692132" loading="lazy" width="16" height="16">

        </div>
        <div class="text-ellipsis overflow-hidden  max-w-full text-xs text-center line-clamp-1">Điện thoại</div>
      </a>
      @endif


    </div>
            </div>
</div>
	@include('UI-FRONTEND.partials.search-drawer')

	<quick-view class="portal portal--modal" id="quick-view-product" data-type="modal" data-animation="scale-in-hor-left">
		<dialog class="portal-dialog">
			<div class=" flex items-center justify-center w-full h-full">
				<div class="portal-overlay"></div>

            <div class="portal-inner    h-full  ">
				  <button type="button" id="PortalClose-quick-view-product" data-animation="fade-in" class="portal-close-button animation rounded-full w-[3.2rem] h-[3.2rem]  border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin">
                  <i class="icon icon-cross"> </i>
                </button>
				<div class="product-wrapper animation  bg-background  w-full h-full  md:rounded-lg">

				</div>
				<span class="loading-icon gap-1 hidden items-center justify-center">

            <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>

            <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>

            <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>

</span>
              </div>
			</div>

        </dialog>

	</quick-view>

	@include('UI-FRONTEND.common.cart-drawer')

<portal-component class="portal portal--drawer" id="cart-note-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">
  <dialog class="portal-dialog">
    <div class=" w-full h-full flex">
      <div class="portal-overlay"></div>
      <div class="portal-inner w-full ml-auto bg-background   h-screen px-4 animation">
        <div class="portal-header px-3 pb-3 pt-5 ">
          <div class="font-semibold text-h4 flex items-center gap-2">
            <i class="icon icon-arrow-left  cursor-pointer text-h6 md:text-h4" id="PortalClose-cart-note-drawer"></i>Ghi
            chú đơn hàng
          </div>
        </div>
        <div class="r-bill px-3">
          <cart-note>
            <form>
              <div class="form-group">
                <label class="label block mb-2">Ghi chú</label>
                <textarea class="form-textarea" name="note" rows="6"></textarea>
              </div>
              <button type="submit" class="btn w-full mt-5  btn--large font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2">
                Lưu thông tin
              </button>
            </form>
          </cart-note>
        </div>
      </div>
    </div>
  </dialog>
</portal-component>
<cart-vat-drawer class="portal portal--drawer" id="cart-vat-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">

    <dialog class="portal-dialog">
      <div class=" w-full h-full flex">
        <div class="portal-overlay"></div>
        <div class="portal-inner w-full ml-auto bg-background   h-screen px-4 animation">
			<div class="portal-header px-3 pb-3 pt-5 ">
				<div class="font-semibold text-h6 md:text-h4 flex items-center gap-2">
			<i class="icon icon-arrow-left text-h6 md:text-h4  cursor-pointer" id="PortalClose-cart-vat-drawer"></i>
					Xuất hóa đơn công ty
				</div>
			</div>
         <div class="r-bill px-3">
			 <form>

<div class="bill-field  space-y-3 ">

		   <div class="flex items-center">
        <div class="flex items-center ">
		<input class="invoice" type="hidden" name="attributes[Xuất hóa đơn]" value='không'>
          <input class="invoice-checkbox form-checkbox" type="checkbox">
        </div>
        <div class="ml-2 text-sm">
          <label>Xuất hóa đơn</label>
        </div>
      </div>
		<div class="form-group">
			<label class="label block mb-2">Tên công ty</label>
			<input type="text" class="form-input" name="attributes[Tên công ty]" value="" data-rules="['required']" data-messages="{'required':'Trường này không được bỏ trống' }" placeholder="Tên công ty">
			<span class="error  text-error mt-2 block "></span>
		</div>
		<div class="form-group">
			<label class="label block mb-2">Mã số thuế</label>
			<input type="number" class="form-input" name="attributes[Mã số thuế]" value="" data-rules="['minLength:10','required']" data-messages="{ 'minLength:10': 'Số kí tự tối thiểu [size]', 'require':'Trường này không được bỏ trống' }" placeholder="Mã số thuế">
			 <span class="error text-error mt-2 block "></span>

		</div>
		<div class="form-group">
			<label class="label block mb-2">Địa chỉ công ty</label>
			<textarea class="form-textarea" data-rules="['required']" data-messages="{'required':'Trường này không được bỏ trống' }" name="attributes[Địa chỉ công ty]" placeholder="Địa chỉ công ty"></textarea>
			<span class="error  text-error mt-2 block "></span>

		</div>
		<div class="form-group">
			<label class="label block mb-2">Email nhận hóa đơn</label>
			<input type="email" class="form-input" name="attributes[Email nhận hóa đơn]" value="" placeholder="Email nhận hóa đơn" data-rules="['required','email']" data-messages="{'required':'Trường này không được bỏ trống', 'email': 'Email không đúng định dạng' }">
						<span class="error  text-error mt-2 block "></span>

		</div>

	</div>
				  <button type="submit" class="btn w-full mt-5  btn--large font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2">
        	Lưu thông tin

          </button>
			 </form>
		</div>
        </div>
      </div>
    </dialog>

</cart-vat-drawer>
<portal-component class="portal portal--drawer" id="cart-delivery-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">
  <dialog class="portal-dialog">
    <div class=" w-full h-full flex">
      <div class="portal-overlay"></div>
      <div class="portal-inner w-full ml-auto bg-background   h-screen px-4 animation">
        <div class="portal-header px-3 pb-3 pt-5 ">
          <div class="font-semibold text-h6 md:text-h4 flex items-center gap-2">
            <i class="icon icon-arrow-left text-h4 cursor-pointer" id="PortalClose-cart-delivery-drawer"></i>
Hẹn giờ nhận hàng
          </div>
        </div>
        <div class="r-bill px-3">
          <cart-delivery>
            <form>
              <div class="cart-delivery-time py-4">
      <div class="grid gap-2">
		   <div class="flex items-center">
        <div class="flex items-center ">
			<input id="use-delivery" type="hidden" name="attributes[Hẹn giờ nhận hàng]" value="cart.attributes[Hẹn giờ nhận hàng]">
          <input id="delivery-enabled" type="checkbox" class="form-checkbox">
        </div>
        <div class="ml-2 text-sm">
          <label for="delivery-enabled" class="">Hẹn giờ nhận hàng</label>
        </div>
      </div>
        <div>
          <label for="delivery-date-input" class="label block mb-1 text-base">Ngày nhận hàng</label>
          <div class="relative">
            <i class="icon icon-calendar text-neutral-200 top-1/2 left-2 -translate-y-1/2 absolute"></i>
			<datepicker-input class="datepicker-input">
				<input type="text" value="" name="attributes[Ngày nhận hàng]" id="delivery-date-input" class="form-input pl-2 pl-[var(--spacing-6-5)] py-2.5 min-h-[4rem]">
			  </datepicker-input>
          </div>
        </div>
        <div>
          <label for="delivery-time" class="label block mb-1 text-base ">Thời gian nhận hàng</label>
          <select id="delivery-time" name="attributes[Thời gian nhận hàng]" class="form-select min-h-[4rem]  px-2 py-2.5 border-neutral-50">
            <option selected="" value="">Chọn thời gian</option>

				<option value="08h00 - 12h00">
					08h00 - 12h00
			  	</option>

				<option value="14h00 - 18h00">
					14h00 - 18h00
			  	</option>

				<option value="19h00 - 21h00">
					19h00 - 21h00
			  	</option>
			  			            </select>
        </div>

      </div>

    </div>              <button type="submit" class="btn w-full mt-5  btn--large font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2">
                Lưu thông tin
              </button>
            </form>
          </cart-delivery>
        </div>
      </div>
    </div>
  </dialog>
</portal-component>

@if(false)
{{-- Popup thông báo sau khi thêm sản phẩm vào giỏ (tắt) --}}
<add-to-cart-popup class="portal" id="add-to-cart-popup" data-animation="fade-in">
  <dialog class="portal-dialog">
      <div class="flex items-center justify-center w-full h-full p-4">
        <div class="portal-overlay"></div>
        <div class="portal-inner  popup-content max-w-[400px] relative w-full">
        	<button type="button" id="PortalClose-add-to-cart-popup" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border flex items-center justify-center active:scale-95 transition-transform">
            <i class="icon icon-cross"> </i>
          </button>
          <div class="popup-content w-full bg-white rounded-lg ">
            <div class="popup-header text-success bg-relative py-2 px-4">
              <span class="font-semibold">✔ Thêm vào giỏ hàng thành công</span>
            </div>
            <div class="popup-body grid grid-cols-[80px_1fr] gap-2 p-4">
              <img class="popup-product-img" src="" alt="" style="width:80px;">
              <div>
                <div class="popup-product-title font-semibold"></div>
                <div class="popup-product-variant text-neutral-200 text-sm"></div>
              </div>
            </div>
            <div class="popup-footer p-4 border-t border-neutral-50">
              <div class="flex gap-2 justify-between">
                Giỏ hàng hiện có
                <div class="whitespace-nowrap text-right">
                  <div class="popup-cart-total price font-semibold"></div>
                 <div class="text-neutral-200"> (<span class="popup-cart-quantity"></span> sản phẩm)</div>
                </div>
              </div>
              <div class="flex gap-2 pt-4">
              <button type="button" class="btn w-full font-semibold border border-neutral-50 inline-flex justify-center items-center gap-2 hover:bg-neutral-100" onclick="document.querySelector('#add-to-cart-popup').hide()">Quay lại</button>
              <button type="button" class="btn btn-cart font-semibold  bg-primary text-white inline-flex  justify-center items-center gap-2 w-full">Xem giỏ hàng</button>
              </div>
            </div>
          </div>
        </div>
      </div>
  </dialog>
</add-to-cart-popup>
@endif
	<coupon-drawer lass="portal portal--drawer" id="coupon-drawer" data-type="drawer" data-animation="slide-in-right" style="--dialog-max-width:52rem">

  <dialog class="portal-dialog">
    <div class=" w-full h-full flex ">
      <div class="portal-overlay"></div>
      <div class="portal-inner  grid grid-rows-[auto_1fr_auto] w-full ml-auto bg-background  h-screen px-4 pb-4 animation">
        <div class="portal-header px-3 pb-3 pt-5 ">
          <div class="font-semibold text-h6 md:text-h4 flex items-center gap-2">
            <i class="icon icon-arrow-left text-h6 md:text-h4  cursor-pointer" id="PortalClose-coupon-drawer"></i> Chọn mã giảm giá
          </div>
        </div>
        <div class="coupon-list py-4"></div>
		  <div>

		  </div>
      </div>
    </div>
  </dialog>
</coupon-drawer>

<coupon-modal class="portal portal--modal portal--modal-sm" id="coupon-modal" data-type="modal" data-animation="fade-in">
  <dialog class="portal-dialog">
    <div class=" flex items-center justify-center w-full h-full p-3">
      <div class="portal-overlay"></div>
      <div class="portal-inner animation ">
		  		<button type="button" id="PortalClose-coupon-modal" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin">
                  <i class="icon icon-cross "> </i>
                </button>
		  <div class=" p-3 md:p-5 rounded-lg  bg-background   h-full grid grid grid-rows-[auto_1fr_auto]">
		<div class="coupon-header  border-b pb-2.5 border-dashed border-neutral-50"></div>
		  <div class="coupon-desc "></div>
		  <div class="coupon-act grid grid-cols-2 gap-3 border-t  pt-2.5 border-dashed border-neutral-50">
			<button id="PortalClose-coupon-item-modal" class="btn  border border-neutral-50 hover:bg-neutral-100 py-1.5 ">Đóng</button>
		 	<copy-button data-copied-text="Đã sao chép">
                <input type="hidden" value="">
                <button type="button" class="btn copy-button w-full font-semibold text-primary border border-primary bg-primary text-white  py-1.5 ">
  					Sao chép
      			</button>
        </copy-button>
		  </div>
		</div>
      </div>
    </div>
  </dialog>
</coupon-modal>
	{{-- So sánh sản phẩm: modal + nút mở (tạm tắt)
	<compare-qv class="portal portal--modal" id="quick-view-compare" data-type="modal" data-animation="slide-in-bottom">
  <dialog class="portal-dialog">
    <div class=" flex items-center justify-center w-full">
      <div class="portal-overlay "></div>
      <div class="portal-inner bg-background rounded-sm animation">
		   <button type="button" id="PortalClose-quick-view" class="absolute active:scale-95 transition-transform right-0 p-3 bg-background rounded-sm flex items-center justify-center gap-2 link">
			   			   Thu gọn
                  <i class="icon icon-carret-down"> </i>
                </button>
			<div class="compare-product-list">

		  </div>

	</div>
    </div>
  </dialog>
</compare-qv>

  <portal-opener class="compare-opener hidden">
			<div class=" cursor-pointer font-semibold btn rounded-full bg-background border border-primary text-primary" data-portal="#quick-view-compare">
			  <p class="flex items-center gap-1">
				  	  <span class="line-clamp-1">  So sánh </span>
				  				  	  <span class="compare-count"> </span>

				  <i class="ico icon-arrow-swap"></i>
				 </p>

			</div>
		  </portal-opener>
	--}}

	{{-- Popup banner ưu đãi (tắt)
	<promo-popup class="portal portal--modal" id="promo-popup" data-type="modal" data-animation="fade-in">
		<dialog class="portal-dialog">
			<div class=" flex items-center justify-center w-full h-full">
				  <div class="portal-overlay"></div>
            <div class="relative z-10 animation p-6 md:p-4">
				<button type="button" id="PortalClose-promo-popup" class="portal-close-button w-[3.2rem] h-[3.2rem] rounded-full border border-white text-white flex items-center justify-center active:scale-95 transition-transform hover:animate-spin">
                  <i class="icon icon-cross"> </i>
                </button>
				 <a href="collections/all.html" title="Click ngay để nhận ưu đãi hot!!">
                        <img loading="lazy" class="object-contain" src="100/531/894/themes/1018832/assets/banner-popup-img.png?1768901692132" alt="Click ngay để nhận ưu đãi hot!!" width="540" height="540">
                    </a>
              </div>
			</div>

        </dialog>

	</promo-popup>
	--}}

	<error-popup class="portal portal--modal portal--modal-sm" id="error-modal" data-type="modal" data-animation="fade-in">
  <dialog class="portal-dialog">
    <div class="  flex items-start justify-end  p-3 w-full h-full">
      <div class="portal-overlay"></div>
      <div class="portal-inner animation ">

		  <div class="error-list flex gap-4 flex-col items-end">

		</div>
      </div>
    </div>
  </dialog>
</error-popup>
  {{-- Popup live Facebook (theme Sapo) — tắt: cảnh báo Permissions-Policy unload --}}


@include('UI-FRONTEND.partials.home-products-ajax')
	<script src="100/531/894/themes/1018832/assets/main.js?ww-cart-open-fix-1"></script>
	<script src="100/531/894/themes/1018832/assets/product.js?ww-portal-opener-fix-1" defer fetchpriority="low"></script>
	<script src="100/531/894/themes/1018832/assets/quick-view-enhance.js?ww-qv-cart-prompt-1" defer fetchpriority="low"></script>
	@include('UI-FRONTEND.common.cart-scripts')
	<script src="100/531/894/themes/1018832/assets/flashsale.js?1768901692132" defer="" fetchpriority="low"></script>
	<script src="100/531/894/themes/1018832/assets/coupon.js?1768901692132" defer="" fetchpriority="low"></script>

	<script src="100/531/894/themes/1018832/assets/defer-scripts.js?ww-cart-boot-immediate-1" defer="" fetchpriority="low"></script>

  @if (!app()->environment('local'))
    <script defer="" src="beacon.min.js/v8c78df7c7c0f484497ecbca7046644da1771523124516" integrity="sha512-8DS7rgIrAmghBFwoOTujcf6D9rXvH8xm8JQ1Ja01h9QX8EzXldiszufYa4IFfKdLUKTTrnSFXLDkUEOTrZQ8Qg==" data-cf-beacon='{"version":"2024.11.0","token":"6c92bbc133584e029f09e826272d3606","server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
  @endif
</body>
</html>


