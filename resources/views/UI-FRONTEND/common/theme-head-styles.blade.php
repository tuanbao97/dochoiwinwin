    <style>
      @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url('{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/lexend-regular.woff2') }}') format('woff2');
      }
      @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 600;
        font-display: swap;
        src: url('{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/lexend-semibold.woff2') }}') format('woff2');
      }
      @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 700;
        font-display: swap;
        src: url('{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/lexend-bold.woff2') }}') format('woff2');
      }
      @font-face {
        font-family: ega-iconfont;
        src: url('{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/ega-iconfont.woff2') }}') format('woff2');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
      }
    </style>
    <link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/vendors.css?1768901692132">
    <link rel="stylesheet" href="100/531/894/themes/1018832/assets/vendors.css?1768901692132" media="all">
    <link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/components.css?1768901692132">
    <link rel="stylesheet" href="100/531/894/themes/1018832/assets/components.css?1768901692132" media="all">
    <link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/icon.css?1768901692132">
    <link rel="stylesheet" href="100/531/894/themes/1018832/assets/icon.css?1768901692132" media="all">
    <link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/global.css?ww-scroll-mid-1">
    <link rel="stylesheet" href="100/531/894/themes/1018832/assets/global.css?ww-scroll-mid-1" media="all">
    <link rel="preload" as="style" media="all" href="100/531/894/themes/1018832/assets/custom.css?ww-card-adding-1">
    <link rel="stylesheet" href="100/531/894/themes/1018832/assets/custom.css?ww-card-adding-1" media="all">
    <link
      rel="stylesheet"
      href="100/531/894/themes/1018832/assets/quickview.css?ww-qv-variant-warn-1"
      media="print"
      onload="this.media='all'"
    >
    <link
      rel="stylesheet"
      href="100/531/894/themes/1018832/assets/cart-drawer.css?ww-cart-suggest-4-1"
      media="print"
      onload="this.media='all'"
    >
    <noscript>
      <link
        href="100/531/894/themes/1018832/assets/quickview.css?ww-qv-variant-warn-1"
        rel="stylesheet"
        type="text/css"
        media="all"
      >
    </noscript>
    <noscript>
      <link
        href="100/531/894/themes/1018832/assets/cart-drawer.css?ww-cart-suggest-4-1"
        rel="stylesheet"
        type="text/css"
        media="all"
      >
    </noscript>

    <style>
      .sapo-product-reviews-badge {
        display: none !important;
      }
      html,
      body {
        margin: 0 !important;
        padding: 0 !important;
      }
      /* top-banner tạm ẩn
      body > .top-banner:first-child {
        margin-top: 0 !important;
        padding-top: 0 !important;
      }
      .top-banner {
        display: block !important;
        height: 50px !important;
        line-height: 0 !important;
        margin: 0 !important;
        margin-top: -20px !important;
        padding: 0 !important;
        font-size: 0 !important;
        background: #ff9838;
        background-image: url("{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/top_banner.jpg') }}");
        background-repeat: no-repeat;
        background-position: center top;
        background-size: cover;
        overflow: hidden;
      }
      .top-banner__link {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        line-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
      }
      .top-banner__img {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        vertical-align: top !important;
      }
      .top-banner .top-banner__img--mobile {
        display: none !important;
      }
      @media (max-width: 767px) {
        .top-banner {
          height: 45px !important;
          background-image: url("{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/top_banner_mb.jpg') }}");
        }
        .top-banner .top-banner__img--desktop {
          display: none !important;
        }
        .top-banner .top-banner__img--mobile {
          display: block !important;
        }
      }
      */
    </style>

    <style>
      html {
        box-sizing: border-box;
        font-size: 62.5%;
        height: 100%;
      }
      :root {
       /*
         replace font family and font weight
       */
       --font-body-family: 'Lexend', sans-serif;
       --font-body-weight: 400;
       --font-headings-family: 'Lexend', sans-serif;
       --font-headings-weight: 700;

       /* For text color */
       --color-foreground: #131313;

       /* For background color */
       --color-background: #FFFFFF;

    	 --color-background-body: #f0f9ff;

       /* For primary color : button, link  */

       --color-primary: #0ea5e9;

       /* base on design  */
       --color-secondary: #0284c7;

       --color-link:  #0369a1 ;
       /* For cta color */
       --color-accent: #38bdf8;

       --color-neutral-50 : #EBEBEB;
       --color-neutral-100 : #BFBFBF;
       --color-neutral-200 : #999999;
       --color-neutral-300 : #666666;
       --color-neutral-400 : #464646;


       /* For line border color or shadow color */
       --color-label: var(--color-neutral-200);
       --color-price:  #0284c7;
       --color-price-compare: #929292;
       --color-sale-bg: #0ea5e9;
       --color-sale: #ffffff;
       --color-addtocart: #FFFFFF;
       --color-addtocart-bg: #0ea5e9;
       --color-coupon-primary: #0284c7;
       --color-coupon-secondary: #0369a1;

       /* For error state */
       --color-error: #EE1926;

       /* For success state */
       --color-success: #2BAD14 ;

       /* For warning state */
       --color-warning: #F2BC1B;

      /* header */
      --color-header: var(--color-foreground);
      --color-header-bg:  var(--color-background);
      --header-height: 8rem;
      --logo-width: auto;
      --logo-height: calc(var(--header-height) - var(--spacing-4));
      --logo-height-header: var(--logo-height);
      --logo-max-width-header: 21.6rem;
      --color-cart-bubble: #FFFFFF;
      --color-cart-bubble-bg: #f97316;
      --color-search: var(--color-primary);
      --color-sub-header-bg: #0ea5e9;
      --color-sub-header: #ffffff;
      --color-sub-header-link: #e0f2fe;

       /* Font size for body text */
       --font-size-body: 1.5rem;

       /* Font size for text h1 - h6 */
       --font-size-h1: 4.8rem;
       --font-size-h2: 4rem;
       --font-size-h3: 3.2rem;
       --font-size-h4: 2.8rem;
       --font-size-h5: 2rem;
       --font-size-h6: 1.6rem;

       /*  if any title different
       --font-title-1: 3rem;
       --font-title-2: 2.25rem;
       */

       /* Rounded config */
       --rounded: 0.8rem;
       --rounded-lg: calc( var(--rounded) * 1.5 );
       --rounded-sm: calc( var(--rounded) / 2 );
       --rounded-xs: calc( var(--rounded) / 4 );
       --rounded-full: 9999px;

       --rounded-button: 0.8rem;
       --rounded-input: 0.4rem;


       /* Spacing */

         /**
          * ---------------------------------------------------------------------
          * SPACING VARIABLES
          *
          * We are using a spacing inspired from frameworks like Tailwind CSS.
          * ---------------------------------------------------------------------
          */
          --spacing-0-5: 0.2rem; /* 2px */
          --spacing-1: 0.4rem; /* 4px */
          --spacing-1-5: 0.6rem; /* 6px */
          --spacing-2: 0.8rem; /* 8px */
          --spacing-2-5: 1rem; /* 10px */
          --spacing-3: 1.2rem; /* 12px */
          --spacing-3-5: 1.4rem; /* 14px */
          --spacing-4: 1.6rem; /* 16px */
          --spacing-4-5: 1.8rem; /* 18px */
          --spacing-5: 2rem; /* 20px */
          --spacing-5-5: 2.2rem; /* 22px */
          --spacing-6: 2.4rem; /* 24px */
          --spacing-6-5: 2.6rem; /* 26px */
          --spacing-7: 2.8rem; /* 28px */
          --spacing-7-5: 3rem; /* 30px */
          --spacing-8: 3.2rem; /* 32px */
          --spacing-8-5: 3.4rem; /* 34px */
          --spacing-9: 3.6rem; /* 36px */
          --spacing-9-5: 3.8rem; /* 38px */
          --spacing-10: 4rem; /* 40px */
          --spacing-11: 4.4rem; /* 44px */
          --spacing-12: 4.8rem; /* 48px */
          --spacing-14: 5.6rem; /* 56px */
          --spacing-16: 6.4rem; /* 64px */
          --spacing-18: 7.2rem; /* 72px */
          --spacing-20: 8rem; /* 80px */
          --spacing-24: 9.6rem; /* 96px */

          /* Container */
       --container-width: 100%;
       --container-padding: var(--spacing-3);
       --grid-gutter: var(--spacing-2);

       --shadow-l: 0px 2rem 4.8rem 0px rgba(51, 51, 51, 0.2);
       --navigation-width: 40rem;
       --swatch-xanh-duong: #232fff;
       --swatch-hong: #fe77f1;
       --swatch-den: #000000;
       --swatch-trang: #ffffff;
     }

     @media (max-width: 767px) {
       :root {
         --logo-width: auto;
         --logo-height: calc(var(--header-height) - var(--spacing-3));
         --header-height: 6.4rem;
       }
     }

     body {
       grid-template-columns: 100%;
       min-height: 100%;
       margin: 0;
       font-size: var(--font-size-body);
       letter-spacing: -0.004em;
       line-height: calc(20 / 14);
       font-family: var(--font-body-family);
       font-weight: var(--font-body-weight);
       background-color: var(--color-background-body);
       color: var(--color-foreground);
       scroll-behavior: smooth;
       -webkit-font-smoothing: antialiased;
     }
     .section {
       padding: var(--section-padding,  0);
       margin: var(--section-margin, 2rem 0 0 0);
     }

     @media(max-width: 975px){

      .section {
        padding: var(--section-padding-mb,0);
        margin: var(--section-margin-mb, 2rem 0 0 0);
      }

    }


  @media (min-width: 1200px) {
    :root {
      --container-width: 1200px;
    }
  }

  @media (min-width: 1320px) {
    :root {
      --container-width: 1320px;
    }
  }
