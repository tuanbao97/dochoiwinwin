@php
  $seoTitle = 'Lịch sử mua hàng — Đồ Chơi Win Win';
  $seoDescription = 'Theo dõi các đơn hàng đã đặt tại Đồ Chơi Win Win.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme page">
  @include('UI-FRONTEND.common.header')

  <main>
    <div class="breadcrumbs">
      <div class="container">
        <ul class="breadcrumb py-3 flex flex-wrap items-center text-xs md:text-sm">
          <li>
            <a class="link" href="{{ url('/') }}">Trang chủ</a>
            <span class="mx-1 md:mx-2">&nbsp;/&nbsp;</span>
          </li>
          <li><span class="text-neutral-100">Lịch sử mua hàng</span></li>
        </ul>
      </div>
    </div>

    <section class="section">
      <div class="container">
        <div class="ww-orders">
          <div class="ww-orders__heading">
            <div>
              <h1 class="text-h4 font-semibold mb-1">Lịch sử mua hàng</h1>
              <p class="text-neutral-200 mb-0">Theo dõi trạng thái và chi tiết các đơn hàng của bạn.</p>
            </div>
            <a href="{{ url('/tat-ca-san-pham') }}" class="btn bg-primary text-white font-semibold">Tiếp tục mua sắm</a>
          </div>

          <div class="ww-orders__loading" data-orders-loading>Đang tải đơn hàng…</div>
          <div class="ww-orders__error" data-orders-error hidden></div>
          <div class="ww-orders__empty" data-orders-empty hidden>
            <div class="ww-orders__empty-icon">🛍️</div>
            <h2 class="font-semibold text-h5">Bạn chưa có đơn hàng</h2>
            <p class="text-neutral-200">Các đơn đã đặt bằng tài khoản hoặc email này sẽ xuất hiện tại đây.</p>
            <a href="{{ url('/tat-ca-san-pham') }}" class="btn bg-primary text-white font-semibold">Khám phá sản phẩm</a>
          </div>
          <div class="ww-orders__list" data-orders-list></div>
          <nav class="ww-orders__pagination" data-orders-pagination aria-label="Phân trang đơn hàng"></nav>
        </div>
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.footer')
  @include('UI-FRONTEND.common.theme-portals')

  <script>
  (function () {
    var loginUrl = @json(url('/account/login'));
    var apiUrl = @json(url('/api/auth/storefront-orders'));
    var appUrl = @json(rtrim(url('/'), '/'));
    var listEl = document.querySelector('[data-orders-list]');
    var loadingEl = document.querySelector('[data-orders-loading]');
    var emptyEl = document.querySelector('[data-orders-empty]');
    var errorEl = document.querySelector('[data-orders-error]');
    var paginationEl = document.querySelector('[data-orders-pagination]');
    var accessToken = null;

    try {
      accessToken = localStorage.getItem('ACCESS_TOKEN');
    } catch (e) {}

    if (!accessToken) {
      window.location.replace(loginUrl);
      return;
    }

    function escapeHtml(value) {
      return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function money(value) {
      return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + '₫';
    }

    function dateTime(value) {
      if (!value) return '';
      var date = new Date(value);
      return isNaN(date.getTime()) ? '' : date.toLocaleString('vi-VN');
    }

    function imageUrl(value) {
      if (!value) return '';
      value = String(value);
      if (/^(https?:)?\/\//i.test(value) || value.indexOf('data:') === 0) return value;
      return appUrl + '/' + value.replace(/^\/+/, '');
    }

    function productUrl(handle, productId) {
      var id = Number(productId) || 0;
      var slug = String(handle || '').replace(/^\/+/, '');
      if (slug === '') {
        return id > 0 ? appUrl + '/san-pham/chi-tiet/sp-' + id : '#';
      }
      if (id > 0 && !/-\d+$/.test(slug)) {
        slug += '-' + id;
      }
      return appUrl + '/san-pham/chi-tiet/' + slug;
    }

    function statusClass(status) {
      var allowed = ['PENDING', 'CONFIRMED', 'SHIPPING', 'COMPLETED', 'CANCELLED'];
      return allowed.indexOf(status) >= 0 ? status.toLowerCase() : 'pending';
    }

    function renderItem(item) {
      var image = imageUrl(item.IMAGE);
      var href = productUrl(item.HANDLE, item.PRODUCT_ID);
      return '<div class="ww-orders__product">'
        + (image
          ? '<a href="' + escapeHtml(href) + '" class="ww-orders__product-image"><img src="' + escapeHtml(image) + '" alt="' + escapeHtml(item.NAME) + '" loading="lazy"></a>'
          : '<div class="ww-orders__product-image ww-orders__product-image--empty">🧸</div>')
        + '<div class="ww-orders__product-info">'
        + '<a href="' + escapeHtml(href) + '" class="font-semibold">' + escapeHtml(item.NAME || 'Sản phẩm') + '</a>'
        + '<span>Số lượng: ' + escapeHtml(item.QUANTITY) + '</span>'
        + '</div>'
        + '<strong class="ww-orders__product-price">' + money(item.LINE_TOTAL) + '</strong>'
        + '</div>';
    }

    function renderTracking(icon, label, value, fallback) {
      var isEmpty = !value;
      return '<div class="ww-orders__tracking-item">'
        + '<span class="ww-orders__tracking-icon" aria-hidden="true">' + icon + '</span>'
        + '<span class="ww-orders__tracking-text">'
        + '<span class="ww-orders__tracking-label">' + escapeHtml(label) + '</span>'
        + '<strong class="ww-orders__tracking-value' + (isEmpty ? ' is-empty' : '') + '">'
        + escapeHtml(isEmpty ? fallback : value)
        + '</strong>'
        + '</span>'
        + '</div>';
    }

    function renderOrder(order) {
      var buyer = order.BUYER || {};
      var items = Array.isArray(order.ITEMS) ? order.ITEMS : [];
      var discountText = Number(order.DISCOUNT_AMOUNT || 0) > 0
        ? ' · Giảm giá' + (order.DISCOUNT_CODE ? ' (' + escapeHtml(order.DISCOUNT_CODE) + ')' : '')
          + ': -' + money(order.DISCOUNT_AMOUNT)
        : '';
      var cancelReason = order.STATUS === 'CANCELLED'
        ? '<div class="ww-orders__cancel-reason">'
          + '<span class="ww-orders__cancel-icon" aria-hidden="true">!</span>'
          + '<span><small>Lý do hủy đơn</small><strong>'
          + escapeHtml(order.CANCEL_REASON || 'Sapo chưa cung cấp lý do hủy')
          + '</strong></span></div>'
        : '';
      return '<article class="ww-orders__card">'
        + '<div class="ww-orders__card-head">'
        + '<div><strong>Đơn hàng ' + escapeHtml(order.CODE || ('#' + order.ID)) + '</strong><span>' + escapeHtml(dateTime(order.CREATED_AT)) + '</span></div>'
        + '<span class="ww-orders__status ww-orders__status--' + statusClass(order.STATUS) + '">' + escapeHtml(order.STATUS_LABEL) + '</span>'
        + '</div>'
        + cancelReason
        + '<div class="ww-orders__products">' + items.map(renderItem).join('') + '</div>'
        + '<div class="ww-orders__delivery">'
        + '<div><span>Người nhận</span><strong>' + escapeHtml(buyer.FULL_NAME || '') + '</strong></div>'
        + '<div><span>Điện thoại</span><strong>' + escapeHtml(buyer.PHONE || '') + '</strong></div>'
        + '<div class="ww-orders__address"><span>Địa chỉ</span><strong>' + escapeHtml(buyer.ADDRESS || '') + '</strong></div>'
        + '</div>'
        + '<div class="ww-orders__tracking">'
        + renderTracking('👤', 'Nhân viên phụ trách', order.ASSIGNEE_NAME, 'Chưa phân công')
        + renderTracking('🚚', 'Ngày hẹn giao', dateTime(order.EXPECTED_DELIVERY_DATE), 'Chưa có lịch hẹn')
        + '</div>'
        + '<div class="ww-orders__total"><span class="ww-orders__total-breakdown">Tạm tính: ' + money(order.SUBTOTAL) + ' · Phí vận chuyển: ' + money(order.SHIPPING_FEE) + discountText + '</span>'
        + '<span class="ww-orders__grand-total">Tổng cộng <strong>' + money(order.TOTAL_PRICE) + '</strong></span></div>'
        + '</article>';
    }

    function renderPagination(current, last) {
      paginationEl.innerHTML = '';
      if (last <= 1) return;

      var start = Math.max(1, current - 2);
      var end = Math.min(last, current + 2);
      var pages = [];
      if (start > 1) pages.push(1);
      for (var page = start; page <= end; page++) pages.push(page);
      if (end < last) pages.push(last);

      function button(label, page, disabled, active) {
        var el = document.createElement('button');
        el.type = 'button';
        el.textContent = label;
        el.disabled = disabled;
        if (active) el.classList.add('is-active');
        el.addEventListener('click', function () { load(page, false); });
        paginationEl.appendChild(el);
      }

      button('‹', Math.max(1, current - 1), current === 1, false);
      var previous = 0;
      pages.forEach(function (page) {
        if (previous && page - previous > 1) {
          var dots = document.createElement('span');
          dots.textContent = '…';
          paginationEl.appendChild(dots);
        }
        button(String(page), page, false, page === current);
        previous = page;
      });
      button('›', Math.min(last, current + 1), current === last, false);
    }

    // BE dùng mốc fetch Sapo chung; UI chỉ hiển thị đơn theo email đăng nhập.
    var POLL_INTERVAL = 30000;
    var currentPage = 1;
    var lastSnapshot = '';

    function snapshot(data) {
      var orders = Array.isArray(data.ITEMS) ? data.ITEMS : [];
      return JSON.stringify({
        page: data.CURRENT_PAGE,
        last: data.LAST_PAGE,
        total: data.TOTAL,
        orders: orders.map(function (order) {
          return [
            order.ID,
            order.CODE,
            order.STATUS,
            order.STATUS_LABEL,
            order.TOTAL_QUANTITY,
            order.SUBTOTAL,
            order.SHIPPING_FEE,
            order.DISCOUNT_CODE,
            order.DISCOUNT_AMOUNT,
            order.TOTAL_PRICE,
            order.ASSIGNEE_NAME,
            order.EXPECTED_DELIVERY_DATE,
            order.CANCEL_REASON,
            (Array.isArray(order.ITEMS) ? order.ITEMS : []).map(function (item) {
              return [
                item.SAPO_LINE_ITEM_ID || item.ID,
                item.PRODUCT_ID,
                item.NAME,
                item.QUANTITY,
                item.PRICE,
                item.LINE_TOTAL
              ];
            })
          ];
        })
      });
    }

    function render(data) {
      var orders = Array.isArray(data.ITEMS) ? data.ITEMS : [];
      if (!orders.length) {
        listEl.innerHTML = '';
        paginationEl.innerHTML = '';
        emptyEl.hidden = false;
        return;
      }
      emptyEl.hidden = true;
      listEl.innerHTML = orders.map(renderOrder).join('');
      renderPagination(Number(data.CURRENT_PAGE || 1), Number(data.LAST_PAGE || 1));
    }

    function load(page, silent) {
      currentPage = page;

      if (!silent) {
        loadingEl.hidden = false;
        errorEl.hidden = true;
        emptyEl.hidden = true;
        listEl.innerHTML = '';
        paginationEl.innerHTML = '';
      }

      fetch(apiUrl + '?page=' + page + '&per_page=10', {
        headers: {
          'Authorization': 'Bearer ' + accessToken,
          'Accept': 'application/json'
        }
      })
        .then(function (response) {
          if (response.status === 401) {
            try {
              localStorage.removeItem('ACCESS_TOKEN');
              localStorage.removeItem('REFRESH_TOKEN');
              localStorage.removeItem('AUTH_SCOPE');
            } catch (e) {}
            window.location.replace(loginUrl);
            throw new Error('unauthenticated');
          }
          if (!response.ok) throw new Error('Không thể tải lịch sử mua hàng.');
          return response.json();
        })
        .then(function (payload) {
          var data = payload && payload.DATAS ? payload.DATAS : {};
          loadingEl.hidden = true;

          var current = snapshot(data);
          if (silent && current === lastSnapshot) return;

          lastSnapshot = current;
          render(data);
        })
        .catch(function (error) {
          if (error.message === 'unauthenticated') return;
          // Lần làm mới ngầm thất bại thì giữ nguyên dữ liệu đang hiển thị.
          if (silent) return;
          loadingEl.hidden = true;
          errorEl.textContent = error.message || 'Đã có lỗi xảy ra.';
          errorEl.hidden = false;
        });
    }

    setInterval(function () {
      if (document.hidden) return;
      load(currentPage, true);
    }, POLL_INTERVAL);

    document.addEventListener('visibilitychange', function () {
      if (!document.hidden) load(currentPage, true);
    });

    load(1, false);
  })();
  </script>
  <script src="100/531/894/themes/1018832/assets/main.js?ww-page-1" defer fetchpriority="low"></script>
  @include('UI-FRONTEND.common.cart-scripts')
  <script src="100/531/894/themes/1018832/assets/defer-scripts.js?ww-page-1" defer fetchpriority="low"></script>
</body>
</html>
