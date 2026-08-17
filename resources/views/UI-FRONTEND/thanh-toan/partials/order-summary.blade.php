@php
  $appUrl = $appUrl ?? rtrim(url('/'), '/');
  $formatPrice = static function (int $amount): string {
      return number_format(max(0, $amount), 0, ',', '.') . ' ₫';
  };
  $productUrl = static function (array $line): string {
      $handle = trim((string) ($line['handle'] ?? ''));
      $productId = (int) ($line['product_id'] ?? 0);
      if ($handle === '') {
          return $productId > 0 ? url('san-pham/chi-tiet/sp-' . $productId) : '#';
      }
      if ($productId > 0 && ! preg_match('/-\d+$/', $handle)) {
          $handle .= '-' . $productId;
      }
      return url('san-pham/chi-tiet/' . ltrim($handle, '/'));
  };
  $resolveImageUrl = static function (array $line) use ($appUrl): string {
      $imgRel = (string) ($line['image'] ?? '');
      if ($imgRel === '') {
          return asset('image/UI-BACKEND/default-image.png');
      }
      if (preg_match('#^https?://#i', $imgRel) || str_starts_with($imgRel, '//')) {
          return $imgRel;
      }
      $path = ltrim($imgRel, '/');
      if (str_starts_with($path, 'upload/') || str_starts_with($path, 'storage/')) {
          return $appUrl . '/' . $path;
      }
      return asset('UI-FRONTEND/' . $path);
  };

  $subtotal = (int) $totalPrice;
  $shippingFee = max(0, (int) config('storefront.shipping_fee', 30000));
  $grandTotal = $subtotal + $shippingFee;
  $savings = collect($items ?? [])->sum(static function (array $line): int {
      $compare = (int) ($line['compare_price'] ?? 0);
      $price = (int) ($line['price'] ?? 0);
      $quantity = (int) ($line['quantity'] ?? 0);
      return $compare > $price ? ($compare - $price) * $quantity : 0;
  });
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
  $checkoutState = [
      'subtotal' => $subtotal,
      'shipping' => $shippingFee,
      'total' => $grandTotal,
      'quantity' => (int) $totalQuantity,
  ];
@endphp

