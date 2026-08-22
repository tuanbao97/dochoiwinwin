@php
  $seoTitle = 'Chính sách thanh toán — Win Win';
  $seoDescription = 'Chính sách thanh toán tại Đồ Chơi Win Win. Hotline 0905 454 775 - 0905 09 09 10.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  <link rel="stylesheet" href="{{ storefrontThemeAsset('policy-page.css') }}" media="all">
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
            <span class="text-neutral-100">Chính sách thanh toán</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-page" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-6">
          <div class="page-content ww-policy">
            <header class="ww-policy__header">
              <h1 class="text-h4 font-semibold mb-2">Chính sách thanh toán</h1>
              <p class="text-base text-primary font-semibold mb-0">Đồ Chơi Win Win</p>
            </header>

            <div class="ww-policy__body prose text-base">
              <p>
                Win Win hỗ trợ nhiều hình thức thanh toán linh hoạt để quý khách mua hàng tại cửa hàng
                hoặc đặt hàng online thuận tiện nhất.
              </p>

              <h2>1. Hình thức thanh toán</h2>
              <ul>
                <li><strong>Tiền mặt:</strong> thanh toán trực tiếp tại cửa hàng hoặc khi nhận hàng (COD).</li>
                <li><strong>Chuyển khoản ngân hàng:</strong> chuyển khoản theo thông tin cửa hàng cung cấp khi xác nhận đơn.</li>
                <li><strong>Thanh toán qua ứng dụng / ví điện tử:</strong> theo hướng dẫn của nhân viên khi đặt hàng (nếu áp dụng).</li>
              </ul>

              <h2>2. Quy trình thanh toán đơn online</h2>
              <ol>
                <li>Chọn sản phẩm và gửi đơn hàng trên website hoặc qua Zalo / Messenger / hotline.</li>
                <li>Nhân viên Win Win xác nhận đơn, địa chỉ giao và hình thức thanh toán.</li>
                <li>Quý khách thanh toán theo phương thức đã chọn và nhận hàng theo lịch giao.</li>
              </ol>

              <h2>3. Lưu ý</h2>
              <ul>
                <li>Với đơn chuyển khoản, vui lòng ghi rõ nội dung chuyển khoản theo hướng dẫn để đối soát nhanh.</li>
                <li>Hóa đơn / biên nhận được cung cấp theo yêu cầu của khách hàng.</li>
                <li>Mọi thắc mắc về thanh toán vui lòng liên hệ hotline
                  <a class="link text-primary font-semibold" href="{{ wwWebContact()['hotline']['tel'] ?? 'tel:' }}" data-ww-contact="hotline" title="Hotline">
                    <span data-ww-contact-slot="hotline-number">{{ wwWebContact()['hotline']['display'] ?? '' }}</span>
                  </a>
                  hoặc trang
                  <a class="link text-primary font-semibold" href="{{ url('/lien-he') }}" title="Liên hệ">Liên hệ</a>.
                </li>
              </ul>

              <p class="mb-0">
                Thời gian hỗ trợ: <strong>6:00 – 21:30</strong> (Tất cả các ngày trong tuần).
              </p>
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
