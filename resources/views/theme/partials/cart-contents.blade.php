@php
  $cartSection = $cartSection ?? 'all';
  $items = $items ?? [];
  $totalQuantity = (int) ($totalQuantity ?? 0);
  $totalPrice = (int) ($totalPrice ?? 0);
  $appUrl = $appUrl ?? rtrim(url('/'), '/');
  $formatPrice = static function (int $amount): string {
      return number_format($amount, 0, ',', '.').' ₫';
  };
  $productUrl = static function (array $line) use ($appUrl): string {
      $handle = trim((string) ($line['handle'] ?? ''));
      $productId = (int) ($line['product_id'] ?? 0);
      if ($handle === '') {
          return $productId > 0 ? url('san-pham/chi-tiet/sp-'.$productId) : '#';
      }
      if ($productId > 0 && ! preg_match('/-\d+$/', $handle)) {
          $handle .= '-'.$productId;
      }

      return url('san-pham/chi-tiet/'.ltrim($handle, '/'));
  };
  $resolveImageUrl = static function (array $line) use ($appUrl): string {
      $imgRel = $line['image'] ?? '';
      if ($imgRel === '') {
          return asset('image/UI-BACKEND/default-image.png');
      }
      if (preg_match('#^https?://#i', $imgRel) || str_starts_with($imgRel, '//')) {
          return $imgRel;
      }
      $path = ltrim($imgRel, '/');
      if (str_starts_with($path, 'upload/') || str_starts_with($path, 'storage/')) {
          return $appUrl.'/'.$path;
      }

      return asset('UI-FRONTEND/'.$path);
  };
  $loadingIcon = <<<'HTML'
<span class="loading-icon gap-1 hidden items-center justify-center">
  <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
  <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
  <span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>
</span>
HTML;
@endphp

@if($cartSection === 'all' || $cartSection === 'table')
<div class="cart-table">
  @if(session('cart_stock_notices'))
    <div class="cart-stock-notices rounded border border-warning bg-warning/10 p-3 mb-3 text-sm">
      @foreach((array) session('cart_stock_notices') as $notice)
        <div>{{ $notice }}</div>
      @endforeach
    </div>
  @endif
  <div class="cart-header dnone lg:grid md:grid-cols-[var(--cart-template)] gap-[var(--table-gap)] border-t border-b border-neutral-50 font-semibold text-center">
    <div class="py-3">Sản phẩm</div>
    <div class="py-3">Đơn giá</div>
    <div class="py-3">Số lượng</div>
    <div class="py-3">Tạm tính</div>
  </div>
  <div class="cart-items divide-y divide-neutral-50">
    @forelse($items as $idx => $line)
    @php
      $lineIndex = $idx + 1;
      $imgUrl = $resolveImageUrl($line);
      $variantTitle = trim((string) ($line['variant_title'] ?? ''));
      $showVariant = ! in_array(mb_strtolower($variantTitle), ['', 'mặc định', 'default title'], true);
      $itemUrl = $productUrl($line);
    @endphp
    <div
      class="cart-item relative"
      data-variant-id="{{ $line['variant_id'] }}"
      data-line-index="{{ $lineIndex }}"
    >
      <remove-cart-button data-line-index="{{ $lineIndex }}" class="cart-remove-col">
        <button type="button" class="w-[2.8rem] h-[2.8rem] rounded-full flex items-center justify-center text-neutral-200 hover:text-error hover:bg-neutral-50 transition-colors" aria-label="Xóa sản phẩm">
          <i class="icon icon-trash text-[1.4rem]"></i>
        </button>
      </remove-cart-button>

      <div class="cart-product-col flex items-start gap-3">
        <a href="{{ $itemUrl }}" class="shrink-0" title="{{ $line['title'] }}">
          <img
            class="ww-cart-item-img object-contain rounded border border-neutral-50 bg-neutral-50/40"
            src="{{ $imgUrl }}"
            width="64"
            height="64"
            alt="{{ $line['title'] }}"
            loading="lazy"
          >
        </a>
        <div class="min-w-0 flex-1 pr-8">
          <a class="link font-semibold line-clamp-2 text-[1.4rem] leading-snug" href="{{ $itemUrl }}" title="{{ $line['title'] }}">{{ $line['title'] }}</a>
          @if($showVariant)
          <p class="text-sm text-neutral-400 mt-1">{{ $variantTitle }}</p>
          @endif
          <div class="cart-unit-price-col mt-1">
            <span class="price text-sm text-neutral-300">{{ $formatPrice((int) ($line['price'] ?? 0)) }}</span>
          </div>
        </div>
      </div>

      <div class="cart-quantity-col flex items-center">
        <quantity-input>
          <div class="custom-number-input">
            <div class="flex flex-row border border-neutral-50 relative bg-background rounded-pill overflow-hidden h-[3.2rem] w-[9.6rem]">
              <button type="button" name="minus" class="h-full w-20 cursor-pointer outline-none p-2">
                <i class="m-auto icon icon-minus"></i>
              </button>
              <input
                type="number"
                class="focus:outline-none form-quantity w-full focus:ring-transparent text-base font-semibold cursor-default flex items-center outline-none bg-transparent border-none text-center"
                name="Lines"
                data-line-index="{{ $lineIndex }}"
                value="{{ (int) ($line['quantity'] ?? 1) }}"
                min="1"
                max="{{ max(1, (int) ($line['stock'] ?? 1)) }}"
                data-stock="{{ max(0, (int) ($line['stock'] ?? 0)) }}"
                aria-label="Số lượng"
              >
              <button type="button" name="plus" class="h-full w-20 rounded-r cursor-pointer p-2">
                <i class="m-auto icon icon-plus"></i>
              </button>
            </div>
          </div>
        </quantity-input>
      </div>

      <div class="cart-total-col">
        <span class="price font-semibold text-primary">{{ $formatPrice((int) ($line['line_price'] ?? 0)) }}</span>
        {!! $loadingIcon !!}
      </div>
    </div>
    @empty
    @endforelse
  </div>
