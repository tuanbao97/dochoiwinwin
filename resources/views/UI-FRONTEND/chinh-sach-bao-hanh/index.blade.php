@php
  $seoTitle = 'Chính sách bảo hành — Win Win';
  $seoDescription = 'Chính sách bảo hành và hỗ trợ sau mua tại Đồ Chơi Win Win. Hotline 0905 454 775 - 0905 09 09 10.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  <link rel="stylesheet" href="100/531/894/themes/1018832/assets/policy-page.css?ww-policy-2" media="all">
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
                Đồ Chơi Win Win cam kết mang đến sản phẩm chất lượng và hỗ trợ
                khách hàng sau khi mua hàng một cách rõ ràng, minh bạch. Chính sách bảo hành dưới đây
                áp dụng cho các sản phẩm mua tại cửa hàng hoặc đặt hàng qua website / Zalo / Messenger / hotline.
              </p>

              <h2>1. Phạm vi hỗ trợ</h2>
              <p>Tùy theo nhóm sản phẩm, Win Win áp dụng hình thức hỗ trợ như sau:</p>
              <ul>
                <li>
                  <strong>Đồ chơi điện tử / điều khiển:</strong> được kiểm tra hoạt động trước khi giao. Nếu sản phẩm
                  không khởi động, lỗi điều khiển hoặc thiếu phụ kiện so với mô tả, quý khách vui lòng báo trong vòng
                  <strong>48 giờ</strong> kể từ khi nhận hàng để được kiểm tra và hỗ trợ.
                </li>
                <li>
                  <strong>Đồ chơi lắp ghép / mô hình:</strong> quý khách nên kiểm tra số lượng chi tiết, bao bì và
                  phụ kiện ngay khi nhận. Win Win hỗ trợ đổi hoặc bổ sung khi giao sai hàng, thiếu chi tiết do nhà
                  sản xuất hoặc hư hỏng trong quá trình vận chuyển.
                </li>
                <li>
                  <strong>Đồ chơi không dùng điện:</strong> được hỗ trợ khi có lỗi kỹ thuật, sai mẫu, sai màu hoặc
                  không đúng thông tin đơn hàng. Sản phẩm cần còn nguyên tem, bao bì và chưa qua sử dụng.
                </li>
                <li>
                  <strong>Sản phẩm khác:</strong> điều kiện hỗ trợ sẽ được nhân viên tư vấn rõ khi bán hàng
                  hoặc khi xác nhận đơn.
                </li>
              </ul>

              <h2>2. Thời hạn tiếp nhận yêu cầu</h2>
              <ul>
                <li><strong>Lỗi khi nhận hàng / thiếu phụ kiện:</strong> trong vòng <strong>48 giờ</strong> sau khi nhận hàng.</li>
                <li><strong>Yêu cầu bảo hành kỹ thuật:</strong> theo thời hạn của từng sản phẩm hoặc nhà sản xuất.</li>
                <li>Quá thời hạn trên, Win Win vẫn tiếp nhận phản ánh để hỗ trợ tư vấn, nhưng có thể không áp dụng đổi/bù.</li>
              </ul>

              <h2>3. Điều kiện tiếp nhận bảo hành / đổi hàng</h2>
              <ul>
                <li>Còn hóa đơn, biên nhận, tin nhắn xác nhận đơn hoặc thông tin đơn hàng trên hệ thống.</li>
                <li>Cung cấp hình ảnh/video sản phẩm rõ nét thể hiện tình trạng lỗi (nếu yêu cầu đổi/bù).</li>
                <li>Sản phẩm còn nguyên trạng theo điều kiện từng nhóm hàng (tem, bao bì, hạn sử dụng…).</li>
                <li>Lỗi phát sinh không do bảo quản sai hướng dẫn, va đập sau khi nhận, hoặc sử dụng không đúng cách.</li>
              </ul>

              <h2>4. Trường hợp từ chối bảo hành</h2>
              <p>Win Win xin phép <strong>từ chối bảo hành / đổi trả</strong> trong các trường hợp sau:</p>
              <ul>
                <li>Sản phẩm bị rơi vỡ, ngấm nước, cháy chập hoặc biến dạng do sử dụng sai hướng dẫn.</li>
                <li>Sản phẩm đã qua sử dụng, đã mở bao bì/niêm phong (đối với hàng đóng gói) mà không phải lỗi từ cửa hàng.</li>
                <li>Hư hỏng do bảo quản sai (để ngoài nắng, nhiệt độ cao, ẩm ướt, gần nguồn nhiệt…).</li>
                <li>Hư hỏng do vận chuyển lại bởi bên thứ ba sau khi khách đã nhận hàng.</li>
                <li>Không cung cấp được thông tin đơn hàng / bằng chứng mua hàng cần thiết.</li>
                <li>Yêu cầu vượt quá thời hạn tiếp nhận quy định tại mục 2.</li>
              </ul>

              <h2>5. Hình thức hỗ trợ</h2>
              <p>Tùy tình trạng thực tế và thỏa thuận với khách hàng, Win Win có thể hỗ trợ một trong các hình thức:</p>
              <ul>
                <li>Đổi sản phẩm tương đương.</li>
                <li>Bù sản phẩm / bổ sung phần bị thiếu hoặc lỗi.</li>
                <li>Giảm giá / hoàn tiền một phần hoặc toàn bộ (theo từng trường hợp).</li>
              </ul>
              <p>
                Thời gian xử lý thông thường từ <strong>01 – 03 ngày làm việc</strong> sau khi tiếp nhận đủ thông tin.
                Với lỗi phát hiện ngay khi mở hộp, cửa hàng ưu tiên kiểm tra và phản hồi trong ngày.
              </p>

              <h2>6. Hướng dẫn bảo quản (khuyến nghị)</h2>
              <ul>
                <li>Đồ chơi điện tử: để nơi khô ráo, tránh nước, nguồn nhiệt và tháo pin khi không sử dụng lâu ngày.</li>
                <li>Đồ chơi lắp ghép: cất chi tiết nhỏ đúng hộp, tránh xa trẻ dưới độ tuổi khuyến nghị.</li>
                <li>Luôn đọc hướng dẫn, cảnh báo độ tuổi và vệ sinh sản phẩm theo thông tin trên bao bì.</li>
              </ul>

              <h2>7. Cách liên hệ bảo hành / hỗ trợ</h2>
              <p>Khi cần hỗ trợ, quý khách vui lòng cung cấp: họ tên, số điện thoại, mã/đơn hàng (nếu có), mô tả lỗi và hình ảnh/video.</p>
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
                Win Win luôn sẵn sàng lắng nghe và hỗ trợ quý khách để mang lại trải nghiệm mua sắm tốt nhất.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.theme-portals')
  <script src="100/531/894/themes/1018832/assets/main.js?ww-page-1" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="100/531/894/themes/1018832/assets/defer-scripts.js?ww-page-1" defer fetchpriority="low"></script>
</body>
</html>