</style>

    <!--
      Giao diện: Đồ Chơi Win Win (tùy biến từ theme Sapo)
    -->
    <link
      href="100/531/894/themes/1018832/assets/appcombo.css?1768901692132"
      rel="stylesheet"
      type="text/css"
      media="all"
    >

    <style id="ww-top-banner-fix">
      html {
        background: #f0f9ff !important;
      }
      html,
      body.ega-theme {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100%;
      }
      body.ega-theme {
        width: 100%;
      }
      /* Mobile: chặn scroll ngang — KHÔNG áp dụng desktop (phá sticky menu trái) */
      @media (max-width: 1279px) {
        html {
          overflow-x: clip;
        }
        @supports not (overflow: clip) {
          html {
            overflow-x: hidden;
          }
        }
        body.ega-theme {
          overflow-x: clip;
        }
        @supports not (overflow: clip) {
          body.ega-theme {
            overflow-x: hidden;
          }
        }
      }
      /* Carousel: luôn cắt ngang trước khi JS Embla chạy */
      body.ega-theme .embla,
      body.ega-theme .embla__viewport {
        max-width: 100%;
        overflow: hidden;
      }
      /* top-banner tạm ẩn
      body.ega-theme > .top-banner:first-child {
        position: relative;
        top: 0;
        margin: 0 !important;
        padding: 0 !important;
      }
      .top-banner,
      .top-banner__link,
      .top-banner__img,
      .top-banner img {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 0 !important;
      }
      .top-banner {
        display: block !important;
        height: 50px !important;
        margin-top: -20px !important;
        background: #ff9838 !important;
        background-image: url("{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/top_banner.jpg') }}") !important;
        background-repeat: no-repeat !important;
        background-position: center top !important;
        background-size: cover !important;
        overflow: hidden !important;
        box-shadow: 0 -24px 0 0 #ff9838;
      }
      .top-banner__link {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
      }
      @media (max-width: 767px) {
        .top-banner {
          height: 45px !important;
          background-image: url("{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/top_banner_mb.jpg') }}") !important;
        }
      }
      body.ega-theme > .top-banner:first-child + .header {
        margin-top: 0 !important;
      }
      */
      html.overflow-hidden,
      html:has(body.overflow-hidden),
      body.overflow-hidden {
        overflow: hidden !important;
      }
    </style>

    <style id="ww-quick-view-styles">
      /* Tránh card custom element bị inline → nhỏ rồi to khi JS/CSS kịp */
      card-product {
        display: block;
        width: 100%;
        height: 100%;
        min-width: 0;
      }
      /* Click cả card (ảnh + chữ) mở xem nhanh; ẩn icon mắt */
      .ww-quick-view-btn,
      .ww-quick-view-tooltip {
        display: none !important;
      }
      card-product.ww-card-opens-qv,
      card-product.ww-card-opens-qv .card-product {
        cursor: pointer;
      }
      card-product.ww-card-opens-qv .card-product__cart-btn,
      card-product.ww-card-opens-qv .addtocart-btn,
      card-product.ww-card-opens-qv .add_to_cart {
        cursor: pointer;
      }
      #quick-view-product.ww-open {
        visibility: visible;
        opacity: 1;
      }
      #quick-view-product .ww-qv-gallery-thumb.is-active {
        border-color: var(--color-primary);
      }
      #quick-view-product .gallery-thumbnails {
        max-width: 100%;
      }
      #quick-view-product .gallery-thumbnails.embla-thumbs .embla__slide:first-of-type,
      #quick-view-product .gallery-thumbnails.embla-thumbs .embla__slide:last-of-type {
        margin-left: 0;
        margin-right: 0;
      }
      #quick-view-product .gallery-thumbnails .embla__viewport {
        width: 100%;
        margin-inline: 0;
        overflow: hidden;
      }
      #quick-view-product .gallery-thumbnails .embla__buttons {
        pointer-events: none;
      }
      #quick-view-product .gallery-thumbnails .embla__button {
        pointer-events: auto;
        width: 2.8rem;
        height: 2.8rem;
        opacity: 1;
      }
      #quick-view-product .gallery-thumbnails .embla__button[disabled] {
        opacity: 0.35;
        cursor: default;
      }
      #quick-view-product .ww-vnd {
        font-family: system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
        margin-left: 0.15em;
      }
    </style>

    <script>
      window.wwQuickViewClick = window.wwQuickViewClick || function (e, btn) {
        if (e) {
          e.preventDefault();
          e.stopPropagation();
          if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        }
        var id = btn && (btn.dataset ? btn.dataset.productId : btn.getAttribute('data-product-id'));
        if (id && typeof window.wwOpenQuickView === 'function') {
          window.wwOpenQuickView(parseInt(id, 10) || 0);
        } else if (id) {
          window.__wwQvQueue = window.__wwQvQueue || [];
          window.__wwQvQueue.push(parseInt(id, 10) || 0);
        }
        return false;
      };
      window.wwOpenQuickView = window.wwOpenQuickView || function (id) {
        window.__wwQvQueue = window.__wwQvQueue || [];
        window.__wwQvQueue.push(id);
      };
    </script>
