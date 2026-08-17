{{-- Sản phẩm đã xem — 1 hàng card (2/3/5) + prev/next như carousel --}}
<section id="recent-view-coll" class="section section-products section-related-products ww-product-row-carousel hidden" style="--section-margin: 64px 0 64px 0;--section-margin-mb: 32px 0 32px 0">
  <div class="container">
    <div class="section-card">
      <div class="heading-bar py-3 px-5 text-center mb-2">
        <h2 class="text-h4 heading font-semibold">Sản phẩm đã xem</h2>
      </div>
      <div id="ww-recent-viewed-wrap" class="releated-products w-full">
        <carousel-slider>
          <script data-type="carousel-options" type="application/json">{"loop":false,"dragFree":false,"align":"start","containScroll":"trimSnaps"}</script>
          <div class="embla">
            <div class="embla__viewport">
              <div
                class="embla__container product-list flex h-inherit"
                id="ww-recent-viewed-list"
              ></div>
            </div>
            <div class="embla__buttons">
              <button class="embla__button embla__button--prev" type="button" onclick="event.stopPropagation()">
                <i class="icon icon-carret-left"></i>
              </button>
              <button class="embla__button embla__button--next" type="button" onclick="event.stopPropagation()">
                <i class="icon icon-carret-right"></i>
              </button>
            </div>
          </div>
        </carousel-slider>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  var currentProductId = String({{ (int) $productId }});
  var cfg = {
    apiList: @json(url('/api/public/product/list')),
    appUrl: @json(rtrim(url('/'), '/')),
    defaultImg: @json(asset('image/UI-BACKEND/default-image.png')),
    detailPath: '/san-pham/chi-tiet',
    limit: 10,
  };

  function joinAppUrl(pathRel, updDt) {
    if (!pathRel) return '';
    if (window.wwStorefrontImage && window.wwStorefrontImage.resolveMediaUrl) {
      return window.wwStorefrontImage.resolveMediaUrl(pathRel, updDt, cfg.appUrl);
    }
    if (/^https?:\/\//i.test(String(pathRel))) return String(pathRel);
    var url = cfg.appUrl + '/' + String(pathRel).replace(/^\/+/, '');
    return (window.wwStorefrontImage && window.wwStorefrontImage.appendUpdTime)
      ? window.wwStorefrontImage.appendUpdTime(url, updDt)
      : url;
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function formatIntViDots(num) {
    var s = String(Math.floor(Math.abs(Number(num))));
    if (s.length <= 3) return s;
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function formatPriceShortVnd(amount) {
    var n = Math.round(Number(amount));
    if (!isFinite(n) || n <= 0) return '0';
    if (n < 1000) return String(n);
    return formatIntViDots(n) + ' ₫';
  }

  function relativeImagePathFromImg(img) {
    if (!img) return '';
    if (img.PATH && /^https?:\/\//i.test(String(img.PATH))) return String(img.PATH);
    var fname = img.IMAGE_THUMNAIL || img.NAME || '';
    var ar = (img.ASPECT_RATIO && String(img.ASPECT_RATIO).trim()) || '1x1';
    if (img.DIRECTORY && fname) {
      return String(img.DIRECTORY).replace(/^\/+|\/+$/g, '') + '/' + ar + '_' + fname;
    }
    if (img.PATH) return String(img.PATH).replace(/\\/g, '/');
    return '';
  }

  function avatarUrl(p) {
    var list = p.DANH_SACH_HINH_ANH_DAI_DIEN;
    if (!list || !list.length) return cfg.defaultImg;
    var rel = relativeImagePathFromImg(list[0]);
    if (!rel) return cfg.defaultImg;
    var upd = (window.wwStorefrontImage && window.wwStorefrontImage.pickUpdTime)
      ? window.wwStorefrontImage.pickUpdTime(p, list[0])
      : (p.UPD_DT || '');
    return joinAppUrl(rel, upd);
  }

  function hoverUrl(p) {
    var list = p.DANH_SACH_HINH_ANH;
    if (!list || !list.length) return '';
    var rel = relativeImagePathFromImg(list[0]);
    if (!rel) return '';
    var upd = (window.wwStorefrontImage && window.wwStorefrontImage.pickUpdTime)
      ? window.wwStorefrontImage.pickUpdTime(p, list[0])
      : (p.UPD_DT || '');
    return joinAppUrl(rel, upd);
  }

  function relativeImagePath(p) {
    var list = p.DANH_SACH_HINH_ANH_DAI_DIEN;
    if (!list || !list.length) return '';
    return relativeImagePathFromImg(list[0]);
  }

  function detailUrl(p) {
    var slug = (p.TEN_SAN_PHAM_SLUG && String(p.TEN_SAN_PHAM_SLUG).trim()) || 'sp';
    return cfg.appUrl + cfg.detailPath + '/' + slug + '-' + p.ID;
  }

  function productRequiresVariantSelect(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    return Array.isArray(list) && list.length > 1;
  }

  function variantInStock(v) {
    return !!v && Number(v.SO_LUONG_TON || 0) > 0 &&
      (v.CON_HANG === true || v.CON_HANG === 1 || v.CON_HANG === '1');
  }

  function productInStock(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    if (!Array.isArray(list) || !list.length) return false;
    return list.some(variantInStock);
  }

  function productCartVariantId(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    if (Array.isArray(list) && list.length) {
      for (var i = 0; i < list.length; i++) {
        if (variantInStock(list[i]) && list[i].ID) return list[i].ID;
      }
      return list[0] && list[0].ID ? list[0].ID : 0;
    }
    if (p && p.ATTR1) return p.ATTR1;
    return p && p.ID;
  }

  function productListPriceInfo(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    var minPrice = 0;
    var minCompare = 0;
    if (Array.isArray(list)) {
      for (var i = 0; i < list.length; i++) {
        var v = list[i];
        if (!v) continue;
        if (!variantInStock(v)) continue;
        var vp = Math.round(Number(v.GIA_BAN) || 0);
        if (vp <= 0) continue;
        if (minPrice <= 0 || vp < minPrice) {
          minPrice = vp;
          minCompare = Math.round(Number(v.GIA_GOC) || 0);
        }
      }
    }
    if (minPrice > 0) {
      return { price: minPrice, compare: minCompare };
    }
    return {
      price: Math.round(Number(p && p.GIA_CA) || 0),
      compare: Math.round(Number(p && p.GIA_GOC) || 0),
    };
  }

  function csrfField() {
    var m = document.querySelector('meta[name="csrf-token"]');
    if (!m || !m.content) return '';
    return '<input type="hidden" name="_token" value="' + escapeHtml(m.content) + '">';
  }

  function buildCardHtml(p) {
    var title = p.TEN_SAN_PHAM || 'Sản phẩm';
    var href = detailUrl(p);
    var imgRel = relativeImagePath(p);
    var inStock = productInStock(p);
    var soldOutOverlay = inStock
      ? ''
      : '<div class="ww-card-soldout" aria-hidden="true"><span>Hết hàng</span></div>';
    var priceInfo = productListPriceInfo(p);
    var priceInt = priceInfo.price;
    var compareInt = priceInfo.compare;
    var displayText = p.GIA_HIEN_THI != null ? String(p.GIA_HIEN_THI).trim() : '';
    var isContactPrice =
      priceInt <= 0 ||
      p.IS_GIA_CA_LIEN_HE === true ||
      p.IS_GIA_CA_LIEN_HE === 1 ||
      p.IS_GIA_CA_LIEN_HE === '1';
    if (priceInt > 0) isContactPrice = false;
    var priceLabel = isContactPrice
      ? 'Liên hệ'
      : displayText || (priceInt > 0 ? formatPriceShortVnd(priceInt) : 'Liên hệ');
    var showCompare = !isContactPrice && !displayText && priceInt > 0 && compareInt > priceInt;
    var compareLabel = showCompare ? formatPriceShortVnd(compareInt) : '';
    var hov = hoverUrl(p);
    var hasHover = hov !== '';
    var imgMainClass =
      'card-product__image max-h-full w-auto object-contain scale-[var(--image-scale)] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition duration-300 ease-out' +
      (hasHover ? ' group-hover/card:opacity-0' : '');

    var priceBlock =
      isContactPrice || (!displayText && priceInt <= 0)
        ? '<div class="flex flex-col gap-0.5"><span class="price text-h6 font-semibold leading-tight text-neutral-500">Liên hệ</span></div>'
        : '<div class="flex flex-col gap-1 min-w-0">' +
          '<span class="price text-h6 font-semibold leading-tight text-rose-600">' + escapeHtml(priceLabel) + '</span>' +
          (showCompare
            ? '<span class="compare-price price--struck line-through text-sm font-medium text-neutral-400">' +
              escapeHtml(compareLabel) +
              '</span>'
            : '') +
          '</div>';

    var hoverPictures = hasHover
      ? '<picture><source media="(max-width: 600px)" srcset="' +
        escapeHtml(hov) +
        '"><img class="card-product__image-2 max-h-full w-auto object-contain opacity-0 scale-[var(--image-scale)] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[0] group-hover/card:opacity-100 transition duration-300 ease-out" width="480" height="480" loading="lazy" style="--image-scale:1" src="' +
        escapeHtml(hov) +
        '" alt="' +
        escapeHtml(title) +
        '"></picture>'
      : '';

    return (
      '<card-product class="h-full card-product--vertical ww-card-opens-qv' +
      (inStock ? '' : ' is-soldout') +
      '" data-product-id="' +
      escapeHtml(p.ID) +
      '" data-requires-variant="' +
      (productRequiresVariantSelect(p) ? '1' : '0') +
      '">' +
      '<div class="item_product_main card-product relative transition-transform duration-200 ease-in-out h-full">' +
      '<form action="/cart/add" method="post" enctype="multipart/form-data" class="bg-background relative z-10 m-0 h-full">' +
      csrfField() +
      '<input type="hidden" name="product_title" value="' + escapeHtml(title) + '">' +
      '<input type="hidden" name="product_handle" value="' + escapeHtml(p.TEN_SAN_PHAM_SLUG || '') + '">' +
      '<input type="hidden" name="price" value="' + escapeHtml(priceInt) + '">' +
      '<input type="hidden" name="image" value="' + escapeHtml(imgRel) + '">' +
      '<input type="hidden" name="category_id" value="' + escapeHtml((p.DANH_MUC_SAN_PHAM && p.DANH_MUC_SAN_PHAM.ID) || '') + '">' +
      '<div class="card-product__top relative overflow-visible group/card">' +
      '<a class="link aspect-square flex items-center justify-center w-full relative overflow-hidden" href="' + escapeHtml(href) + '" title="' + escapeHtml(title) + '">' +
      '<picture><source media="(max-width: 600px)" srcset="' + escapeHtml(avatarUrl(p)) + '">' +
      '<img class="' + imgMainClass + '" width="480" height="480" loading="lazy" style="--image-scale:1" src="' + escapeHtml(avatarUrl(p)) + '" alt="' + escapeHtml(title) + '"></picture>' +
      hoverPictures +
      soldOutOverlay +
      '</a>' +
      '</div>' +
      '<div class="card-product__body flex flex-col gap-2 px-2 pb-2 md:gap-1 md:px-2 md:pb-2">' +
      '<a class="link block" href="' + escapeHtml(href) + '" title="' + escapeHtml(title) + '">' +
      '<div class="card-product__title text-sm font-normal line-clamp-3">' + escapeHtml(title) + '</div>' +
      '</a>' +
      '<div class="card-product__price-row flex justify-between gap-3 w-full min-w-0">' +
      '<a class="link flex-1 min-w-0" href="' + escapeHtml(href) + '" title="' + escapeHtml(title) + '">' +
      '<div class="price-box flex-1 min-w-0 flex flex-col items-start gap-1">' + priceBlock + '</div>' +
      '</a>' +
      '<div class="card-product__cart-btn shrink-0">' +
      '<input type="hidden" name="variantId" value="' + escapeHtml(productCartVariantId(p)) + '">' +
      '<button type="button" ' + (inStock ? '' : 'disabled aria-disabled="true" ') + 'class="btn bg-relative addtocart-btn font-semibold add_to_cart flex justify-center items-center gap-3' + (inStock ? '' : ' opacity-50 cursor-not-allowed') + '" data-variant-id="' + escapeHtml(productCartVariantId(p)) + '" data-requires-variant="' + (productRequiresVariantSelect(p) ? '1' : '0') + '" data-action="addtocart" aria-label="' + (inStock ? 'Thêm vào giỏ' : 'Hết hàng') + '">' +
      '<span class="loading-icon gap-1 hidden items-center justify-center">' +
      '<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>' +
      '<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>' +
      '<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>' +
      '</span><span class="flex items-center justify-center"><i class="icon icon-cart text-[1.35rem]"></i></span></button></div></div></div></form></div></card-product>'
    );
  }

  function isLaravelProductId(id) {
    var n = parseInt(id, 10);
    return Number.isFinite(n) && n > 0 && n < 1000000;
  }

  function sanitizeRecentStorage() {
    try {
      var key = window.themeConfigs && window.themeConfigs.recentStorage;
      if (!key) return;
      var storage = JSON.parse(localStorage.getItem(key)) || [];
      if (!Array.isArray(storage)) storage = [];
      storage = storage.map(String).filter(isLaravelProductId);
      localStorage.setItem(key, JSON.stringify(storage));
    } catch (e) {}
  }

  function removeRecentId(id) {
    try {
      var key = window.themeConfigs && window.themeConfigs.recentStorage;
      if (!key) return;
      var storage = JSON.parse(localStorage.getItem(key)) || [];
      if (!Array.isArray(storage)) return;
      id = String(id);
      storage = storage.filter(function (x) { return String(x) !== id; });
      localStorage.setItem(key, JSON.stringify(storage));
    } catch (e) {}
  }

  function saveRecentProduct(productId) {
    try {
      var key = window.themeConfigs && window.themeConfigs.recentStorage;
      if (!key) return;
      productId = String(productId);
      var storage = JSON.parse(localStorage.getItem(key)) || [];
      if (!Array.isArray(storage)) storage = [];
      storage = storage.map(String).filter(function (id) { return id !== productId; });
      storage.unshift(productId);
      if (storage.length > cfg.limit) storage = storage.slice(0, cfg.limit);
      localStorage.setItem(key, JSON.stringify(storage));
    } catch (e) {}
  }

  function getRecentIdsExceptCurrent() {
    try {
      var key = window.themeConfigs && window.themeConfigs.recentStorage;
      if (!key) return [];
      var storage = JSON.parse(localStorage.getItem(key)) || [];
      if (!Array.isArray(storage)) return [];
      return storage
        .map(String)
        .filter(function (id) { return id && id !== currentProductId && isLaravelProductId(id); })
        .slice(0, cfg.limit);
    } catch (e) {
      return [];
    }
  }

  function fetchRecentProducts(ids) {
    var params = new URLSearchParams();
    params.set('PAGE', '1');
    params.set('PER_PAGE', String(ids.length));
    params.set('TRANG_THAI_HOAT_DONG', 'true');
    params.set('IS_API_PUBLIC', 'true');
    ids.forEach(function (id) {
      params.append('DANH_SACH_SAN_PHAM_ID[]', id);
    });

    return fetch(cfg.apiList + '?' + params.toString(), {
      method: 'GET',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) {
        if (!data || data.STATUS !== true || !data.DATAS || !data.DATAS.PRODUCT) {
          throw new Error('invalid');
        }
        var rows = data.DATAS.PRODUCT.DATA || [];
        var returned = {};
        rows.forEach(function (p) {
          returned[String(p.ID)] = true;
        });
        ids.forEach(function (id) {
          if (!returned[id]) removeRecentId(id);
        });
        return rows;
      });
  }

  function renderRecentViewed() {
    var section = document.getElementById('recent-view-coll');
    var list = document.getElementById('ww-recent-viewed-list');
    if (!section || !list) return;

    var ids = getRecentIdsExceptCurrent();
    if (!ids.length) {
      section.classList.add('hidden');
      return;
    }

    fetchRecentProducts(ids)
      .then(function (products) {
        if (!products.length) {
          section.classList.add('hidden');
          return;
        }

        list.innerHTML = '';
        products.forEach(function (p) {
          list.insertAdjacentHTML(
            'beforeend',
            '<div class="embla__slide h-inherit">' + buildCardHtml(p) + '</div>'
          );
        });

        section.classList.remove('hidden');

        function initCarousel() {
          var carousel = document.querySelector('#ww-recent-viewed-wrap carousel-slider');
          if (carousel && typeof carousel.init === 'function') {
            carousel.init();
            if (typeof window.wwBindThumbLikeCarouselNav === 'function') {
              window.wwBindThumbLikeCarouselNav(carousel);
            }
            return true;
          }
          return false;
        }
        if (!initCarousel() && window.EGATheme && window.EGATheme.subscribe && window.themeConfigs) {
          window.EGATheme.subscribe(window.themeConfigs.firstInteraction, initCarousel);
        }
        if (window.EGATheme && window.EGATheme.publish && window.themeConfigs) {
          window.EGATheme.publish(window.themeConfigs.productLoaded);
        }
      })
      .catch(function () {
        section.classList.add('hidden');
      });
  }

  sanitizeRecentStorage();
  saveRecentProduct(currentProductId);

  function boot() {
    renderRecentViewed();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