<div class="ww-sum" data-quantity="{{ $totalQuantity }}">
  <div class="ww-sum__head">
    <div class="ww-sum__head-text">
      <h2 class="ww-sum__heading">Đơn hàng của bạn</h2>
      <span class="ww-sum__count">{{ $totalQuantity }} sản phẩm</span>
    </div>
    <button type="button" class="ww-sum__toggle" data-ww-summary-toggle aria-expanded="false">
      <span data-ww-summary-toggle-text>Xem chi tiết</span>
      <i class="icon icon-carret-down" aria-hidden="true"></i>
    </button>
  </div>

  <div class="ww-sum__items-wrap" data-ww-summary-items>
    <ul class="ww-sum__items">
      @foreach($items as $idx => $line)
        @php
          $lineIndex = $idx + 1;
          $title = (string) ($line['title'] ?? 'Sản phẩm');
          $variantTitle = trim((string) ($line['variant_title'] ?? ''));
          $showVariant = ! in_array(mb_strtolower($variantTitle), ['', 'mặc định', 'default title'], true);
          $quantity = max(1, (int) ($line['quantity'] ?? 1));
          $stock = max(0, (int) ($line['stock'] ?? 0));
          $itemUrl = $productUrl($line);
        @endphp
        <li class="ww-sum__item" data-line-index="{{ $lineIndex }}">
          <a class="ww-sum__thumb" href="{{ $itemUrl }}" title="{{ $title }}">
            <img src="{{ $resolveImageUrl($line) }}" alt="{{ $title }}" width="64" height="64" loading="lazy">
          </a>
          <div class="ww-sum__info">
            <a class="ww-sum__title" href="{{ $itemUrl }}" title="{{ $title }}">{{ $title }}</a>
            @if($showVariant)
              <span class="ww-sum__variant">{{ $variantTitle }}</span>
            @endif
            <span class="ww-sum__unit">{{ $formatPrice((int) ($line['price'] ?? 0)) }}</span>
            <div class="ww-sum__controls">
              <quantity-input>
                <div class="custom-number-input ww-sum__qty">
                  <button type="button" name="minus" aria-label="Giảm số lượng">
                    <i class="icon icon-minus" aria-hidden="true"></i>
                  </button>
                  <input
                    type="number"
                    class="form-quantity"
                    name="Lines"
                    data-line-index="{{ $lineIndex }}"
                    value="{{ $quantity }}"
                    min="1"
                    max="{{ max(1, $stock) }}"
                    data-stock="{{ $stock }}"
                    aria-label="Số lượng {{ $title }}"
                  >
                  <button type="button" name="plus" aria-label="Tăng số lượng">
                    <i class="icon icon-plus" aria-hidden="true"></i>
                  </button>
                </div>
              </quantity-input>
              <button type="button" class="ww-sum__remove" data-ww-remove-line="{{ $lineIndex }}" title="Xóa sản phẩm">
                <i class="icon icon-trash" aria-hidden="true"></i>
                <span>Xóa</span>
              </button>
            </div>
          </div>
          <div class="ww-sum__line-price">{{ $formatPrice((int) ($line['line_price'] ?? 0)) }}</div>
        </li>
      @endforeach
    </ul>
  </div>

  <div class="ww-sum__voucher">
    <button type="button" class="ww-voucher-cta" data-ww-open-vouchers>
      <span class="ww-voucher-cta__icon" aria-hidden="true">
        <i class="icon icon-ticket-discount"></i>
        <span class="ww-voucher-cta__badge" data-ww-voucher-badge hidden></span>
      </span>
      <span class="ww-voucher-cta__text">
        <strong>Voucher</strong>
        <span data-ww-voucher-count>Chọn hoặc nhập mã giảm giá</span>
      </span>
      <span class="ww-voucher-cta__action">
        <span data-ww-voucher-action-text>Chọn mã</span>
        <i class="icon icon-carret-right" aria-hidden="true"></i>
      </span>
    </button>

    <div class="ww-voucher-chips" id="ww-voucher-chips" hidden></div>
    <p class="ww-sum__voucher-msg" id="ww-voucher-message" hidden></p>
  </div>

  <div class="ww-sum__rows">
    <div class="ww-sum__row">
      <span>Tạm tính</span>
      <span id="ww-checkout-subtotal">{{ $formatPrice($subtotal) }}</span>
    </div>
    @if($savings > 0)
      <div class="ww-sum__row ww-sum__row--saving">
        <span>Tiết kiệm</span>
        <span>-{{ $formatPrice((int) $savings) }}</span>
      </div>
    @endif
    <div class="ww-sum__row">
      <span>Phí vận chuyển</span>
      <span id="ww-checkout-shipping">{{ $formatPrice($shippingFee) }}</span>
    </div>
    <div class="ww-sum__discounts" id="ww-checkout-discount-rows"></div>
    <div class="ww-sum__row ww-sum__row--total">
      <span>Tổng cộng</span>
      <span id="ww-checkout-total">{{ $formatPrice($grandTotal) }}</span>
    </div>
    <p class="ww-sum__vat">Đã bao gồm VAT (nếu có). Thanh toán khi nhận hàng.</p>
  </div>

  <button type="submit" form="ww-checkout-form" class="ww-sum__submit" id="ww-checkout-submit">
    ĐẶT HÀNG
  </button>

  <ul class="ww-sum__trust">
    <li><i class="icon icon-truck-fast" aria-hidden="true"></i> Giao nhanh toàn quốc, kiểm tra hàng trước khi thanh toán</li>
    <li><i class="icon icon-tick" aria-hidden="true"></i> Hàng chính hãng 100%, vật liệu an toàn cho bé</li>
    <li><i class="icon icon-refresh" aria-hidden="true"></i> Đổi trả trong 7 ngày nếu lỗi nhà sản xuất</li>
  </ul>
</div>

<script type="application/json" id="ww-checkout-items">@json($checkoutItemsPayload)</script>
<script type="application/json" id="ww-checkout-state">@json($checkoutState)</script>
