<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>cart-data</title>
</head>
<body>
@if($totalQuantity === 0)
<div class="is-empty"></div>
@endif

<a class="mini-cart header-icon-group flex gap-2 items-center cart-group hover:bg-neutral-50 active:scale-95 transition-all duration-150 md:px-2 px-1 py-1 rounded-sm" href="{{ url('/cart') }}" title="Giỏ hàng">
  <div class="header-icon w-[3.6rem] h-[3.6rem] p-2 rounded-full flex items-center justify-center relative border border-neutral-50">
    <i class="icon icon-cart"></i>
    <span class="cart-count flex items-center count_item count_item_pr justify-center rounded-full absolute font-semibold">{{ $totalQuantity }}</span>
  </div>
</a>

<span class="cart-count">{{ $totalQuantity }}</span>

@include('theme.partials.cart-contents', [
  'cartSection' => 'all',
  'items' => $items,
  'totalQuantity' => $totalQuantity,
  'totalPrice' => $totalPrice,
  'appUrl' => $appUrl ?? rtrim(url('/'), '/'),
])
</body>
</html>
