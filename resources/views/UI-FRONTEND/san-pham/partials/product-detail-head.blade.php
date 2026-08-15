<!doctype html>
<html lang="vi">
  <head>
    <base href="{{ rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') }}/UI-FRONTEND/">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="revisit-after" content="2 days">
    <meta name="robots" content="noodp,index,follow">
    <meta
      name="viewport"
      content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"
    >
    @include('UI-FRONTEND.common.og-meta')

    <script>
      var Bizweb = Bizweb || {};
      Bizweb.store = @json(config('services.sapo.store') ?: 'thegioisuawinwin.mysapo.net');
      Bizweb.id = 531894;
      Bizweb.theme = { id: 1018832, name: 'Đồ Chơi Win Win', role: 'main' };
      Bizweb.template = 'product';
      if (!Bizweb.fbEventId) {
        Bizweb.fbEventId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
          var r = (Math.random() * 16) | 0;
          var v = c === 'x' ? r : (r & 0x3) | 0x8;
          return v.toString(16);
        });
      }
    </script>
    {{-- Tắt Sapo Apps (reviews/aff/combo): store cũ ega-babymart + CDN thường ERR_CONNECTION_RESET trên Laravel --}}
    <script>
      window.BizwebAnalytics = window.BizwebAnalytics || {};
      window.BizwebAnalytics.meta = window.BizwebAnalytics.meta || {};
      window.BizwebAnalytics.meta.currency = 'VND';
      window.BizwebAnalytics.tracking_url = '/s';
      window.BizwebAnalytics.meta.product = {
        id: {{ (int) ($productId ?? 0) }},
        vendor: '',
        name: '',
        type: '',
        price: 0,
      };
    </script>

    {{-- Tắt stats.min.js (Bizweb): gọi /s/api/v1/* và /cart/add.js không tồn tại trên Laravel --}}
    @if (false && !app()->environment('local'))
      <script src="dist/js/stats.min.js?v=96f2ff2"></script>
    @endif

    <meta
      name="keywords"
      content="đồ chơi trẻ em, đồ chơi an toàn, quà tặng cho bé, Win Win, sản phẩm"
    >
    <link rel="preconnect" href="https://bizweb.dktcdn.net">
    <link rel="preconnect" href="https://egany.com" crossorigin="">
    <link rel="preload" as="script" href="100/531/894/themes/1018832/assets/vendors.js?1768901692132">
    <script src="100/531/894/themes/1018832/assets/vendors.js?1768901692132"></script>
    <script src="100/531/894/themes/1018832/assets/jquery.js?1768901692132"></script>
    <script src="assets/themes_support/api.jquery.js"></script>

    <script>
      window.themeUrl = function (path) {
        const base = "{{ rtrim(url('/'), '/') }}";
        if (!path) return base;
        if (typeof path !== 'string') return base;
        if (/^https?:\/\//i.test(path)) return path;
        return path.startsWith('/') ? base + path : base + '/' + path;
      };
      window.__csrfToken = function () {
        return (
          document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        );
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
        vendorsJSLink:
          '//bizweb.dktcdn.net/100/531/894/themes/1018832/assets/defer-vendors.js?1768901692132',
        vendorsCssLink:
          '//bizweb.dktcdn.net/100/531/894/themes/1018832/assets/defer-vendors.css?1768901692132',
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
      window.EGATheme.addToCart = function (variantId, quantity, callback) {
        const data = 'quantity=' + quantity + '&VariantId=' + variantId;
        const { addToCartAction, productAddEvent } = window.themeConfigs;
        fetch(window.themeUrl('/cart/add'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.__csrfToken(),
          },
          body: data,
          credentials: 'same-origin',
        })
          .then((res) => res.json())
          .then((response) => {
            publish(productAddEvent, { data: response, action: addToCartAction });
            if (typeof callback === 'function') callback();
          })
          .catch((err) => console.error(err));
      };
      window.EGATheme.showQuickView = function (productHandle) {
        if (!productHandle) return;
        document.querySelector('quick-view').show({ dataset: { product: productHandle } });
      };
    </script>

    @include('UI-FRONTEND.common.theme-head-styles')
    <link
      rel="stylesheet"
      href="100/531/894/themes/1018832/assets/product-detail.css?ww-pd-variants-5"
      media="all"
    >
  </head>
