@php
  $p = $product ?? [];
  $id = (int) ($p['ID'] ?? $productId ?? 0);
  $title = $p['TEN_SAN_PHAM'] ?? 'Sản phẩm';
  $slug = ($p['TEN_SAN_PHAM_SLUG'] ?? null) ? trim((string) $p['TEN_SAN_PHAM_SLUG']) : 'sp';
  $priceInt = (int) round((float) ($p['GIA_CA'] ?? 0));
  $displayText = trim((string) ($p['GIA_HIEN_THI'] ?? ''));
  $isContactPrice = !empty($p['IS_GIA_CA_LIEN_HE']);
  $category = $p['DANH_MUC_SAN_PHAM'] ?? [];
  $categoryId = (int) ($category['ID'] ?? 0);
  $categoryName = $category['TEN_DANH_MUC_SAN_PHAM'] ?? '';
  $detailUrl = url('san-pham/chi-tiet/' . $slug . '-' . $id);

  $imageDisplayRelFromImg = function ($img) {
      if (!$img || !is_array($img)) {
          return '';
      }
      $fname = $img['IMAGE_THUMNAIL'] ?? $img['NAME'] ?? '';
      $ar = trim((string) ($img['ASPECT_RATIO'] ?? '1x1')) ?: '1x1';
      if ($fname !== '' && !empty($img['DIRECTORY'])) {
          $dir = trim(str_replace('\\', '/', (string) $img['DIRECTORY']), '/');

          return $dir . '/' . $ar . '_' . $fname;
      }
      if (!empty($img['PATH'])) {
          return str_replace('\\', '/', (string) $img['PATH']);
      }

      return '';
  };

  $imageOriginalRelFromImg = function ($img) {
      if (!$img || !is_array($img)) {
          return '';
      }
      $fname = $img['IMAGE_THUMNAIL'] ?? $img['NAME'] ?? '';
      if ($fname !== '' && !empty($img['DIRECTORY'])) {
          $dir = trim(str_replace('\\', '/', (string) $img['DIRECTORY']), '/');

          return $dir . '/' . $fname;
      }
      if (!empty($img['PATH'])) {
          $path = str_replace('\\', '/', (string) $img['PATH']);

          return preg_replace('#/(\d+x\d+)_([^/]+)$#i', '/$2', $path) ?: $path;
      }

      return '';
  };

  $imageUrls = [];
  $imageOriginalUrls = [];
  $imageRels = [];
  $productUpd = $p['UPD_DT'] ?? null;
  $pushImages = function ($list) use (&$imageUrls, &$imageOriginalUrls, &$imageRels, $imageDisplayRelFromImg, $imageOriginalRelFromImg, $productUpd) {
      if (!$list || !is_array($list)) {
          return;
      }
      foreach ($list as $img) {
          $displayRel = $imageDisplayRelFromImg($img);
          $originalRel = $imageOriginalRelFromImg($img) ?: $displayRel;
          if ($displayRel === '' && $originalRel === '') {
              continue;
          }
          $bust = $productUpd ?? ($img['UPD_DT'] ?? null);
          $displayUrl = storefrontImageUrl($displayRel ?: $originalRel, $bust);
          $originalUrl = storefrontImageUrl($originalRel ?: $displayRel, $bust);
          if (!in_array($displayUrl, $imageUrls, true)) {
              $imageUrls[] = $displayUrl;
              $imageOriginalUrls[] = $originalUrl;
              $imageRels[] = $displayRel ?: $originalRel;
          }
      }
  };
  $pushImages($p['DANH_SACH_HINH_ANH_DAI_DIEN'] ?? null);
  $pushImages($p['DANH_SACH_HINH_ANH'] ?? null);
  if ($imageUrls === []) {
      $imageUrls[] = asset('image/UI-BACKEND/default-image.png');
      $imageOriginalUrls[] = asset('image/UI-BACKEND/default-image.png');
      $imageRels[] = '';
  }

  $formatPrice = function (int $amount): string {
      if ($amount <= 0) {
          return 'Liên hệ';
      }

      return number_format($amount, 0, ',', '.');
  };

  $compareInt = (int) round((float) ($p['GIA_GOC'] ?? 0));
  $discountPct = 0;
  $showCompare = !$isContactPrice && $displayText === '' && $priceInt > 0 && $compareInt > $priceInt;
  if ($showCompare) {
      $discountPct = min(99, max(1, (int) round((1 - $priceInt / $compareInt) * 100)));
  }
  $priceLabel = $isContactPrice
      ? 'Liên hệ'
      : ($displayText !== ''
          ? $displayText
          : ($priceInt > 0 ? $formatPrice($priceInt) : 'Liên hệ'));
  $showVndSuffix = !$isContactPrice && $displayText === '' && $priceInt > 0;

  $rawVariants = is_array($p['DANH_SACH_BIEN_THE'] ?? null) ? $p['DANH_SACH_BIEN_THE'] : [];
  $variants = [];
  $normalizeImgKey = static function (string $url): string {
      $url = trim($url);
      if ($url === '') {
          return '';
      }
      $path = parse_url($url, PHP_URL_PATH);
      $path = is_string($path) ? $path : $url;

      return strtolower(preg_replace('#/\d+x\d+_([^/]+)$#i', '/$1', $path) ?: $path);
  };
  $imageKeys = array_map($normalizeImgKey, $imageUrls);
  foreach ($rawVariants as $v) {
      if (! is_array($v)) {
          continue;
      }
      $vTitle = trim((string) ($v['TEN_BIEN_THE'] ?? $v['MAU_SAC'] ?? ''));
      if ($vTitle === '') {
          $vTitle = 'Mặc định';
      }
      $vImg = '';
      $vImgs = $v['DANH_SACH_HINH_ANH_DAI_DIEN'] ?? null;
      if (is_array($vImgs) && isset($vImgs[0]) && is_array($vImgs[0])) {
          $vImg = storefrontImageUrl((string) ($vImgs[0]['PATH'] ?? ''), $vImgs[0]['UPD_DT'] ?? $productUpd);
      }
      $imageIndex = -1;
      if ($vImg !== '') {
          $vKey = $normalizeImgKey($vImg);
          foreach ($imageKeys as $idx => $key) {
              if ($key !== '' && ($key === $vKey || str_ends_with($key, $vKey) || str_ends_with($vKey, $key))) {
                  $imageIndex = (int) $idx;
                  break;
              }
          }
      }
      $variants[] = [
          'id' => (int) ($v['ID'] ?? 0),
          'title' => $vTitle,
          'price' => (int) round((float) ($v['GIA_BAN'] ?? 0)),
          'compare' => (int) round((float) ($v['GIA_GOC'] ?? 0)),
          'in_stock' => ! empty($v['CON_HANG']),
          'stock' => max(0, (int) ($v['SO_LUONG_TON'] ?? 0)),
          'image' => $vImg,
          'image_index' => $imageIndex,
      ];
  }
  $hasVariants = count($variants) > 1
      || (count($variants) === 1 && strcasecmp($variants[0]['title'], 'Default Title') !== 0 && strcasecmp($variants[0]['title'], 'Mặc định') !== 0);
  $variantGroupLabel = trim((string) ($p['TEN_NHOM_BIEN_THE'] ?? 'Phân loại')) ?: 'Phân loại';

  // Giá mặc định = giá variant thấp nhất (kiểu Shopee); chưa chọn biến thể
  $minVariantPrice = 0;
  $minVariantCompare = 0;
  $anyVariantInStock = false;
  if ($hasVariants) {
      foreach ($variants as $v) {
          if (! empty($v['in_stock'])) {
              $anyVariantInStock = true;
          }
          $vp = (int) ($v['price'] ?? 0);
          if ($vp <= 0) {
              continue;
          }
          if ($minVariantPrice <= 0 || $vp < $minVariantPrice) {
              $minVariantPrice = $vp;
              $minVariantCompare = (int) ($v['compare'] ?? 0);
          }
      }
      if ($minVariantPrice > 0) {
          $priceInt = $minVariantPrice;
          $compareInt = $minVariantCompare;
          $isContactPrice = false;
          $displayText = '';
          $showCompare = $compareInt > $priceInt;
          $discountPct = 0;
          if ($showCompare) {
              $discountPct = min(99, max(1, (int) round((1 - $priceInt / $compareInt) * 100)));
          }
          $priceLabel = $formatPrice($priceInt);
          $showVndSuffix = true;
      }
  }

  $singleVariant = !$hasVariants && count($variants) === 1 ? $variants[0] : null;
  $singleStock = $singleVariant ? (int) $singleVariant['stock'] : 0;
  $singleInStock = $singleVariant && !empty($singleVariant['in_stock']) && $singleStock > 0;
  $defaultVariantId = $hasVariants ? '' : (int) ($singleVariant['id'] ?? 0);
  $productSku = (string) ($p['MA_SAN_PHAM'] ?? $id);
