@php
  $contact = wwWebContact();
  $storeName = $contact['storeName'] ?? 'Đồ Chơi Win Win';
  $seoTitle = 'Hướng dẫn xóa dữ liệu — Win Win';
  $seoDescription = 'Hướng dẫn yêu cầu xóa tài khoản và dữ liệu cá nhân tại ' . $storeName . ', bao gồm dữ liệu đăng nhập bằng Facebook hoặc Google.';
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
            <span class="text-neutral-100">Hướng dẫn xóa dữ liệu</span>
          </li>
        </ul>
      </div>
    </div>

    <section class="section main-page" style="--section-margin: 0px 0px 40px; --section-margin-mb: 0px 0px 20px">
      <div class="container">
        <div class="bg-background rounded-lg px-3 py-4 md:px-6 md:py-6 mb-6">
          <div class="page-content ww-policy">
            <header class="ww-policy__header">
              <h1 class="text-h4 font-semibold mb-2">Hướng dẫn xóa dữ liệu người dùng</h1>
              <p class="text-base text-primary font-semibold mb-0">{{ $storeName }}</p>
            </header>

            <div class="ww-policy__body prose text-base">
              <p>
                Quý khách có thể yêu cầu xóa tài khoản và toàn bộ dữ liệu cá nhân đã lưu tại {{ $storeName }},
                kể cả dữ liệu nhận được khi đăng nhập bằng Facebook hoặc Google (tên, email, ảnh đại diện, mã định danh tài khoản).
              </p>

              <h2>1. Gửi yêu cầu xóa dữ liệu</h2>
              <p>Liên hệ với chúng tôi qua một trong các kênh sau:</p>
              <ul>
                @if (!empty($contact['email']))
                  <li>
                    <strong>Email:</strong>
                    <a class="link text-primary font-semibold" href="mailto:{{ $contact['email'] }}?subject={{ rawurlencode('Yêu cầu xóa dữ liệu') }}">{{ $contact['email'] }}</a>
                    với tiêu đề <em>"Yêu cầu xóa dữ liệu"</em>
                  </li>
                @endif
                @if (!empty($contact['hotline']))
                  <li>
                    <strong>Hotline / Zalo:</strong>
                    <a class="link text-primary font-semibold" href="{{ $contact['hotline']['tel'] }}" title="Hotline">{{ $contact['hotline']['display'] }}</a>
                  </li>
                @endif
                <li>
                  <strong>Trang liên hệ:</strong>
                  <a class="link text-primary font-semibold" href="{{ url('/lien-he') }}" title="Liên hệ">{{ url('/lien-he') }}</a>
                </li>
              </ul>
              <p>Vui lòng cung cấp email hoặc số điện thoại đã dùng để đăng ký / đăng nhập để chúng tôi xác minh đúng chủ tài khoản.</p>

              <h2>2. Thời gian xử lý</h2>
              <ul>
                <li>Chúng tôi xác nhận đã nhận yêu cầu trong vòng 2 ngày làm việc.</li>
                <li>Tài khoản và dữ liệu cá nhân được xóa trong vòng 30 ngày kể từ khi xác minh xong.</li>
                <li>Chứng từ đơn hàng đã phát sinh có thể được giữ lại theo thời hạn kế toán, thuế pháp luật yêu cầu, nhưng không còn gắn với tài khoản của quý khách.</li>
              </ul>

              <h2>3. Gỡ quyền truy cập trên Facebook</h2>
              <p>Quý khách cũng có thể tự gỡ kết nối giữa tài khoản Facebook và website:</p>
              <ol>
                <li>Mở Facebook, vào <em>Cài đặt &amp; quyền riêng tư → Cài đặt</em>.</li>
                <li>Chọn <em>Ứng dụng và trang web</em>.</li>
                <li>Tìm ứng dụng <strong>{{ $storeName }}</strong> và chọn <em>Gỡ</em>.</li>
              </ol>
              <p>
                Gỡ ứng dụng trên Facebook chỉ ngừng chia sẻ dữ liệu mới. Để xóa dữ liệu đã lưu tại website,
                vui lòng gửi yêu cầu theo mục 1.
              </p>

              <p class="mb-0">
                Xem thêm
                <a class="link text-primary font-semibold" href="{{ url('/chinh-sach-bao-mat') }}" title="Chính sách bảo mật">Chính sách bảo mật</a>.
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
