@php
  $seoTitle = 'Thanh toán — Win Win';
  $seoDescription = 'Thanh toán đơn hàng tại Đồ Chơi Win Win.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

@php
  $formatPrice = static function (int $amount): string {
      if ($amount <= 0) {
          return '0';
      }
      if ($amount < 1000) {
          return (string) $amount;
      }
      return number_format($amount, 0, ',', '.') . ' ₫';
  };
  $productUrl = static function (array $line): string {
      $handle = trim((string) ($line['handle'] ?? ''));
      $productId = (int) ($line['variant_id'] ?? 0);
      if ($handle === '') {
          return $productId > 0 ? url('san-pham/chi-tiet/sp-' . $productId) : '#';
      }
      if ($productId > 0 && ! preg_match('/-\d+$/', $handle)) {
          $handle .= '-' . $productId;
      }
      return url('san-pham/chi-tiet/' . ltrim($handle, '/'));
  };
  $resolveImageUrl = static function (array $line) use ($appUrl): string {
      $imgRel = $line['image'] ?? '';
      if ($imgRel === '') {
          return asset('image/UI-BACKEND/default-image.png');
      }
      if (preg_match('#^https?://#i', $imgRel) || str_starts_with($imgRel, '//')) {
          return $imgRel;
      }
      $path = ltrim((string) $imgRel, '/');
      if (str_starts_with($path, 'upload/') || str_starts_with($path, 'storage/')) {
          return $appUrl . '/' . $path;
      }
      return asset('UI-FRONTEND/' . $path);
  };
  $checkoutItemsPayload = collect($items ?? [])->map(static function (array $line): array {
      return [
          'PRODUCT_ID' => (int) ($line['variant_id'] ?? 0),
          'QUANTITY' => (float) ($line['quantity'] ?? 0),
          'PRICE' => (float) ($line['price'] ?? 0),
          'TEN_SAN_PHAM' => $line['title'] ?? null,
          'HINH_ANH' => $line['image'] ?? null,
          'HANDLE' => $line['handle'] ?? null,
      ];
  })->values()->all();
@endphp