@endphp
<div class="ww-qv-shell">
  <div class="ww-qv-scroll">
    <product-form class="block h-full" data-product-id="{{ $id }}" id="ww-qv-product-form">
      <div class="product-detail lg:gap-x-[3.2rem] gap-4 gap-x-6 grid grid-cols-1 auto-rows-min lg:grid-cols-2 relative">
        <div class="product-gallery-wrapper bg-background min-h-0 min-w-0 relative lg:rounded-lg" style="height:fit-content">
          <div class="">
            <div class="product-gallery md:px-3 pt-3 md:pt-6">
              <media-gallery>
                <div id="GalleryMain-qv-{{ $id }}" class="embla gallery-main relative mx-auto">
                  <div class="embla__viewport">
                    <div class="embla__container" id="ww-qv-gallery-main">
                      @foreach ($imageUrls as $i => $imgUrl)
                        @php $originalUrl = $imageOriginalUrls[$i] ?? $imgUrl; @endphp
                        <div
                          class="embla__slide w-full grow-0 shrink-0 aspect-square flex items-center justify-center relative swiper-spotlight cursor-zoom-in"
                          data-src="{{ $originalUrl }}"
                          data-original-src="{{ $originalUrl }}"
                          data-display-src="{{ $imgUrl }}"
                          data-index="{{ $i }}"
                        >
                          <img
                            class="object-contain rounded-lg scale-[var(--image-scale)] gallery-main-img"
                            src="{{ $imgUrl }}"
                            alt="{{ $title }}"
                            style="--image-scale:1"
                            width="480"
                            height="480"
                            decoding="async"
                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                          >
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="embla__buttons">
                    <button class="embla__button embla__button--prev" type="button">
                      <i class="icon icon-carret-left"></i>
                    </button>
                    <button class="embla__button embla__button--next" type="button">
                      <i class="icon icon-carret-right"></i>
                    </button>
                  </div>
                </div>

                <div id="GalleryThumbnails-qv-{{ $id }}" class="embla embla-thumbs overflow-hidde text-center tec gallery-thumbnails mt-3 relative">
                  <div class="embla__viewport">
                    <div class="embla__container gap-3" id="ww-qv-gallery-thumbs">
                      @foreach ($imageUrls as $i => $imgUrl)
                        <div class="embla__slide aspect-square cursor-pointer grow-0 shrink-0 w-[6.1rem] md:w-[9rem]{{ $i === 0 ? ' embla-thumbs__slide--selected' : '' }}">
                          <div class="flex items-center justify-center w-full h-full">
                            <img class="object-contain w-auto" src="{{ $imgUrl }}" width="64" height="64" loading="lazy" alt="{{ $title }}">
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  @if (count($imageUrls) > 1)
                    <div class="embla__buttons">
                      <button class="embla__button embla__button--prev" type="button" aria-label="Thumbnail trước">
                        <i class="icon icon-carret-left"></i>
                      </button>
                      <button class="embla__button embla__button--next" type="button" aria-label="Thumbnail sau">
                        <i class="icon icon-carret-right"></i>
                      </button>
                    </div>
                  @endif
                </div>
              </media-gallery>
            </div>
          </div>
        </div>

        <div class="product-form-wrapper lg:row-start-1 lg:col-start-2">
          <div class="bg-background relative">
            <div class="product-title space-y-2 mb-3">
              <h2 class="font-semibold text-h4 leading-snug">{{ $title }}</h2>
            </div>

            <div class="product-price-group mb-3">
              <div class="price-box flex items-center flex-wrap gap-2" id="ww-qv-price-box">
                <div class="flex flex-wrap gap-1 items-baseline">
                  <span class="price text-h4" id="ww-qv-price">
                    @if ($isContactPrice || ($displayText === '' && $priceInt <= 0))
                      Liên hệ
                    @elseif ($displayText !== '')
                      {{ $priceLabel }}
                    @else
                      {{ $priceLabel }}@if ($showVndSuffix)<span class="ww-vnd">&#8363;</span>@endif
                    @endif
                  </span>
                  <span class="compare-price text-h6 line-through{{ $showCompare ? '' : ' hidden' }}" id="ww-qv-compare">
                    @if ($showCompare)
                      {{ $formatPrice($compareInt) }}<span class="ww-vnd">&#8363;</span>
                    @endif
                  </span>
                </div>
                <div class="badge sale-badge px-2 py-1 text-h6 font-semibold{{ ($showCompare && $discountPct > 0) ? '' : ' hidden' }}" id="ww-qv-discount">
                  @if ($showCompare && $discountPct > 0)-{{ $discountPct }}%@endif
                </div>
              </div>
            </div>

            <div class="ww-qv-meta text-sm text-neutral-300 mb-4">
              <div>Mã sản phẩm: <b class="text-foreground" id="ww-qv-sku">{{ $productSku }}</b></div>
              <div>Tình trạng: <b class="{{ ($hasVariants ? $anyVariantInStock : $singleInStock) ? 'text-success' : 'text-danger' }}" id="ww-qv-stock">{{ ($hasVariants ? $anyVariantInStock : $singleInStock) ? 'Còn hàng' : 'Hết hàng' }}</b></div>
              @if ($categoryName !== '')
                <div>Danh mục: <b class="text-foreground">{{ $categoryName }}</b></div>
              @endif
            </div>

            @if ($hasVariants)
              <div
                class="ww-qv-variants mb-4"
                id="ww-qv-variants"
                data-variants='@json($variants)'
                data-require-select="1"
                data-min-price="{{ $minVariantPrice }}"
                data-min-compare="{{ $minVariantCompare }}"
                data-product-sku="{{ $productSku }}"
                data-group-label="{{ $variantGroupLabel }}"
              >
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-neutral-400 shrink-0">{{ $variantGroupLabel }}</span>
                  <span class="font-semibold text-neutral-300" id="ww-qv-variant-label">Vui lòng chọn</span>
                </div>
                <div class="ww-qv-variant-list flex flex-wrap gap-2" role="listbox" aria-label="{{ $variantGroupLabel }}">
                  @foreach ($variants as $index => $variant)
                    @php
                      $variantThumb = trim((string) ($variant['image'] ?? ''));
                      if ($variantThumb === '' && isset($imageUrls[0])) {
                          $variantThumb = $imageUrls[0];
                      }
                    @endphp
                    <button
                      type="button"
                      class="ww-qv-variant-btn{{ empty($variant['in_stock']) ? ' is-soldout' : '' }}{{ $variantThumb !== '' ? ' has-thumb' : '' }}"
                      role="option"
                      aria-selected="false"
                      data-variant-id="{{ $variant['id'] }}"
                      data-title="{{ $variant['title'] }}"
                      data-price="{{ $variant['price'] }}"
                      data-compare="{{ $variant['compare'] }}"
                      data-in-stock="{{ !empty($variant['in_stock']) ? '1' : '0' }}"
                      data-stock="{{ (int) ($variant['stock'] ?? 0) }}"
                      data-image="{{ $variant['image'] }}"
                      data-image-index="{{ (int) ($variant['image_index'] ?? -1) }}"
                      title="{{ $variant['title'] }}"
                    >
                      @if ($variantThumb !== '')
                        <span class="ww-qv-variant-thumb" aria-hidden="true">
                          <img src="{{ $variantThumb }}" alt="" width="28" height="28" loading="lazy" decoding="async">
                        </span>
                      @endif
                      <span class="ww-qv-variant-text">{{ $variant['title'] }}</span>
                    </button>
                  @endforeach
                </div>
                <p class="ww-qv-variant-error" id="ww-qv-variant-error" hidden role="alert">
                  Vui lòng chọn {{ strcasecmp($variantGroupLabel, 'Phân loại') === 0 ? 'Phân loại hàng' : $variantGroupLabel }}
                </p>
              </div>
            @endif

            <div class="product-cta mb-0 mt-4">
              <form class="ww-qv-cart-form" enctype="multipart/form-data" action="/cart/add" method="post">
                @csrf
                <input type="hidden" name="variantId" id="ww-qv-variant-id" value="{{ $defaultVariantId }}">
                <input type="hidden" name="variant_title" id="ww-qv-variant-title" value="{{ $hasVariants ? '' : 'Mặc định' }}">
                <input type="hidden" name="product_title" value="{{ $title }}">
                <input type="hidden" name="product_handle" value="{{ $slug }}">
                <input type="hidden" name="price" id="ww-qv-price-input" value="{{ $priceInt }}">
                <input type="hidden" name="image" id="ww-qv-image-input" value="{{ $imageRels[0] ?? '' }}">
                <input type="hidden" name="category_id" value="{{ $categoryId ?: '' }}">

                <div class="flex items-center flex-wrap gap-3 mb-4">
                  <div class="w-[88px] text-neutral-400">Số lượng</div>
                  <quantity-input>
                    <div class="custom-number-input product-quantity">
                      <div class="flex flex-row h-10 border border-neutral-50 relative bg-background rounded-pill overflow-hidden h-[3.8rem] w-[13rem]">
                        <button type="button" name="minus" class="h-full w-20 cursor-pointer outline-none p-2">
                          <i class="m-auto icon icon-minus"></i>
                        </button>
                        <input type="number" class="focus:outline-none form-quantity w-full focus:ring-transparent text-base font-semibold bg-transparent border-none text-center" name="quantity" value="1" min="1" max="{{ $hasVariants ? 1 : max(1, $singleStock) }}" @unless($hasVariants) data-stock="{{ max(0, (int) $singleStock) }}" @endunless>
                        <button type="button" name="plus" class="h-full w-20 rounded-r cursor-pointer p-2">
                          <i class="m-auto icon icon-plus"></i>
                        </button>
                      </div>
                    </div>
                  </quantity-input>
                </div>

                <div class="flex gap-2 mt-4 border-t border-neutral-50 pt-4">
                  <button
                    type="button"
                    name="addtocart"
                    id="ww-qv-addtocart"
                    class="font-semibold btn bg-[var(--color-addtocart-bg)] text-[var(--color-addtocart)] btn-add-to-cart add_to_cart w-full"
                    data-variant-id="{{ $defaultVariantId }}"
                    data-action="addtocart"
                    data-requires-variant="{{ $hasVariants ? '1' : '0' }}"
                    @if(!$hasVariants && !$singleInStock) disabled aria-disabled="true" @endif
                  >
                    {{ (!$hasVariants && !$singleInStock) ? 'HẾT HÀNG' : 'THÊM VÀO GIỎ' }}
                    <br><span class="text-xs font-normal opacity-90">Giao hàng tận nơi hoặc nhận tại cửa hàng</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </product-form>
  </div>

  <div class="ww-qv-footer">
    <a class="ww-qv-detail-link link font-semibold text-sm" href="{{ $detailUrl }}">Xem chi tiết sản phẩm »</a>
  </div>
</div>
{{-- Variant click handler: quick-view-enhance.js (DOMParser không chạy script inline) --}}
