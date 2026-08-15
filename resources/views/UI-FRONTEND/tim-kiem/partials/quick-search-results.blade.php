@php
  $defaultImg = asset('image/UI-BACKEND/default-image.png');
  $resolveImg = function (array $product) use ($defaultImg): string {
    $imgs = $product['DANH_SACH_HINH_ANH_DAI_DIEN'] ?? [];
    $img = $imgs[0] ?? null;
    if (!$img) {
      return $defaultImg;
    }
    $bust = $product['UPD_DT'] ?? ($img['UPD_DT'] ?? null);
    $fname = $img['IMAGE_THUMNAIL'] ?? $img['NAME'] ?? '';
    $ar = trim((string) ($img['ASPECT_RATIO'] ?? '1x1')) ?: '1x1';
    if ($fname !== '' && !empty($img['DIRECTORY'])) {
      $dir = trim(str_replace('\\', '/', $img['DIRECTORY']), '/');

      return storefrontImageUrl($dir . '/' . $ar . '_' . $fname, $bust);
    }
    if (!empty($img['PATH'])) {
      return storefrontImageUrl(str_replace('\\', '/', $img['PATH']), $bust);
    }

    return $defaultImg;
  };
  $formatPrice = function (array $product): string {
    if (!empty($product['IS_GIA_CA_LIEN_HE'])) {
      return 'Liên hệ';
    }
    if (!empty($product['GIA_HIEN_THI'])) {
      return (string) $product['GIA_HIEN_THI'];
    }
    $price = (int) ($product['GIA_CA'] ?? 0);

    return $price > 0 ? number_format($price, 0, ',', '.') . ' ₫' : 'Liên hệ';
  };
  $detailUrl = function (array $product) use ($appUrl): string {
    $slug = trim((string) ($product['TEN_SAN_PHAM_SLUG'] ?? 'sp'));

    return rtrim($appUrl, '/') . '/san-pham/chi-tiet/' . $slug . '-' . ($product['ID'] ?? '');
  };
@endphp

@if ($total === 0)
  <p class="text-sm text-neutral-400 px-2 py-2">Không tìm thấy sản phẩm cho &ldquo;{{ $query }}&rdquo;</p>
@else
  <p class="text-xs font-semibold text-primary px-2 mb-1">Có {{ number_format($total, 0, ',', '.') }} sản phẩm</p>
  @foreach ($products as $product)
    @php
      $p = is_array($product) ? $product : (array) $product;
      $title = $p['TEN_SAN_PHAM'] ?? 'Sản phẩm';
    @endphp
    <a
      href="{{ $detailUrl($p) }}"
      class="flex gap-3 items-center px-2 py-2 hover:bg-neutral-50 rounded-sm transition-colors"
      title="{{ $title }}"
    >
      <img
        src="{{ $resolveImg($p) }}"
        alt="{{ $title }}"
        width="48"
        height="48"
        loading="lazy"
        class="w-12 h-12 object-contain shrink-0 rounded border border-neutral-50 bg-white"
      >
      <div class="min-w-0 flex-1">
        <span class="text-sm font-semibold line-clamp-2 block">{{ $title }}</span>
        <span class="text-sm text-rose-600 font-medium">{{ $formatPrice($p) }}</span>
      </div>
    </a>
  @endforeach
  @if ($total > count($products))
    <a
      href="{{ storefrontListingUrl(['mode' => !empty($categoryKey) ? 'category' : 'search', 'categoryKey' => $categoryKey ?? '', 'query' => $query]) }}"
      class="link text-primary text-sm font-semibold flex items-center justify-center gap-1 py-2 border-t border-neutral-50 mt-1"
    >
      Xem tất cả ({{ number_format($total, 0, ',', '.') }}) <i class="icon icon-carret-right text-xs"></i>
    </a>
  @endif
@endif
