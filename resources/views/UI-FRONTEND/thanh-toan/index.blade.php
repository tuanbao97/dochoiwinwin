@php
  $seoTitle = 'Thanh toán — Win Win';
  $seoDescription = 'Thanh toán đơn hàng tại Đồ Chơi Win Win.';
@endphp
@include('UI-FRONTEND.san-pham.partials.product-detail-head')

<body class="ega-theme checkout">@include('UI-FRONTEND.common.header')
  <script>
    (function () {
      var t = document.getElementById('ww-page-title');
      if (t) t.textContent = 'Thanh toán — Win Win';
    })();
  </script>

  <main class="ww-co">
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
        <div class="ww-co__head">
          <h1 class="ww-co__title">Thanh toán</h1>
          <a class="ww-co__back" href="{{ url('/cart') }}">
            <i class="icon icon-arrow-left" aria-hidden="true"></i> Quay lại giỏ hàng
          </a>
        </div>

        @if(session('cart_stock_notices'))
          <div class="ww-co__notice">
            @foreach((array) session('cart_stock_notices') as $notice)
              <div>{{ $notice }}</div>
            @endforeach
          </div>
        @endif

        @if($totalQuantity <= 0)
          <div class="ww-co__empty">
            <div class="ww-co__empty-icon" aria-hidden="true">🛒</div>
            <h2>Giỏ hàng chưa có sản phẩm</h2>
            <p>Bạn hãy chọn sản phẩm yêu thích trước khi thanh toán nhé.</p>
            <a href="{{ url('/') }}" class="btn ww-co__empty-btn">Tiếp tục mua sắm</a>
          </div>
        @else
          <div class="ww-co__grid">
            <div class="ww-co__main">
              <div class="ww-co-card" id="ww-checkout-login-hint" hidden>
                <div class="ww-co-hint">
                  <span class="ww-co-hint__icon" aria-hidden="true">👋</span>
                  <div>
                    <strong>Đã có tài khoản?</strong>
                    <p>Đăng nhập để tự động điền thông tin và theo dõi đơn hàng dễ dàng hơn.</p>
                  </div>
                  <a class="ww-co-hint__btn" href="{{ url('/account/login') }}?redirect={{ urlencode(url('/thanh-toan')) }}">Đăng nhập</a>
                </div>
              </div>

              <div class="ww-co-card">
                <h2 class="ww-co-card__title"><span class="ww-co-step">1</span> Thông tin nhận hàng</h2>
                <form id="ww-checkout-form" class="ww-co-form" method="post" action="{{ url('/api/public/transaction/place-order') }}" novalidate>
                  @csrf
                  <div class="ww-co-field ww-co-field--full">
                    <label for="checkout-name">Họ và tên <span class="ww-co-req">*</span></label>
                    <input id="checkout-name" name="name" type="text" placeholder="Nhập họ tên người nhận" autocomplete="name">
                    <span class="ww-co-error" id="MSG_HO_TEN"></span>
                  </div>
                  <div class="ww-co-field">
                    <label for="checkout-phone">Số điện thoại <span class="ww-co-req">*</span></label>
                    <input id="checkout-phone" name="phone" type="tel" placeholder="Ví dụ: 0909 123 456" autocomplete="tel" inputmode="tel">
                    <span class="ww-co-error" id="MSG_SO_DIEN_THOAI"></span>
                  </div>
                  <div class="ww-co-field">
                    <label for="checkout-email">Email <span class="ww-co-optional">(không bắt buộc)</span></label>
                    <input id="checkout-email" name="email" type="text" placeholder="email@example.com" autocomplete="email">
                    <span class="ww-co-error" id="MSG_EMAIL"></span>
                  </div>
                  <div class="ww-co-field ww-co-field--full">
                    <label for="checkout-address">Địa chỉ nhận hàng <span class="ww-co-req">*</span></label>
                    <textarea id="checkout-address" name="address" rows="2" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" autocomplete="street-address"></textarea>
                    <span class="ww-co-error" id="MSG_DIA_CHI"></span>
                  </div>
                  <div class="ww-co-field ww-co-field--full">
                    <label for="checkout-note">Ghi chú đơn hàng <span class="ww-co-optional">(không bắt buộc)</span></label>
                    <textarea id="checkout-note" name="note" rows="2" placeholder="Thời gian muốn nhận hàng, lời nhắn cho shop..."></textarea>
                    <span class="ww-co-error" id="MSG_GHI_CHU"></span>
                  </div>
                  <span class="ww-co-error ww-co-field--full" id="MSG_ITEMS"></span>
                  <p class="ww-co-alert ww-co-field--full" id="ww-checkout-error" hidden></p>
                </form>
              </div>

              <div class="ww-co-card">
                <h2 class="ww-co-card__title"><span class="ww-co-step">2</span> Phương thức thanh toán</h2>
                <div class="ww-co-pay is-active">
                  <span class="ww-co-pay__check" aria-hidden="true">✓</span>
                  <div>
                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                    <p>Bạn kiểm tra hàng rồi mới thanh toán cho đơn vị vận chuyển.</p>
                  </div>
                </div>
                <p class="ww-co-pay__note">Cần chuyển khoản trước hoặc xuất hóa đơn VAT? Ghi chú giúp shop ở mục Ghi chú đơn hàng, nhân viên sẽ liên hệ xác nhận.</p>
              </div>
            </div>

            <aside class="ww-co__side" id="ww-checkout-summary">
              @include('UI-FRONTEND.thanh-toan.partials.order-summary')
            </aside>
          </div>

          <div class="ww-co-bar" id="ww-checkout-bar">
            <div class="ww-co-bar__info">
              <span>Tổng cộng</span>
              <strong id="ww-checkout-bar-total">—</strong>
            </div>
            <button type="submit" form="ww-checkout-form" class="ww-co-bar__btn" data-ww-submit>ĐẶT HÀNG</button>
          </div>
        @endif
      </div>
    </section>
  </main>

  @include('UI-FRONTEND.common.theme-portals')
  <script src="100/531/894/themes/1018832/assets/main.js?ww-checkout-2"></script>
  @include('UI-FRONTEND.common.cart-scripts')

  <div id="ww-voucher-picker" class="ww-voucher-picker" hidden aria-hidden="true">
    <button type="button" class="ww-voucher-picker__overlay" data-ww-close-vouchers aria-label="Đóng danh sách mã giảm giá"></button>
    <div class="ww-voucher-picker__panel" role="dialog" aria-modal="true" aria-labelledby="ww-voucher-picker-title">
      <div class="ww-voucher-picker__head">
        <h2 id="ww-voucher-picker-title">Chọn Voucher</h2>
        <p>Mã có nhãn “Dùng chung được” áp được cùng một mã khác.</p>
        <button type="button" class="ww-voucher-picker__close" data-ww-close-vouchers aria-label="Đóng">×</button>
      </div>
      <div class="ww-voucher-picker__input">
        <input id="ww-voucher-code" type="text" maxlength="255" autocomplete="off" placeholder="Nhập mã voucher" spellcheck="false">
        <button type="button" id="ww-voucher-apply">ÁP DỤNG</button>
      </div>
      <p class="ww-voucher-picker__msg" id="ww-voucher-picker-msg" hidden></p>
      <div class="ww-voucher-picker__list" id="ww-voucher-list"></div>
      <div class="ww-voucher-picker__foot">
        <button type="button" class="ww-voucher-picker__back" data-ww-close-vouchers>TRỞ LẠI</button>
        <button type="button" class="ww-voucher-picker__ok" id="ww-voucher-confirm">OK</button>
      </div>
    </div>
  </div>

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
      <div class="ww-order-success__actions">
        <a href="{{ url('/account/orders') }}" class="ww-order-success__link btn" id="ww-order-success-orders" hidden>Xem đơn hàng</a>
        <button type="button" id="ww-order-success-home" class="ww-order-success__btn btn bg-primary text-white text-h6 font-semibold">
          Về trang chủ
        </button>
      </div>
    </div>
  </div>

  <script>
  (function () {
    var form = document.getElementById('ww-checkout-form');
    if (!form) return;

    var summaryRoot = document.getElementById('ww-checkout-summary');
    var errEl = document.getElementById('ww-checkout-error');
    var barTotalEl = document.getElementById('ww-checkout-bar-total');
    var loginHint = document.getElementById('ww-checkout-login-hint');
    var successModal = document.getElementById('ww-order-success-modal');
    var successCodeEl = document.getElementById('ww-order-success-code');
    var successHomeBtn = document.getElementById('ww-order-success-home');
    var successOrdersLink = document.getElementById('ww-order-success-orders');

    var placeUrl = @json(url('/api/public/transaction/place-order'));
    var authenticatedPlaceUrl = @json(url('/api/auth/storefront/place-order'));
    var voucherListUrl = @json(url('/api/public/voucher/list'));
    var voucherQuoteUrl = @json(url('/api/public/voucher/quote'));
    var summaryUrl = @json(url('/thanh-toan')) + '?view=summary';
    var cartChangeUrl = @json(url('/cart/change'));
    var cartClearUrl = @json(url('/cart/clear'));
    var profileUrl = @json(url('/api/auth/storefront-profile'));
    var homeUrl = @json(url('/'));

    var accessToken = null;
    try {
      accessToken = localStorage.getItem('ACCESS_TOKEN');
    } catch (e) {}

    if (!accessToken && loginHint) loginHint.hidden = false;
    if (accessToken && successOrdersLink) successOrdersLink.hidden = false;

    var state = readState();
    var appliedVoucherCodes = [];
    var pendingVoucherCodes = [];
    var voucherCache = null;
    var voucherProgressTimer = null;
    var submitting = false;

    var fieldMap = {
      HO_TEN: 'name',
      SO_DIEN_THOAI: 'phone',
      EMAIL: 'email',
      DIA_CHI: 'address',
      GHI_CHU: 'note'
    };

    function csrfToken() {
      var input = form.querySelector('[name="_token"]');
      return input ? input.value : '';
    }

    function money(value) {
      return Math.max(0, Math.round(Number(value) || 0)).toLocaleString('vi-VN') + ' ₫';
    }

    function readJson(id, fallback) {
      var el = document.getElementById(id);
      if (!el) return fallback;
      try {
        return JSON.parse(el.textContent || '');
      } catch (err) {
        return fallback;
      }
    }

    function readState() {
      var raw = readJson('ww-checkout-state', {});
      return {
        subtotal: Math.max(0, Math.round(Number(raw.subtotal) || 0)),
        shipping: Math.max(0, Math.round(Number(raw.shipping) || 0)),
        total: Math.max(0, Math.round(Number(raw.total) || 0)),
        quantity: Math.max(0, Math.round(Number(raw.quantity) || 0))
      };
    }

    function checkoutItems() {
      var items = readJson('ww-checkout-items', []);
      return Array.isArray(items) ? items : [];
    }

    function el(id) {
      return document.getElementById(id);
    }

    function syncBar() {
      var totalEl = el('ww-checkout-total');
      if (barTotalEl && totalEl) barTotalEl.textContent = totalEl.textContent;
    }

    function syncCartBadges(quantity) {
      document.querySelectorAll('.cart-count__num').forEach(function (node) {
        node.textContent = String(quantity);
      });
      document.querySelectorAll('.cart-count').forEach(function (node) {
        if (!node.querySelector('.cart-count__num')) node.textContent = String(quantity);
      });
    }

    function setSubmitDisabled(disabled, label) {
      document.querySelectorAll('#ww-checkout-submit, [data-ww-submit]').forEach(function (node) {
        node.disabled = disabled;
        if (label) {
          if (!node.dataset.defaultLabel) node.dataset.defaultLabel = node.textContent;
          node.textContent = label;
        } else if (node.dataset.defaultLabel) {
          node.textContent = node.dataset.defaultLabel;
        }
      });
    }

    /* ------------------------------------------------------------------ *
     * Tóm tắt đơn hàng: sửa số lượng, xóa dòng, tải lại từ server
     * ------------------------------------------------------------------ */

    function setSummaryBusy(busy) {
      if (summaryRoot) summaryRoot.classList.toggle('is-busy', !!busy);
    }

    function showLineMessage(lineIndex, message) {
      if (!summaryRoot || !message) return;
      var item = summaryRoot.querySelector('.ww-sum__item[data-line-index="' + lineIndex + '"]');
      if (!item) return;
      var box = item.querySelector('.ww-sum__error');
      if (!box) {
        box = document.createElement('p');
        box.className = 'ww-sum__error';
        item.appendChild(box);
      }
      box.textContent = message;
    }

    function reloadSummary(pendingMessage) {
      setSummaryBusy(true);
      return fetch(summaryUrl, {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (response) {
          if (!response.ok) throw new Error('summary');
          return response.text();
        })
        .then(function (html) {
          if (!summaryRoot) return;
          summaryRoot.innerHTML = html;
          state = readState();
          syncCartBadges(state.quantity);
          if (state.quantity <= 0) {
            window.location.reload();
            return;
          }
          if (pendingMessage) showLineMessage(pendingMessage.line, pendingMessage.text);
          updateVoucherHighlight();
          if (appliedVoucherCodes.length) {
            applyVoucherCodes(appliedVoucherCodes, true);
          }
          syncBar();
          refreshVoucherHighlight();
        })
        .catch(function () {})
        .finally(function () {
          setSummaryBusy(false);
        });
    }

    function changeLine(lineIndex, quantity) {
      setSummaryBusy(true);
      fetch(cartChangeUrl + '?line=' + encodeURIComponent(lineIndex) + '&quantity=' + encodeURIComponent(quantity), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (response) {
          if (response.ok) return null;
          return response.json().catch(function () {
            return {};
          }).then(function (payload) {
            throw new Error(payload.message || 'Không cập nhật được số lượng.');
          });
        })
        .then(function () {
          return reloadSummary();
        })
        .catch(function (error) {
          reloadSummary({ line: lineIndex, text: error && error.message ? error.message : '' });
        });
    }

    if (summaryRoot) {
      var quantityTimers = {};

      summaryRoot.addEventListener('change', function (e) {
        var input = e.target;
        if (!input || input.name !== 'Lines') return;
        var lineIndex = parseInt(input.getAttribute('data-line-index') || '0', 10);
        var quantity = Math.max(1, Math.floor(Number(input.value) || 1));
        if (!lineIndex) return;
        clearTimeout(quantityTimers[lineIndex]);
        quantityTimers[lineIndex] = setTimeout(function () {
          changeLine(lineIndex, quantity);
        }, 350);
      });

      summaryRoot.addEventListener('click', function (e) {
        var openVouchers = e.target.closest('[data-ww-open-vouchers]');
        if (openVouchers) {
          openVoucherPicker();
          return;
        }

        var toggle = e.target.closest('[data-ww-summary-toggle]');
        if (toggle) {
          var card = summaryRoot.querySelector('.ww-sum');
          if (!card) return;
          var open = card.classList.toggle('is-open');
          toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
          var label = toggle.querySelector('[data-ww-summary-toggle-text]');
          if (label) label.textContent = open ? 'Thu gọn' : 'Xem chi tiết';
          return;
        }

        var removeBtn = e.target.closest('[data-ww-remove-line]');
        if (removeBtn) {
          var line = parseInt(removeBtn.getAttribute('data-ww-remove-line') || '0', 10);
          if (line) changeLine(line, 0);
          return;
        }

        var chipRemove = e.target.closest('[data-ww-remove-voucher]');
        if (chipRemove) {
          var dropped = String(chipRemove.getAttribute('data-ww-remove-voucher') || '').toUpperCase();
          var rest = appliedVoucherCodes.filter(function (code) {
            return code !== dropped;
          });
          if (rest.length) {
            applyVoucherCodes(rest, true);
          } else {
            resetVoucher('');
          }
        }
      });
    }

    /* ------------------------------------------------------------------ *
     * Mã giảm giá
     * ------------------------------------------------------------------ */

    function escapeHtml(value) {
      return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function voucherDate(value) {
      if (!value) return '';
      var date = new Date(value);
      if (Number.isNaN(date.getTime())) return '';
      return date.toLocaleDateString('vi-VN');
    }

    function voucherMeta(voucher) {
      var parts = [];
      if (Number(voucher.min_subtotal || 0) > 0) {
        parts.push('Đơn từ ' + money(voucher.min_subtotal));
      }
      var expiry = voucherDate(voucher.ends_at);
      if (expiry) parts.push('HSD ' + expiry);
      if (voucher.remaining_uses !== null && voucher.remaining_uses !== undefined) {
        parts.push('Còn ' + Number(voucher.remaining_uses) + ' lượt');
      }
      return parts.filter(Boolean);
    }

    function voucherCardHtml(voucher) {
      var code = String(voucher.code || '').toUpperCase();
      var eligible = voucher.eligible === true;
      var stackable = voucher.stackable === true;
      var picked = pendingVoucherCodes.indexOf(code) !== -1;
      var meta = voucherMeta(voucher).join(' · ');
      var statusText = eligible && Number(voucher.discount_amount || 0) > 0
        ? 'Đơn này giảm ' + money(voucher.discount_amount)
        : (voucher.message || 'Chưa đủ điều kiện');
      var headline = voucher.benefit_headline || voucher.benefit || voucher.title;
      var note = voucher.benefit_note || '';
      var desc = String(voucher.description || voucher.title || '');

      return '<div class="ww-vcard' + (eligible ? ' is-ok' : ' is-off') +
        (stackable ? ' is-stack' : '') + (picked ? ' is-picked' : '') + '"' +
        (eligible ? ' data-ww-pick-voucher="' + escapeHtml(code) + '" role="button" tabindex="0"' : '') + '>' +
        '<div class="ww-vcard__stamp">' +
          '<b>' + escapeHtml(headline) + '</b>' +
          (note ? '<small>' + escapeHtml(note) + '</small>' : '') +
        '</div>' +
        '<div class="ww-vcard__info">' +
          '<span class="ww-vcard__code">' + escapeHtml(code) + '</span>' +
          (stackable ? '<span class="ww-vcard__tag">Dùng chung được</span>' : '') +
          (desc ? '<p class="ww-vcard__desc">' + escapeHtml(desc) + '</p>' : '') +
          (meta ? '<p class="ww-vcard__meta">' + escapeHtml(meta) + '</p>' : '') +
          '<p class="ww-vcard__note">' + escapeHtml(statusText) + '</p>' +
        '</div>' +
        '<span class="ww-vcard__radio" aria-hidden="true"></span>' +
      '</div>';
    }

    function renderVoucherList(vouchers) {
      var list = el('ww-voucher-list');
      if (!list) return;
      if (!Array.isArray(vouchers) || !vouchers.length) {
        list.innerHTML = '<div class="ww-voucher-picker__empty">Hiện chưa có mã giảm giá khả dụng.</div>';
        return;
      }

      var rank = function (voucher) {
        return (voucher.stackable === true ? 0 : 2) + (voucher.eligible === true ? 0 : 1);
      };
      var ordered = vouchers.slice().sort(function (a, b) {
        return rank(a) - rank(b);
      });

      list.innerHTML = ordered.map(voucherCardHtml).join('');
    }

    function updateVoucherHighlight() {
      var countEl = document.querySelector('[data-ww-voucher-count]');
      var badge = document.querySelector('[data-ww-voucher-badge]');
      if (!voucherCache) return;

      var eligible = voucherCache.filter(function (voucher) {
        return voucher.eligible === true;
      }).length;

      if (badge) {
        badge.hidden = eligible <= 0;
        badge.textContent = String(eligible);
      }
      if (countEl) {
        if (eligible > 0) {
          countEl.textContent = 'Có ' + eligible + ' mã dùng được cho đơn này';
        } else if (voucherCache.length > 0) {
          countEl.textContent = voucherCache.length + ' mã đang có, xem điều kiện áp dụng';
        } else {
          countEl.textContent = 'Hiện chưa có mã giảm giá khả dụng';
        }
      }
    }

    function voucherStackable(code) {
      var upper = String(code || '').trim().toUpperCase();
      var found = (voucherCache || []).find(function (voucher) {
        return String(voucher.code || '').toUpperCase() === upper;
      });
      return !!(found && found.stackable);
    }

    function mergeVoucherCodes(nextCode, current) {
      var next = String(nextCode || '').trim().toUpperCase();
      var base = (current || appliedVoucherCodes).slice();
      if (!next) return base;
      var merged = base.filter(function (code) {
        if (code === next) return false;
        return voucherStackable(next) || voucherStackable(code);
      });
      merged.push(next);
      return merged.slice(-2);
    }

    function setAppliedVoucherUi(codes) {
      var chips = el('ww-voucher-chips');
      var cta = document.querySelector('.ww-voucher-cta');
      var actionText = document.querySelector('[data-ww-voucher-action-text]');
      var list = Array.isArray(codes) ? codes.filter(Boolean) : [];

      if (cta) cta.classList.toggle('is-applied', list.length > 0);
      if (actionText) actionText.textContent = list.length ? 'Đổi mã' : 'Chọn mã';
      if (!chips) return;

      chips.hidden = list.length === 0;
      chips.innerHTML = list.map(function (code) {
        return '<span class="ww-voucher-chip">' + escapeHtml(code) +
          '<button type="button" data-ww-remove-voucher="' + escapeHtml(code) +
          '" aria-label="Bỏ mã ' + escapeHtml(code) + '">×</button></span>';
      }).join('');
    }

    function fetchVoucherList() {
      return fetch(voucherListUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken()
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          EMAIL: form.email.value.trim() || null,
          SO_DIEN_THOAI: form.phone.value.trim() || null,
          ITEMS: checkoutItems()
        })
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { ok: response.ok, data: data };
          });
        })
        .then(function (res) {
          if (!res.ok || !res.data || res.data.STATUS === false) {
            throw new Error((res.data && res.data.STATUS_DETAIL) || 'Không tải được mã giảm giá.');
          }
          var vouchers = res.data.DATAS && res.data.DATAS.VOUCHERS;
          voucherCache = Array.isArray(vouchers) ? vouchers : [];
          updateVoucherHighlight();
          return voucherCache;
        });
    }

    function voucherLoadingHtml() {
      var skeleton = '<div class="ww-vskeleton">' +
        '<span class="ww-vskeleton__stamp"></span>' +
        '<span class="ww-vskeleton__lines">' +
          '<i style="width:38%"></i><i style="width:82%"></i><i style="width:56%"></i>' +
        '</span>' +
      '</div>';

      return '<div class="ww-voucher-loading">' +
        '<div class="ww-voucher-loading__art" aria-hidden="true">' +
          '<span class="ww-voucher-loading__spark"></span>' +
          '<span class="ww-voucher-loading__spark"></span>' +
          '<span class="ww-voucher-loading__spark"></span>' +
          '<i class="icon icon-ticket-discount"></i>' +
        '</div>' +
        '<strong data-ww-voucher-progress-note>Đang tìm mã tốt nhất cho bạn…</strong>' +
        '<span>Vui lòng đợi tí xíu nhé.</span>' +
        '<div class="ww-voucher-loading__track">' +
          '<div class="ww-voucher-loading__bar"><i data-ww-voucher-progress style="width:10%"></i></div>' +
          '<b data-ww-voucher-progress-text>10%</b>' +
        '</div>' +
      '</div>' + skeleton + skeleton;
    }

    /**
     * Mốc tiến độ: 5s đầu lên 80%, 8s lên 85%, 10s lên 95%, sau đó bò dần tới 99%.
     */
    function voucherProgressAt(seconds) {
      if (seconds <= 5) return 10 + (seconds / 5) * 70;
      if (seconds <= 8) return 80 + ((seconds - 5) / 3) * 5;
      if (seconds <= 10) return 85 + ((seconds - 8) / 2) * 10;
      return Math.min(99, 95 + (seconds - 10) * 1.5);
    }

    function voucherProgressNote(seconds) {
      if (seconds <= 5) return 'Đang tìm mã tốt nhất cho bạn…';
      if (seconds <= 10) return 'Đang kiểm tra điều kiện áp dụng…';
      return 'Sắp xong rồi, chờ chút xíu nhé…';
    }

    function startVoucherProgress() {
      stopVoucherProgress();
      var startedAt = Date.now();

      var tick = function () {
        var bar = document.querySelector('[data-ww-voucher-progress]');
        if (!bar) {
          stopVoucherProgress();
          return;
        }
        var seconds = (Date.now() - startedAt) / 1000;
        var percent = Math.round(voucherProgressAt(seconds));
        var text = document.querySelector('[data-ww-voucher-progress-text]');
        var note = document.querySelector('[data-ww-voucher-progress-note]');
        bar.style.width = percent + '%';
        if (text) text.textContent = percent + '%';
        if (note) note.textContent = voucherProgressNote(seconds);
      };

      tick();
      voucherProgressTimer = setInterval(tick, 120);
    }

    function stopVoucherProgress() {
      if (!voucherProgressTimer) return;
      clearInterval(voucherProgressTimer);
      voucherProgressTimer = null;
    }

    function finishVoucherProgress(done) {
      stopVoucherProgress();
      var bar = document.querySelector('[data-ww-voucher-progress]');
      var text = document.querySelector('[data-ww-voucher-progress-text]');
      if (!bar) {
        done();
        return;
      }
      bar.style.width = '100%';
      if (text) text.textContent = '100%';
      setTimeout(done, 180);
    }

    function loadVoucherList() {
      var list = el('ww-voucher-list');
      if (list && !voucherCache) {
        list.innerHTML = voucherLoadingHtml();
        startVoucherProgress();
      }
      if (voucherCache) renderVoucherList(voucherCache);

      fetchVoucherList()
        .then(function (vouchers) {
          finishVoucherProgress(function () {
            renderVoucherList(vouchers);
          });
        })
        .catch(function (error) {
          stopVoucherProgress();
          var target = el('ww-voucher-list');
          if (target && !voucherCache) {
            target.innerHTML = '<div class="ww-voucher-picker__empty is-error">' +
              escapeHtml(error && error.message ? error.message : 'Không tải được mã giảm giá.') +
              '</div>';
          }
        });
    }

    function refreshVoucherHighlight() {
      fetchVoucherList().catch(function () {});
    }

    function bindVoucherPicker() {
      var picker = el('ww-voucher-picker');
      if (!picker || picker.dataset.wwBound === '1') return;
      picker.dataset.wwBound = '1';
      if (picker.parentNode !== document.body) {
        document.body.appendChild(picker);
      }

      picker.addEventListener('click', function (e) {
        if (e.target.closest('[data-ww-close-vouchers]')) {
          closeVoucherPicker();
          return;
        }

        if (e.target.closest('#ww-voucher-confirm')) {
          var chosen = pendingVoucherCodes.slice();
          closeVoucherPicker();
          if (chosen.length) {
            applyVoucherCodes(chosen, false);
          } else {
            resetVoucher('');
          }
          return;
        }

        if (e.target.closest('#ww-voucher-apply')) {
          var input = el('ww-voucher-code');
          applyVoucher(input ? input.value : '', false);
          return;
        }

        var card = e.target.closest('[data-ww-pick-voucher]');
        if (card) togglePendingVoucher(card.getAttribute('data-ww-pick-voucher'));
      });

      picker.addEventListener('keydown', function (e) {
        if (e.target && e.target.id === 'ww-voucher-code' && e.key === 'Enter') {
          e.preventDefault();
          applyVoucher(e.target.value, false);
          return;
        }
        var card = e.target.closest ? e.target.closest('[data-ww-pick-voucher]') : null;
        if (card && (e.key === 'Enter' || e.key === ' ')) {
          e.preventDefault();
          togglePendingVoucher(card.getAttribute('data-ww-pick-voucher'));
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && picker && !picker.hidden) closeVoucherPicker();
      });
    }

    function togglePendingVoucher(rawCode) {
      var code = String(rawCode || '').trim().toUpperCase();
      if (!code) return;
      pendingVoucherCodes = pendingVoucherCodes.indexOf(code) !== -1
        ? pendingVoucherCodes.filter(function (item) { return item !== code; })
        : mergeVoucherCodes(code, pendingVoucherCodes);

      var list = el('ww-voucher-list');
      if (!list) return;
      Array.prototype.forEach.call(list.querySelectorAll('[data-ww-pick-voucher]'), function (card) {
        var cardCode = String(card.getAttribute('data-ww-pick-voucher') || '').toUpperCase();
        card.classList.toggle('is-picked', pendingVoucherCodes.indexOf(cardCode) !== -1);
      });
    }

    function openVoucherPicker() {
      bindVoucherPicker();
      var picker = el('ww-voucher-picker');
      if (!picker) return;
      pendingVoucherCodes = appliedVoucherCodes.slice();
      picker.hidden = false;
      picker.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('ww-voucher-picker-open');
      loadVoucherList();
    }

    function closeVoucherPicker() {
      stopVoucherProgress();
      var picker = el('ww-voucher-picker');
      if (!picker) return;
      picker.hidden = true;
      picker.setAttribute('aria-hidden', 'true');
      document.documentElement.classList.remove('ww-voucher-picker-open');
    }

    function setVoucherMessage(message, type) {
      [el('ww-voucher-message'), el('ww-voucher-picker-msg')].forEach(function (node) {
        if (!node) return;
        node.hidden = !message;
        node.textContent = message || '';
        node.className = (node.id === 'ww-voucher-message' ? 'ww-sum__voucher-msg' : 'ww-voucher-picker__msg') +
          (type ? ' is-' + type : '');
      });
    }

    function resetVoucher(message) {
      appliedVoucherCodes = [];
      pendingVoucherCodes = [];
      var rows = el('ww-checkout-discount-rows');
      var totalEl = el('ww-checkout-total');
      if (rows) rows.innerHTML = '';
      if (totalEl) totalEl.textContent = money(state.subtotal + state.shipping);
      setAppliedVoucherUi([]);
      setVoucherMessage(message, message ? 'error' : '');
      syncBar();
    }

    function applyVoucher(rawCode, silent) {
      applyVoucherCodes(mergeVoucherCodes(rawCode), silent);
    }

    function applyVoucherCodes(codes, silent) {
      codes = (codes || []).map(function (code) {
        return String(code || '').trim().toUpperCase();
      }).filter(Boolean);
      var applyBtn = el('ww-voucher-apply');
      if (!codes.length) {
        resetVoucher(silent ? '' : 'Vui lòng nhập mã giảm giá.');
        return;
      }

      if (!form.phone.value.trim() && !form.email.value.trim()) {
        setVoucherMessage('Vui lòng nhập số điện thoại ở mục Thông tin nhận hàng rồi áp mã.', 'error');
        if (!silent) {
          form.phone.focus();
          form.phone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
      }

      if (applyBtn) {
        applyBtn.disabled = true;
        applyBtn.textContent = 'Đang kiểm tra';
      }

      fetch(voucherQuoteUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken()
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          DISCOUNT_CODES: codes,
          EMAIL: form.email.value,
          SO_DIEN_THOAI: form.phone.value,
          ITEMS: checkoutItems()
        })
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { ok: response.ok, data: data };
          });
        })
        .then(function (res) {
          if (!res.ok || !res.data || res.data.STATUS === false) {
            throw new Error((res.data && res.data.STATUS_DETAIL) || 'Mã giảm giá không hợp lệ.');
          }
          var quote = res.data.DATAS && res.data.DATAS.VOUCHER;
          if (!quote) throw new Error('Không đọc được thông tin mã giảm giá.');

          appliedVoucherCodes = Array.isArray(quote.codes) && quote.codes.length
            ? quote.codes.map(function (code) { return String(code).toUpperCase(); })
            : codes;
          pendingVoucherCodes = appliedVoucherCodes.slice();

          var input = el('ww-voucher-code');
          if (input) input.value = '';
          renderDiscountRows(quote);

          var totalEl = el('ww-checkout-total');
          if (totalEl) totalEl.textContent = money(quote.total);
          setAppliedVoucherUi(appliedVoucherCodes);
          setVoucherMessage('Đã áp dụng ' + appliedVoucherCodes.join(' + ') +
            ', giảm ' + money(quote.discount_amount) + '.', 'success');
          syncBar();
        })
        .catch(function (error) {
          setVoucherMessage(error && error.message ? error.message : 'Không thể áp dụng mã giảm giá.', 'error');
        })
        .finally(function () {
          var btn = el('ww-voucher-apply');
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'ÁP DỤNG';
          }
        });
    }

    function renderDiscountRows(quote) {
      var host = el('ww-checkout-discount-rows');
      if (!host) return;

      var parts = Array.isArray(quote && quote.vouchers) && quote.vouchers.length
        ? quote.vouchers
        : (quote ? [{ code: quote.code, discount_amount: quote.discount_amount }] : []);
      var rows = parts.filter(function (part) {
        return Number(part.discount_amount || 0) > 0;
      });

      host.innerHTML = rows.map(function (part) {
        var code = String(part.code || '').toUpperCase();
        var benefit = voucherBenefit(code);

        return '<div class="ww-sum__row ww-sum__row--saving">' +
          '<span class="ww-sum__discount-label">' +
            '<i class="icon icon-ticket-discount" aria-hidden="true"></i>' +
            '<span><strong>' + escapeHtml(code) + '</strong>' +
              (benefit ? '<em>' + escapeHtml(benefit) + '</em>' : '') +
            '</span>' +
          '</span>' +
          '<span>-' + money(part.discount_amount) + '</span>' +
        '</div>';
      }).join('');
    }

    function voucherBenefit(code) {
      var found = (voucherCache || []).find(function (voucher) {
        return String(voucher.code || '').toUpperCase() === code;
      });
      return found ? String(found.benefit || '') : '';
    }

    /* ------------------------------------------------------------------ *
     * Form thông tin nhận hàng
     * ------------------------------------------------------------------ */

    if (accessToken) {
      fetch(profileUrl, {
        headers: {
          Authorization: 'Bearer ' + accessToken,
          Accept: 'application/json'
        }
      })
        .then(function (response) {
          if (!response.ok) throw new Error('profile');
          return response.json();
        })
        .then(function (payload) {
          var profile = (payload && payload.DATAS) || {};
          if (!form.name.value) form.name.value = profile.FULL_NAME || '';
          if (!form.phone.value) form.phone.value = profile.PHONE || '';
          if (!form.email.value) form.email.value = profile.EMAIL || '';
          if (!form.address.value) form.address.value = profile.ADDRESS || '';
        })
        .catch(function () {});
    }

    function clearFieldErrors() {
      form.querySelectorAll('.ww-co-error').forEach(function (node) {
        node.textContent = '';
      });
      form.querySelectorAll('.is-invalid').forEach(function (node) {
        node.classList.remove('is-invalid');
      });
      if (errEl) {
        errEl.hidden = true;
        errEl.textContent = '';
      }
    }

    function markField(messageId, fieldName, message) {
      var msgEl = document.getElementById('MSG_' + messageId);
      if (msgEl) msgEl.textContent = message;
      var input = fieldName ? form.querySelector('[name="' + fieldName + '"]') : null;
      if (input) input.classList.add('is-invalid');
      return input;
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
        var message = firstMessage(errors[key]);
        if (!message) return;
        hasField = true;
        if (rootKey === 'DISCOUNT_CODE') {
          resetVoucher(message);
          if (!firstFocus) firstFocus = document.querySelector('.ww-voucher-cta');
          return;
        }
        var input = markField(rootKey, fieldMap[rootKey], message);
        if (input && !firstFocus) firstFocus = input;
      });
      if (firstFocus) {
        firstFocus.focus();
        firstFocus.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return hasField;
    }

    function validateForm() {
      clearFieldErrors();
      var firstInvalid = null;

      if (!form.name.value.trim()) {
        firstInvalid = firstInvalid || markField('HO_TEN', 'name', 'Vui lòng nhập họ tên người nhận.');
      }

      var digits = form.phone.value.replace(/\D/g, '');
      if (!digits) {
        firstInvalid = firstInvalid || markField('SO_DIEN_THOAI', 'phone', 'Vui lòng nhập số điện thoại.');
      } else if (digits.length < 9 || digits.length > 11) {
        firstInvalid = firstInvalid || markField('SO_DIEN_THOAI', 'phone', 'Số điện thoại chưa đúng, vui lòng kiểm tra lại.');
      }

      if (!form.address.value.trim()) {
        firstInvalid = firstInvalid || markField('DIA_CHI', 'address', 'Vui lòng nhập địa chỉ nhận hàng.');
      }

      var email = form.email.value.trim();
      if (email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        firstInvalid = firstInvalid || markField('EMAIL', 'email', 'Email chưa đúng định dạng.');
      }

      if (firstInvalid) {
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
      }
      return true;
    }

    ['input', 'blur'].forEach(function (eventName) {
      form.addEventListener(eventName, function (e) {
        if (e.target && e.target.classList && e.target.classList.contains('is-invalid') && String(e.target.value).trim()) {
          e.target.classList.remove('is-invalid');
        }
      }, true);
    });

    function showOrderSuccessModal(orderCode) {
      if (!successModal) {
        window.location.href = homeUrl;
        return;
      }
      if (successCodeEl) {
        successCodeEl.hidden = !orderCode;
        successCodeEl.textContent = orderCode ? 'Mã đơn ' + orderCode : '';
      }
      successModal.hidden = false;
      successModal.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('ww-order-success-open');
      if (successHomeBtn) successHomeBtn.focus();
    }

    if (successHomeBtn) {
      successHomeBtn.addEventListener('click', function () {
        window.location.href = homeUrl;
      });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (submitting) return;
      if (!validateForm()) return;

      submitting = true;
      setSubmitDisabled(true, 'ĐANG XỬ LÝ...');

      var payload = {
        name: form.name.value,
        phone: form.phone.value,
        email: form.email.value,
        address: form.address.value,
        note: form.note.value,
        discount_codes: appliedVoucherCodes,
        ITEMS: checkoutItems()
      };

      var headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken()
      };
      if (accessToken) headers.Authorization = 'Bearer ' + accessToken;

      fetch(accessToken ? authenticatedPlaceUrl : placeUrl, {
        method: 'POST',
        headers: headers,
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { ok: response.ok, data: data };
          });
        })
        .then(function (res) {
          if (!res.ok || (res.data && res.data.STATUS === false)) {
            var errors = res.data && res.data.ERRORS;
            if (showFieldErrors(errors)) throw new Error('');
            var message = (res.data && res.data.STATUS_DETAIL) || 'Đặt hàng thất bại.';
            if (errors && typeof errors === 'object') {
              message = Object.keys(errors).map(function (key) {
                return firstMessage(errors[key]);
              }).filter(Boolean).join(' ') || message;
            } else if (typeof errors === 'string') {
              message = errors;
            }
            throw new Error(message);
          }

          var transaction = (res.data && res.data.DATAS && res.data.DATAS.TRANSACTION) || {};
          var orderCode = transaction.SAPO_ORDER_NAME || (transaction.ID ? '#' + transaction.ID : '');

          return fetch(cartClearUrl, {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrfToken()
            },
            credentials: 'same-origin'
          }).then(function () {
            syncCartBadges(0);
            showOrderSuccessModal(orderCode);
          });
        })
        .catch(function (err) {
          submitting = false;
          setSubmitDisabled(false);
          var message = (err && err.message) || '';
          if (!message || !errEl) return;
          errEl.hidden = false;
          errEl.textContent = message;
          errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    bindVoucherPicker();
    syncBar();
    refreshVoucherHighlight();
  })();
  </script>
  <script src="100/531/894/themes/1018832/assets/defer-scripts.js?ww-checkout-2" defer fetchpriority="low"></script>
</body>
</html>
