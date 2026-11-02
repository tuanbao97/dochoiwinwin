@php
  $seoTitle = 'Chính sách bảo hành — Win Win';
  $seoDescription = 'Chính sách bảo hành tại Đồ Chơi Win Win: kiểm tra khi nhận hàng, video mở hộp và điều kiện hỗ trợ lỗi nhà sản xuất.';
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
            <span class="text-neutral-100">Chính sách bảo hành</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-page" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-6">
          <div class="page-content ww-policy">
            <header class="ww-policy__header">
              <h1 class="text-h4 font-semibold mb-2">Chính sách bảo hành</h1>
              <p class="text-base text-primary font-semibold mb-0">Đồ Chơi Win Win</p>
            </header>

            <div class="ww-policy__body prose text-base">
              <p>
                Đồ chơi là sản phẩm thường xuyên tiếp xúc, va chạm khi trẻ chơi.
                Win Win hỗ trợ bảo hành theo điều kiện rõ ràng dưới đây để bảo vệ quyền lợi khách hàng
                và đảm bảo sản phẩm lỗi từ nhà sản xuất được xử lý công bằng.
              </p>

              <h2>1. Điều kiện được bảo hành</h2>
              <p>Win Win chỉ tiếp nhận bảo hành khi đồng thời đáp ứng các điều kiện sau:</p>
              <ul>
                <li>
                  Quý khách có <strong>video mở hộp sản phẩm và test trực tiếp ngay lúc đó</strong>
                  (quay liền mạch, thấy rõ bao bì, tem, tình trạng sản phẩm và quá trình kiểm tra).
                </li>
                <li>
                  Lỗi thuộc về <strong>nhà sản xuất</strong>
                  (không khởi động, lỗi linh kiện, thiếu phụ kiện so với mô tả, lỗi kỹ thuật sẵn có…).
                </li>
                <li>
                  Còn thông tin mua hàng: hóa đơn, tin nhắn xác nhận đơn, hoặc đơn hàng trên hệ thống.
                </li>
              </ul>

              <h2>2. Trường hợp không bảo hành</h2>
              <ul>
                <li>Hàng đã qua sử dụng.</li>
                <li>Hư hỏng do đâm va, rơi vỡ, ngấm nước, cháy chập hoặc sử dụng sai hướng dẫn.</li>
                <li>Không có video mở hộp và test ngay khi nhận hàng.</li>
                <li>Lỗi phát sinh sau khi sản phẩm đã hoạt động bình thường lúc kiểm tra.</li>
              </ul>

              <h2>3. Kiểm tra tại cửa hàng</h2>
              <ul>
                <li>
                  Khi quý khách <strong>test trực tiếp tại cửa hàng</strong> và sản phẩm hoạt động bình thường,
                  sản phẩm được xem là <strong>đã đạt chuẩn</strong>.
                </li>
                <li>
                  Sau khi mang sản phẩm ra khỏi cửa hàng, Win Win
                  <strong>không bảo hành</strong> các trường hợp lỗi phát sinh sau đó,
                  trừ khi có thỏa thuận riêng bằng văn bản hoặc tin nhắn xác nhận từ shop.
                </li>
              </ul>
              <p>
                Đồ chơi là món đồ tiếp xúc, va chạm trong quá trình chơi nên rất dễ phát sinh hư hỏng ngoài lỗi nhà sản xuất.
                Mong quý khách thông cảm và kiểm tra kỹ ngay khi nhận hàng hoặc tại cửa hàng.
              </p>

              <h2>4. Cách liên hệ hỗ trợ</h2>
              <p>
                Khi cần hỗ trợ, quý khách vui lòng gửi kèm: họ tên, số điện thoại, thông tin đơn hàng,
                mô tả lỗi và video mở hộp / test.
              </p>
              <ul>
                <li>
                  Hotline:
                  <a class="link text-primary font-semibold" href="{{ wwWebContact()['hotline']['tel'] ?? 'tel:' }}" data-ww-contact="hotline" title="Hotline">
                    <span data-ww-contact-slot="hotline-number">{{ wwWebContact()['hotline']['display'] ?? '' }}</span>
                  </a>
                  —
                  <a class="link text-primary font-semibold" href="tel:0905090910" title="0905 09 09 10">0905 09 09 10</a>
                </li>
                <li>
                  Trang liên hệ:
                  <a class="link text-primary font-semibold" href="{{ url('/lien-he') }}" title="Liên hệ">{{ url('/lien-he') }}</a>
                </li>
                <li>Địa chỉ cửa hàng: Đường DT605, xã Hòa Tiến, Đà Nẵng (đối diện Trường Tiểu học số 2 Hòa Tiến)</li>
                <li>Thời gian hỗ trợ: <strong>6:00 – 21:30</strong> (Tất cả các ngày trong tuần)</li>
              </ul>

              <p class="mb-0">
                Cảm ơn quý khách đã tin tưởng và mua sắm tại Đồ Chơi Win Win.
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
