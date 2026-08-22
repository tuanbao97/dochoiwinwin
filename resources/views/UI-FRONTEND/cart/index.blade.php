@php
  $seoTitle = 'Giỏ hàng — Win Win';
  $seoDescription = 'Giỏ hàng mua sắm tại Đồ Chơi Win Win.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme cart">@include('UI-FRONTEND.common.header')

  <main>
    <style>
      .section-cart cart-form.is-empty .cart-form {
        display: block;
      }
      .section-cart cart-form.is-empty .cart-left,
      .section-cart cart-form.is-empty .cart-right {
        display: none;
      }
      .section-cart cart-form.is-empty .cart-empty {
        display: block;
      }
      .section-cart .cart-table {
        --cart-page-template: minmax(0, 1fr) 16rem 16rem;
      }
      .section-cart .cart-header {
        display: grid !important;
        grid-template-columns: var(--cart-page-template);
        gap: 1.6rem;
        text-align: center;
      }
      .section-cart .cart-header > div:nth-child(2) {
        display: none;
      }
      .section-cart .cart-item {
        display: grid;
        grid-template-columns: var(--cart-page-template);
        gap: 1.6rem;
        align-items: center;
        padding: 1.6rem 0;
      }
      .section-cart .cart-product-col {
        min-width: 0;
        padding-left: 3.2rem;
      }
      .section-cart .cart-unit-price-col {
        display: block;
      }
      .section-cart .cart-quantity-col {
        justify-content: center;
      }
      .section-cart .cart-total-col {
        text-align: center;
      }
      .section-cart .cart-remove-col {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1;
      }
      .section-cart .ww-cart-item-img {
        width: 6.4rem;
        height: 6.4rem;
      }
      @media (max-width: 767px) {
        .section-cart .cart-header {
          display: none !important;
        }
        .section-cart .cart-item {
          grid-template-columns: 1fr auto;
          grid-template-areas:
            "product product"
            "total quantity";
          gap: 1rem 1.2rem;
        }
        .section-cart .cart-product-col {
          grid-area: product;
          padding-left: 0;
          padding-right: 3.2rem;
        }
        .section-cart .cart-quantity-col {
          grid-area: quantity;
          justify-content: flex-end;
        }
        .section-cart .cart-total-col {
          grid-area: total;
          text-align: left;
        }
        .section-cart .cart-remove-col {
          left: auto;
          right: 0;
          top: 0;
          transform: none;
        }
      }
    </style>

    <div class="breadcrumbs">
      <div class="container">
        <ul class="breadcrumb py-3 flex flex-wrap items-center text-xs md:text-sm">
          <li class="home">
            <a class="link" href="{{ url('/') }}" title="Trang chủ"><span>Trang chủ</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          <li><span class="text-neutral-100">Giỏ hàng</span></li>
        </ul>
      </div>
    </div>

    <section class="section section-cart" style="--section-margin: 0 0 40px; --section-margin-mb: 0 0 24px">
      <div class="container">
        <div class="bg-background rounded-lg p-3 md:p-6">
          <h1 class="text-h4 font-semibold mb-5">Giỏ hàng</h1>

          <cart-form class="block {{ ($totalQuantity ?? 0) <= 0 ? 'is-empty' : '' }}">
            <form class="cart-form" action="{{ url('/cart') }}" method="post">
              <div class="cart cart-page-grid grid gap-4 lg:grid-cols-[1fr_36rem]">
                <div class="cart-left">
                  @include('theme.partials.cart-contents', [
                    'cartSection' => 'table',
                    'items' => $items ?? [],
                    'totalQuantity' => $totalQuantity ?? 0,
                    'totalPrice' => $totalPrice ?? 0,
                    'appUrl' => $appUrl ?? rtrim(url('/'), '/'),
                  ])
                  @include('theme.partials.cart-contents', [
                    'cartSection' => 'empty',
                    'items' => $items ?? [],
                    'totalQuantity' => $totalQuantity ?? 0,
                    'totalPrice' => $totalPrice ?? 0,
                    'appUrl' => $appUrl ?? rtrim(url('/'), '/'),
                  ])
                </div>

                <div class="cart-right">
                  @include('theme.partials.cart-contents', [
                    'cartSection' => 'summary',
                    'items' => $items ?? [],
                    'totalQuantity' => $totalQuantity ?? 0,
                    'totalPrice' => $totalPrice ?? 0,
                    'appUrl' => $appUrl ?? rtrim(url('/'), '/'),
                  ])
                </div>
              </div>
            </form>
          </cart-form>
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.theme-portals')

  <script src="{{ storefrontThemeAsset('main.js') }}"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="{{ storefrontThemeAsset('defer-scripts.js') }}" defer fetchpriority="low"></script>
</body>
</html>
