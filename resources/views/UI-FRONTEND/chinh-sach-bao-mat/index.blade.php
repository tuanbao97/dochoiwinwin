@php
  $contact = wwWebContact();
  $storeName = $contact['storeName'] ?? 'Đồ Chơi Win Win';
  $seoTitle = 'Chính sách bảo mật — Win Win';
  $seoDescription = 'Chính sách bảo mật thông tin khách hàng tại ' . $storeName . ': thông tin thu thập, mục đích sử dụng, chia sẻ và quyền của khách hàng.';
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
            <span class="text-neutral-100">Chính sách bảo mật</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-page" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-6">
          <div class="page-content ww-policy">
            <header class="ww-policy__header">
              <h1 class="text-h4 font-semibold mb-2">Chính sách bảo mật</h1>
              <p class="text-base text-primary font-semibold mb-0">{{ $storeName }}</p>
            </header>

            <div class="ww-policy__body prose text-base">
              <p>
                {{ $storeName }} ({{ preg_replace('#^https?://#', '', rtrim(url('/'), '/')) }}) tôn trọng và bảo vệ thông tin cá nhân của khách hàng.
                Chính sách này giải thích chúng tôi thu thập thông tin gì, dùng vào việc gì và quyền của quý khách đối với thông tin đó.
              </p>

              <h2>1. Thông tin chúng tôi thu thập</h2>
              <ul>
                <li><strong>Khi đặt hàng:</strong> họ tên, số điện thoại, email, địa chỉ nhận hàng và ghi chú đơn hàng.</li>
                <li><strong>Khi tạo tài khoản:</strong> họ tên, email, số điện thoại và mật khẩu (được mã hóa, nhân viên không xem được).</li>
                <li>
                  <strong>Khi đăng nhập bằng Facebook hoặc Google:</strong> chỉ nhận <em>tên, địa chỉ email và ảnh đại diện</em>
                  cùng mã định danh tài khoản do Facebook/Google cung cấp. Chúng tôi không nhận mật khẩu, danh sách bạn bè
                  hay bài đăng của quý khách và không đăng bất cứ nội dung gì lên tài khoản của quý khách.
                </li>
                <li><strong>Khi truy cập website:</strong> cookie phiên đăng nhập, giỏ hàng và số liệu truy cập ẩn danh để website hoạt động ổn định.</li>
              </ul>

              <h2>2. Mục đích sử dụng</h2>
              <ul>
                <li>Xử lý, giao hàng và chăm sóc đơn hàng; liên hệ xác nhận khi cần.</li>
                <li>Tạo và duy trì tài khoản, lưu lịch sử mua hàng, ưu đãi và voucher của quý khách.</li>
                <li>Hỗ trợ bảo hành, đổi trả và giải đáp thắc mắc.</li>
                <li>Gửi thông tin khuyến mãi khi quý khách đồng ý nhận.</li>
              </ul>

              <h2>3. Chia sẻ thông tin</h2>
              <p>Chúng tôi không bán hoặc cho thuê thông tin cá nhân. Thông tin chỉ được chia sẻ trong phạm vi cần thiết với:</p>
              <ul>
                <li>Đơn vị vận chuyển để giao hàng (họ tên, số điện thoại, địa chỉ).</li>
                <li>Hệ thống quản lý bán hàng của cửa hàng để xử lý đơn và tồn kho.</li>
                <li>Cơ quan nhà nước có thẩm quyền khi có yêu cầu theo quy định pháp luật.</li>
              </ul>

              <h2>4. Lưu trữ và bảo mật</h2>
              <ul>
                <li>Thông tin được lưu trên máy chủ của cửa hàng, truyền qua kết nối mã hóa HTTPS.</li>
                <li>Chỉ nhân viên được phân quyền mới truy cập dữ liệu đơn hàng và khách hàng.</li>
                <li>Thông tin được lưu trong thời gian quý khách còn sử dụng dịch vụ hoặc theo thời hạn pháp luật yêu cầu (ví dụ chứng từ bán hàng).</li>
              </ul>

              <h2>5. Quyền của quý khách</h2>
              <ul>
                <li>Xem và chỉnh sửa thông tin cá nhân trong trang tài khoản.</li>
                <li>Từ chối nhận tin khuyến mãi bất cứ lúc nào.</li>
                <li>
                  Yêu cầu xóa tài khoản và dữ liệu cá nhân — xem
                  <a class="link text-primary font-semibold" href="{{ url('/huong-dan-xoa-du-lieu') }}" title="Hướng dẫn xóa dữ liệu">Hướng dẫn xóa dữ liệu</a>.
                </li>
                <li>
                  Gỡ quyền đăng nhập của website trong tài khoản Facebook:
                  <em>Cài đặt &amp; quyền riêng tư → Cài đặt → Ứng dụng và trang web</em>.
                </li>
              </ul>

              <h2>6. Liên hệ</h2>
              <ul>
                <li><strong>Đơn vị:</strong> {{ $storeName }}</li>
                @if (!empty($contact['address']))
                  <li><strong>Địa chỉ:</strong> {{ $contact['address'] }}</li>
                @endif
                @if (!empty($contact['email']))
                  <li><strong>Email:</strong> <a class="link text-primary font-semibold" href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
                @endif
                @if (!empty($contact['hotline']))
                  <li>
                    <strong>Hotline:</strong>
                    <a class="link text-primary font-semibold" href="{{ $contact['hotline']['tel'] }}" title="Hotline">{{ $contact['hotline']['display'] }}</a>
                  </li>
                @endif
              </ul>

              <p class="mb-0">Chính sách có thể được cập nhật; phiên bản mới nhất luôn được đăng tại trang này.</p>
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