</div>
@endif

@if($cartSection === 'all' || $cartSection === 'summary')
<div class="cart-summary">
  <div class="cart-summary-info ww-cart-drawer-summary p-4 border border-neutral-50 rounded-lg bg-neutral-50/30">
    <div class="cart-bottom">
      <div class="cart-total py-2 flex items-center justify-between w-full">
        <p class="font-semibold text-[1.5rem]">Tổng cộng</p>
        <div class="text-right">
          <div class="price text-[1.8rem] font-bold text-primary">{{ $formatPrice($totalPrice) }}</div>
          {!! $loadingIcon !!}
        </div>
      </div>

      <div class="cart-submit mt-3 space-y-2">
        <a href="{{ url('/thanh-toan') }}" class="btn w-full btn--large font-semibold bg-primary text-white inline-flex justify-center items-center gap-2">
          THANH TOÁN
          <i class="icon icon-arrow-login"></i>
        </a>
        <a href="{{ url('/cart') }}" class="btn w-full btn--large font-semibold border border-neutral-50 bg-background text-foreground inline-flex justify-center items-center gap-2">
          Xem giỏ hàng
        </a>
      </div>
    </div>
  </div>
</div>
@endif

@if($cartSection === 'all' || $cartSection === 'empty')
<div class="cart-empty">
  <div class="bg-background px-4 py-8 rounded-sm relative text-center">
    <div class="flex-col mx-auto gap-4 flex items-center justify-center max-w-[28rem]">
      <img
        class="object-contain"
        src="{{ asset('UI-FRONTEND/100/531/894/themes/1018832/assets/cart_empty_background.png') }}"
        alt="Giỏ hàng trống"
        width="200"
        height="200"
        loading="lazy"
      >
      <h2 class="text-h6 font-semibold">Giỏ hàng chưa có gì!</h2>
      <p class="text-neutral-300 text-sm">Hãy tìm sản phẩm ứng ý và thêm vào giỏ hàng bạn nhé</p>
      <button type="button" class="btn font-semibold bg-primary text-white mt-2 ww-cart-continue-shopping" title="Tiếp tục mua sắm">
        Tiếp tục mua sắm
      </button>
    </div>
  </div>
</div>
@endif