<body class="ega-theme checkout">@include('UI-FRONTEND.common.header')
  <script>
    (function () {
      var t = document.getElementById('ww-page-title');
      if (t) t.textContent = 'Thanh toán — Win Win';
    })();
  </script>

  <main>
    <div class="breadcrumbs">
      <div class="container">
        <ul class="breadcrumb py-3 flex flex-wrap items-center text-xs md:text-sm">
          <li class="home">
            <a class="link" href="{{ url('/') }}" title="Trang chủ"><span>Trang chủ</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          <li>
            <a class="link" href="{{ url('/cart') }}" title="Giỏ hàng"><span>Giỏ hàng</span></a>
            <span class="mx-1 md:mx-2 inline-block">&nbsp;/&nbsp;</span>
          </li>
          <li><span class="text-neutral-100">Thanh toán</span></li>
        </ul>
      </div>
    </div>

    <section class="section section-checkout" style="--section-margin: 0 0 40px; --section-margin-mb: 0 0 24px">
      <div class="container">
        <h1 class="text-h4 font-semibold mb-5">Thanh toán</h1>

        @if($totalQuantity <= 0)
          <div class="bg-background rounded-lg p-6 text-center">
            <h2 class="text-h6 font-semibold mb-2">Giỏ hàng chưa có sản phẩm</h2>
            <p class="text-neutral-300 mb-4">Bạn hãy chọn sản phẩm trước khi thanh toán.</p>
            <a href="{{ url('/') }}" class="btn font-semibold bg-primary text-white inline-flex items-center justify-center px-5 py-3 rounded">
              Tiếp tục mua sắm
            </a>
          </div>
        @else
          <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,40rem)] items-start">
            <section class="bg-background rounded-lg p-4 md:p-6 min-w-0 max-w-full overflow-x-clip">
              <h2 class="text-h5 font-semibold mb-4">Thông tin nhận hàng</h2>
              <form id="ww-checkout-form" class="grid gap-4 min-w-0 max-w-full" method="post" action="{{ url('/api/public/transaction/place-order') }}" novalidate>
                @csrf
                <div class="min-w-0">
                  <label class="block font-semibold mb-1" for="checkout-name">Họ và tên <span class="text-error">*</span></label>
                  <input id="checkout-name" name="name" class="form-input w-full max-w-full rounded border-neutral-50" placeholder="Nhập họ tên người nhận" autocomplete="name">
                  <span class="error-message text-error text-sm mt-1 block" id="MSG_HO_TEN" data-field="name"></span>
                </div>
                <div class="grid gap-4 md:grid-cols-2 min-w-0">
                  <div class="min-w-0">
                    <label class="block font-semibold mb-1" for="checkout-phone">Số điện thoại <span class="text-error">*</span></label>
                    <input id="checkout-phone" name="phone" class="form-input w-full max-w-full rounded border-neutral-50" placeholder="090..." autocomplete="tel" inputmode="tel">
                    <span class="error-message text-error text-sm mt-1 block" id="MSG_SO_DIEN_THOAI" data-field="phone"></span>
                  </div>
                  <div class="min-w-0">
                    <label class="block font-semibold mb-1" for="checkout-email">Email</label>
                    <input id="checkout-email" name="email" type="text" class="form-input w-full max-w-full rounded border-neutral-50" placeholder="email@example.com" autocomplete="email">
                    <span class="error-message text-error text-sm mt-1 block" id="MSG_EMAIL" data-field="email"></span>
                  </div>
                </div>
                <div class="min-w-0">
                  <label class="block font-semibold mb-1" for="checkout-address">Địa chỉ nhận hàng <span class="text-error">*</span></label>
                  <textarea id="checkout-address" name="address" rows="3" class="form-textarea w-full max-w-full rounded border-neutral-50" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" autocomplete="street-address"></textarea>
                  <span class="error-message text-error text-sm mt-1 block" id="MSG_DIA_CHI" data-field="address"></span>
                </div>
                <div class="min-w-0">
                  <label class="block font-semibold mb-1" for="checkout-note">Ghi chú đơn hàng</label>
                  <textarea id="checkout-note" name="note" rows="3" class="form-textarea w-full max-w-full rounded border-neutral-50" placeholder="Thời gian giao hàng, lời nhắn..."></textarea>
                  <span class="error-message text-error text-sm mt-1 block" id="MSG_GHI_CHU" data-field="note"></span>
                </div>
                <p id="ww-checkout-error" class="text-error text-sm mb-0" hidden></p>
                <span class="error-message text-error text-sm block" id="MSG_ITEMS"></span>
                <button type="submit" id="ww-checkout-submit" class="btn bg-primary text-white font-semibold rounded px-5 py-3">
                  Hoàn tất đặt hàng
                </button>
              </form>
              <script type="application/json" id="ww-checkout-items">@json($checkoutItemsPayload)</script>
            </section>

            <aside class="bg-background rounded-lg p-4 md:p-6 lg:sticky lg:top-[calc(var(--header-height)+1.6rem)] shadow-sm min-w-0 max-w-full overflow-x-clip">
              <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-h5 font-semibold">Đơn hàng của bạn</h2>
                <span class="text-sm text-neutral-300 whitespace-nowrap">{{ $totalQuantity }} sản phẩm</span>
              </div>
              <div class="checkout-order-list max-h-[42rem] overflow-y-auto pr-1 space-y-3">
                @foreach($items as $line)
                  @php
                    $imgUrl = $resolveImageUrl($line);
                    $itemUrl = $productUrl($line);
                  @endphp
                  <div class="checkout-order-item flex items-center gap-3 rounded-lg border border-neutral-50 bg-neutral-50/20 p-2.5">
                    <a href="{{ $itemUrl }}" class="shrink-0" title="{{ $line['title'] ?? 'Sản phẩm' }}">
                      <img class="w-[4.4rem] h-[4.4rem] object-contain rounded border border-neutral-50 bg-white" src="{{ $imgUrl }}" alt="{{ $line['title'] ?? 'Sản phẩm' }}" loading="lazy">
                    </a>
                    <div class="min-w-0 flex-1 flex items-center gap-2">
                      <a href="{{ $itemUrl }}" class="link font-semibold truncate leading-snug min-w-0">{{ $line['title'] ?? 'Sản phẩm' }}</a>
                      <span class="text-sm text-neutral-300 shrink-0">x{{ (int) ($line['quantity'] ?? 1) }}</span>
                    </div>
                    <div class="shrink-0 font-semibold text-primary whitespace-nowrap text-right text-sm">
                      {{ $formatPrice((int) ($line['line_price'] ?? 0)) }}
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="border-t border-neutral-50 mt-4 pt-4 space-y-2">
                <div class="flex justify-between">
                  <span>Tạm tính</span>
                  <span class="font-semibold">{{ $formatPrice($totalPrice) }}</span>
                </div>
                <div class="flex justify-between text-neutral-300">
                  <span>Phí vận chuyển</span>
                  <span>Liên hệ</span>
                </div>
                <div class="flex justify-between text-h6 font-bold text-primary pt-2">
                  <span>Tổng cộng</span>
                  <span>{{ $formatPrice($totalPrice) }}</span>
                </div>
              </div>

              <a href="{{ url('/cart') }}" class="btn w-full mt-4 border border-neutral-50 bg-background font-semibold rounded px-5 py-3 inline-flex justify-center">
                Quay lại giỏ hàng
              </a>
            </aside>
          </div>
        @endif
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.theme-portals')
  <script src="100/531/894/themes/1018832/assets/main.js?ww-checkout-1"></script>
  @include('UI-FRONTEND.common.cart-scripts')

  <div id="ww-order-success-modal" class="ww-order-success" hidden aria-hidden="true">
    <div class="ww-order-success__overlay"></div>
    <div class="ww-order-success__panel" role="dialog" aria-modal="true" aria-labelledby="ww-order-success-title">
      <div class="ww-order-success__icon" aria-hidden="true">
        <svg viewBox="0 0 52 52" width="40" height="40">
          <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="2" opacity="0.25"></circle>
          <path fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M14 27l8 8 16-18"></path>
        </svg>
      </div>
      <h2 id="ww-order-success-title" class="ww-order-success__title text-h4 font-semibold">Đặt hàng thành công</h2>
      <p class="ww-order-success__code text-h6 font-semibold" id="ww-order-success-code" hidden></p>
      <p class="ww-order-success__desc text-h6">
        Cảm ơn bạn đã tin tưởng <span style="white-space:nowrap">Win&nbsp;Win</span>.<br>
        Chúng tôi sẽ liên hệ bạn sớm nhất để xác nhận đơn hàng.
      </p>
      <button type="button" id="ww-order-success-home" class="ww-order-success__btn btn bg-primary text-white text-h6 font-semibold">
        Về trang chủ
      </button>
    </div>
  </div>

  <style>
    .ww-order-success {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
    }
    .ww-order-success[hidden] { display: none !important; }
    .ww-order-success__overlay {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.55);
      backdrop-filter: blur(2px);
    }
    .ww-order-success__panel {
      position: relative;
      z-index: 1;
      width: min(100%, 34rem);
      background: #fff;
      border-radius: 1rem;
      padding: 1.75rem 1.5rem 1.35rem;
      text-align: center;
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.22);
      animation: wwOrderSuccessIn 0.28s ease-out;
    }
    @keyframes wwOrderSuccessIn {
      from { opacity: 0; transform: translateY(10px) scale(0.97); }
      to { opacity: 1; transform: none; }
    }
    .ww-order-success__icon {
      width: 3.5rem;
      height: 3.5rem;
      margin: 0 auto 0.75rem;
      border-radius: 999px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #16a34a;
      background: rgba(22, 163, 74, 0.12);
    }
    .ww-order-success__title {
      margin: 0 0 0.4rem;
      color: #0f172a;
      line-height: 1.3;
    }
    .ww-order-success__code {
      margin: 0 0 0.75rem;
      display: inline-block;
      padding: 0.35rem 0.85rem;
      border-radius: 999px;
      background: #f0fdf4;
      color: #15803d;
    }
    .ww-order-success__desc {
      margin: 0 auto 1.25rem;
      max-width: 30rem;
      color: #64748b;
      line-height: 1.55;
    }
    .ww-order-success__btn {
      width: 100%;
      border: 0;
      border-radius: 0.5rem;
      padding: 0.75rem 1rem;
      cursor: pointer;
      transition: transform 0.15s ease, filter 0.15s ease;
    }
    .ww-order-success__btn:hover { filter: brightness(0.95); }
    .ww-order-success__btn:active { transform: scale(0.98); }
    html.ww-order-success-open,
    html.ww-order-success-open body {
      overflow: hidden;
    }
  </style>

  <script>
  (function () {
    var form = document.getElementById('ww-checkout-form');
    if (!form) return;
    var btn = document.getElementById('ww-checkout-submit');
    var errEl = document.getElementById('ww-checkout-error');
    var placeUrl = @json(url('/api/public/transaction/place-order'));
    var homeUrl = @json(url('/'));
    var successModal = document.getElementById('ww-order-success-modal');
    var successCodeEl = document.getElementById('ww-order-success-code');
    var successHomeBtn = document.getElementById('ww-order-success-home');
    var fieldMap = {
      HO_TEN: 'name',
      SO_DIEN_THOAI: 'phone',
      EMAIL: 'email',
      DIA_CHI: 'address',
      GHI_CHU: 'note'
    };

    function clearFieldErrors() {
      form.querySelectorAll('.error-message').forEach(function (el) {
        el.textContent = '';
      });
      form.querySelectorAll('.form-input, .form-textarea').forEach(function (el) {
        el.style.borderColor = '';
      });
      if (errEl) {
        errEl.hidden = true;
        errEl.textContent = '';
      }
    }

    function firstMessage(value) {
      if (Array.isArray(value)) return value[0] || '';
      if (value && typeof value === 'object') return firstMessage(Object.values(value));
      return value ? String(value) : '';
    }

    function showFieldErrors(errors) {
      if (!errors || typeof errors !== 'object') return false;
      var hasField = false;
      var firstFocus = null;
      Object.keys(errors).forEach(function (key) {
        var rootKey = String(key).split('.')[0];
        var msgEl = document.getElementById('MSG_' + rootKey);
        var message = firstMessage(errors[key]);
        if (!message) return;
        hasField = true;
        if (msgEl) msgEl.textContent = message;
        var fieldName = fieldMap[rootKey];
        var input = fieldName ? form.querySelector('[name="' + fieldName + '"]') : null;
        if (input) {
          input.style.borderColor = 'var(--color-error, #e11d48)';
          if (!firstFocus) firstFocus = input;
        }
      });
      if (firstFocus) firstFocus.focus();
      return hasField;
    }

    function showOrderSuccessModal(orderId) {
      if (!successModal) {
        window.location.href = homeUrl;
        return;
      }
      if (successCodeEl) {
        if (orderId) {
          successCodeEl.hidden = false;
          successCodeEl.textContent = 'Mã đơn #' + orderId;
        } else {
          successCodeEl.hidden = true;
          successCodeEl.textContent = '';
        }
      }
      successModal.hidden = false;
      successModal.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('ww-order-success-open');
      if (successHomeBtn) successHomeBtn.focus();
    }

    function goHomeAfterSuccess() {
      window.location.href = homeUrl;
    }

    if (successHomeBtn) {
      successHomeBtn.addEventListener('click', goHomeAfterSuccess);
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearFieldErrors();
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';
      }

      var itemsEl = document.getElementById('ww-checkout-items');
      var items = [];
      try {
        items = itemsEl ? JSON.parse(itemsEl.textContent || '[]') : [];
      } catch (err) {
        items = [];
      }

      var payload = {
        name: form.name.value,
        phone: form.phone.value,
        email: form.email.value,
        address: form.address.value,
        note: form.note.value,
        ITEMS: items
      };

      fetch(placeUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      })
        .then(function (r) {
          return r.json().then(function (data) {
            return { ok: r.ok, status: r.status, data: data };
          });
        })
        .then(function (res) {
          if (!res.ok || (res.data && res.data.STATUS === false)) {
            var errors = res.data && res.data.ERRORS;
            if (showFieldErrors(errors)) {
              throw new Error('');
            }
            var msg = (res.data && res.data.STATUS_DETAIL) || 'Đặt hàng thất bại.';
            if (errors && typeof errors === 'object') {
              msg = Object.keys(errors).map(function (k) {
                return firstMessage(errors[k]);
              }).filter(Boolean).join(' ') || msg;
            } else if (typeof errors === 'string') {
              msg = errors;
            }
            throw new Error(msg);
          }
          var id = res.data && res.data.DATAS && res.data.DATAS.TRANSACTION && res.data.DATAS.TRANSACTION.ID;
          return fetch(@json(url('/cart/clear')), {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
            },
            credentials: 'same-origin'
          }).then(function () {
            try {
              if (window.Bizweb && window.Bizweb.cart) {
                window.Bizweb.cart.item_count = 0;
              }
              document.querySelectorAll('.cart-count, .header-cart-count, [data-cart-count]').forEach(function (el) {
                el.textContent = '0';
              });
            } catch (e) {}
            showOrderSuccessModal(id);
          });
        })
        .catch(function (err) {
          var message = (err && err.message) || '';
          if (!message) return;
          if (errEl) {
            errEl.hidden = false;
            errEl.textContent = message;
          }
        })
        .finally(function () {
          if (btn && (!successModal || successModal.hidden)) {
            btn.disabled = false;
            btn.textContent = 'Hoàn tất đặt hàng';
          }
        });
    });
  })();
  </script>
  <script src="100/531/894/themes/1018832/assets/defer-scripts.js?ww-checkout-1" defer fetchpriority="low"></script>
</body>
</html>
