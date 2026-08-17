<script>
(function () {
  var cfg = {
    apiUrl: @json(url('/api/public/product/list')),
    categoryUrl: @json(url('/api/public/categoryp/list/tree')),
    /** Gốc ứng dụng (có subpath nếu chạy trong thư mục) — dùng cho ảnh để không bị <base href=".../UI-FRONTEND/"> làm sai đường dẫn */
    appUrl: @json(rtrim(url('/'), '/')),
    defaultImg: @json(asset('image/UI-BACKEND/default-image.png')),
    /** Trang chi tiết storefront UI-FRONTEND (không dùng /admin) */
    detailPath: '/san-pham/chi-tiet',
  };

  function joinAppUrl(pathRel, updDt) {
    if (!pathRel) return '';
    if (window.wwStorefrontImage && window.wwStorefrontImage.resolveMediaUrl) {
      return window.wwStorefrontImage.resolveMediaUrl(pathRel, updDt, cfg.appUrl);
    }
    if (/^https?:\/\//i.test(String(pathRel))) return String(pathRel);
    var p = String(pathRel).replace(/^\/+/, '');
    var url = cfg.appUrl + '/' + p;
    return (window.wwStorefrontImage && window.wwStorefrontImage.appendUpdTime)
      ? window.wwStorefrontImage.appendUpdTime(url, updDt)
      : url;
  }

  function storageFileName(img) {
    if (!img) return '';
    return img.IMAGE_THUMNAIL || img.NAME || '';
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  /** Chèn dấu . phân tách hàng nghìn khi phần số có hơn 3 chữ số */
  function formatIntViDots(num) {
    var s = String(Math.floor(Math.abs(Number(num))));
    if (s.length <= 3) return s;
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  /** Hiển thị giá tiền đầy đủ: 120000 → 120.000 ₫ */
  function formatPriceShortVnd(amount) {
    var n = Math.round(Number(amount));
    if (!isFinite(n) || n <= 0) return '0';
    if (n < 1000) return String(n);
    return formatIntViDots(n) + ' ₫';
  }

  function relativeImagePathFromImg(img) {
    if (!img) return '';
    if (img.PATH && /^https?:\/\//i.test(String(img.PATH))) return String(img.PATH);
    var fname = storageFileName(img);
    // Thumbnail card: ưu tiên bản crop theo aspect_ratio (vd 1x1_file.jpg)
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

  function productCartVariantId(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    if (Array.isArray(list) && list.length) {
      for (var i = 0; i < list.length; i++) {
        var v = list[i];
        if (v && v.ID && Number(v.SO_LUONG_TON || 0) > 0 && (v.CON_HANG === true || v.CON_HANG === 1 || v.CON_HANG === '1')) {
          return v.ID;
        }
      }
      return list[0] && list[0].ID ? list[0].ID : 0;
    }
    if (p && p.ATTR1) return p.ATTR1;
    return p && p.ID;
  }

  function productInStock(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    if (!Array.isArray(list) || !list.length) return false;
    return list.some(function (v) {
      return v && Number(v.SO_LUONG_TON || 0) > 0 &&
        (v.CON_HANG === true || v.CON_HANG === 1 || v.CON_HANG === '1');
    });
  }

  function productListPriceInfo(p) {
    var list = p && p.DANH_SACH_BIEN_THE;
    var minPrice = 0;
    var minCompare = 0;
    if (Array.isArray(list)) {
      for (var i = 0; i < list.length; i++) {
        var v = list[i];
        if (!v) continue;
        if (Number(v.SO_LUONG_TON || 0) <= 0 || !(v.CON_HANG === true || v.CON_HANG === 1 || v.CON_HANG === '1')) continue;
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

  function prefetchPath(fullUrl) {
    try {
      return new URL(fullUrl, window.location.origin).pathname || '';
    } catch (e) {
      return '';
    }
  }

  function csrfField() {
    var m = document.querySelector('meta[name="csrf-token"]');
    if (!m || !m.content) return '';
    return '<input type="hidden" name="_token" value="' + escapeHtml(m.content) + '">';
  }

  function buildCardHtml(p, opts) {
    var wrapFlash = opts && opts.wrapFlash;
    var title = p.TEN_SAN_PHAM || 'Sản phẩm';
    var href = detailUrl(p);
    var pref = prefetchPath(href);
    var imgRel = relativeImagePath(p);
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
    var hov = (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches)
      ? hoverUrl(p)
      : '';
    var hasHover = hov !== '';
    var imgMainClass = 'card-product__image max-h-full w-auto object-contain scale-[var(--image-scale)] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2' + (hasHover ? ' transition duration-300 ease-out group-hover/card:opacity-0' : '');

    var priceBlock = '';
    if (isContactPrice || (!displayText && priceInt <= 0)) {
      priceBlock =
        '<div class="flex flex-col gap-0.5">' +
        '<span class="price text-h6 font-semibold leading-tight text-neutral-500">Liên hệ</span>' +
        '</div>';
    } else {
      priceBlock =
        '<div class="flex flex-col gap-1 min-w-0">' +
        '<div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5 min-w-0">' +
        '<span class="price text-h6 font-semibold leading-tight text-rose-600">' + escapeHtml(priceLabel) + '</span>' +
        '</div>';
      if (showCompare) {
        priceBlock +=
          '<div class="price-box__compare-row flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5 min-w-0">' +
          '<span class="compare-price price--struck line-through text-sm font-medium leading-snug text-neutral-400 decoration-neutral-300/80">' +
          escapeHtml(compareLabel) +
          '</span></div>';
      }
      priceBlock += '</div>';
    }

    var hoverPictures = '';
    if (hasHover) {
      hoverPictures =
        '<picture><source media="(max-width: 600px)" srcset="' + escapeHtml(hov) + '">' +
        '<img class="card-product__image-2 max-h-full w-auto object-contain opacity-0 scale-[var(--image-scale)] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[0] group-hover/card:opacity-1 group-hover/card:z-[1] group-hover/card:opacity-100 transition duration-300 ease-out" width="480" height="480" loading="lazy" style="--image-scale:1" src="' +
        escapeHtml(hov) +
        '" alt="' +
        escapeHtml(title) +
        '"></picture>';
    }

    var requiresVariant = productRequiresVariantSelect(p);
    var cartVariantId = productCartVariantId(p);
    var inStock = productInStock(p);
    var soldOutOverlay = inStock
      ? ''
      : '<div class="ww-card-soldout" aria-hidden="true"><span>Hết hàng</span></div>';

    // Không border trên form (ảnh không bị viền khung); border chỉ ở body qua CSS
    var cardInnerStyle = '';

    var inner =
      '<card-product class="h-full card-product--vertical ww-card-opens-qv' +
      (inStock ? '' : ' is-soldout') +
      '" data-product-id="' +
      escapeHtml(p.ID) +
      '" data-requires-variant="' +
      (requiresVariant ? '1' : '0') +
      '" data-prefetch="' +
      escapeHtml(pref) +
      '">' +
      '<div class=" item_product_main card-product relative transition-transform duration-200 ease-in-out  h-full h-full">' +
      '<form action="/cart/add" method="post" data-id="product-actions-' +
      escapeHtml(p.ID) +
      '" enctype="multipart/form-data" class="bg-background relative z-10 m-0   h-full"' +
      cardInnerStyle +
      '>' +
      csrfField() +
      '<input type="hidden" name="product_title" value="' +
      escapeHtml(title) +
      '">' +
      '<input type="hidden" name="product_handle" value="' +
      escapeHtml(p.TEN_SAN_PHAM_SLUG || '') +
      '">' +
      '<input type="hidden" name="price" value="' +
      escapeHtml(priceInt) +
      '">' +
      '<input type="hidden" name="image" value="' +
      escapeHtml(imgRel) +
      '">' +
      '<input type="hidden" name="category_id" value="' +
      escapeHtml((p.DANH_MUC_SAN_PHAM && p.DANH_MUC_SAN_PHAM.ID) || '') +
      '">' +
      '<div class="card-product__top relative overflow-visible group/card">' +
      '<div class="sapo-combo-badge" data-id="' +
      escapeHtml(p.ID) +
      '"></div>' +
      '<a class="link aspect-square flex items-center justify-center w-full relative overflow-hidden" href="' +
      escapeHtml(href) +
      '" title="' +
      escapeHtml(title) +
      '">' +
      '<div class="card-product__badges absolute top-2 left-2 z-10 flex items-center gap-2"></div>' +
      '<picture><source media="(max-width: 600px)" srcset="' +
      escapeHtml(avatarUrl(p)) +
      '">' +
      '<img class="' +
      imgMainClass +
      '" width="480" height="480" decoding="async" loading="lazy" style="--image-scale:1" src="' +
      escapeHtml(avatarUrl(p)) +
      '" alt="' +
      escapeHtml(title) +
      '"></picture>' +
      hoverPictures +
      soldOutOverlay +
      '</a>' +
      '</div>' +
      '<div class="card-product__body flex flex-col gap-2 px-2 pb-2 md:gap-1 md:px-2 md:pb-2">' +
      '<a class="link block" href="' +
      escapeHtml(href) +
      '" title="' +
      escapeHtml(title) +
      '">' +
      '<div class="card-product__title text-sm font-normal line-clamp-3">' +
      escapeHtml(title) +
      '</div>' +
      '<div class="sapo-product-reviews-badge" data-id="' +
      escapeHtml(p.ID) +
      '"></div>' +
      '</a>' +
      '<div class="card-product__price-row flex justify-between gap-3 w-full min-w-0">' +
      '<a class="link flex-1 min-w-0" href="' +
      escapeHtml(href) +
      '" title="' +
      escapeHtml(title) +
      '">' +
      '<div class="price-box flex-1 min-w-0">' +
      priceBlock +
      '</div>' +
      '</a>' +
      '<div class="card-product__price-actions shrink-0 flex flex-col items-center gap-1">' +
      '<div class="card-product__cart-btn">' +
      '<input type="hidden" name="variantId" value="' +
      escapeHtml(cartVariantId) +
      '">' +
      '<button type="button" ' + (inStock ? '' : 'disabled aria-disabled="true" ') + 'class="btn bg-relative addtocart-btn font-semibold add_to_cart flex justify-center items-center gap-3' + (inStock ? '' : ' opacity-50 cursor-not-allowed') + '" data-variant-id="' +
      escapeHtml(cartVariantId) +
      '" data-product="' +
      escapeHtml(pref) +
      '" data-requires-variant="' +
      (requiresVariant ? '1' : '0') +
      '" data-action="addtocart" aria-label="' + (inStock ? 'Thêm vào giỏ' : 'Hết hàng') + '">' +
      '<span class="loading-icon gap-1 hidden items-center justify-center">' +
      '<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>' +
      '<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>' +
      '<span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span>' +
      '</span><span class="flex items-center justify-center"><i class="icon icon-cart text-[1.35rem]"></i></span></button></div>' +
      '</div></div>' +
      '</div></form></div></card-product>';

    if (wrapFlash) {
      return (
        '<div class="relative h-inherit flashsale__item embla__slide w-[65.5%]  md:w-1/3 lg:w-1/5 flex-shrink-0 flex-grow-0 pl-2">' +
        inner +
        '</div>'
      );
    }
    return inner;
  }

  function buildProductGridSkeletonItemHtml() {
    return (
      '<div class="skeleton__product-grid__item" aria-hidden="true">' +
      '<div class="skeleton__product-grid__item__image">' +
      '<span class="ww-skel-badge"></span>' +
      '<span class="ww-skel-img-glow"></span>' +
      '</div>' +
      '<div class="skeleton__product-grid__item__body">' +
      '<div class="ww-skel-title">' +
      '<span class="ww-skel-bone ww-skel-line ww-skel-line--full"></span>' +
      '<span class="ww-skel-bone ww-skel-line ww-skel-line--mid"></span>' +
      '<span class="ww-skel-bone ww-skel-line ww-skel-line--short"></span>' +
      '</div>' +
      '<div class="ww-skel-footer">' +
      '<span class="ww-skel-bone ww-skel-price"></span>' +
      '<span class="ww-skel-bone ww-skel-btn"></span>' +
      '</div></div></div>'
    );
  }

  /** Skeleton khi đang fetch API sản phẩm */
  function buildProductGridSkeletonHtml(count, wrapFlash) {
    var n = Math.max(1, Math.round(Number(count)) || 10);
    var html = '';
    for (var i = 0; i < n; i++) {
      if (wrapFlash) {
        html +=
          '<div class="relative h-inherit flashsale__item embla__slide w-[65.5%] md:w-1/3 lg:w-1/5 flex-shrink-0 flex-grow-0 pl-2">' +
          buildProductGridSkeletonItemHtml() +
          '</div>';
      } else {
        html += buildProductGridSkeletonItemHtml();
      }
    }
    return html;
  }

  function getFlashSectionEl() {
    return document.getElementById('section-flashsale-0');
  }

  function getCategorySectionEl(catId) {
    if (!catId) return null;
    return document.querySelector(
      'section.section-home-category-products[data-home-category-id="' + String(catId) + '"]'
    );
  }

  function hideHomeBlock(el) {
    if (!el) return;
    el.style.display = 'none';
    el.setAttribute('hidden', '');
    el.setAttribute('aria-hidden', 'true');
  }

  function showHomeBlock(el) {
    if (!el) return;
    el.style.display = '';
    el.removeAttribute('hidden');
    el.removeAttribute('aria-hidden');
  }

  var homeProductsSkeletonHidden = false;
  function hideHomeProductsSkeleton() {
    if (homeProductsSkeletonHidden) return;
    homeProductsSkeletonHidden = true;
    var sk = document.getElementById('ww-home-products-skeleton');
    if (!sk) return;
    hideHomeBlock(sk);
    sk.setAttribute('aria-busy', 'false');
  }

  function buildEmptyProductsHtml() {
    return '<p class="col-span-full text-center text-sm text-slate-600 py-8">Không tìm thấy sản phẩm phù hợp.</p>';
  }

  function isHomeBlockVisible(el) {
    return !!(el && !el.hasAttribute('hidden') && el.style.display !== 'none');
  }

  /** Theo dõi load tab theo danh mục — ẩn cả section nếu mọi tab đều trống */
  var categoryLoadState = {};

  function beginCategoryLoads(catId, total) {
    if (!catId) return;
    categoryLoadState[String(catId)] = { pending: total, hasProducts: false };
  }

  function noteCategoryLoadResult(catId, hasProducts) {
    var key = String(catId || '');
    var st = categoryLoadState[key];
    if (!st) return;
    if (hasProducts) st.hasProducts = true;
    st.pending = Math.max(0, st.pending - 1);
    var sectionEl = getCategorySectionEl(key);
    if (st.hasProducts) {
      hideHomeProductsSkeleton();
      // Chỉ hiện khi đã có sản phẩm (không hiện skeleton trống)
      if (!isHomeBlockVisible(sectionEl)) {
        showHomeBlock(sectionEl);
      }
      return;
    }
    if (st.pending <= 0 && !st.hasProducts) {
      hideHomeBlock(sectionEl);
      // Hết mọi tab của section này mà chưa có SP — nếu không còn section nào đang chờ thì ẩn skeleton
      var stillWaiting = false;
      Object.keys(categoryLoadState).forEach(function (k) {
        if (categoryLoadState[k] && categoryLoadState[k].pending > 0) stillWaiting = true;
      });
      if (!stillWaiting) {
        var anyVisible = !!document.querySelector(
          'section.section-home-category-products:not([hidden])'
        );
        if (!anyVisible) hideHomeProductsSkeleton();
      }
    }
  }

  function productSellPrice(p) {
    var info = productListPriceInfo(p);
    return info.price > 0 ? info.price : null;
  }

  function productNameKey(p) {
    return String((p && p.TEN_SAN_PHAM) || '').trim().toLowerCase();
  }

  /** Sort toàn bộ list theo giá bán / tên — dùng cho tab sắp xếp trang chủ */
  function sortProductsByBoLoc(rows, boLoc) {
    var list = Array.isArray(rows) ? rows.slice() : [];
    var mode = boLoc || 'default';
    if (mode === 'default') return list;

    list.sort(function (a, b) {
      if (mode === 'gia-tang' || mode === 'gia-giam') {
        var pa = productSellPrice(a);
        var pb = productSellPrice(b);
        var aEmpty = pa == null;
        var bEmpty = pb == null;
        if (aEmpty !== bEmpty) return aEmpty ? 1 : -1;
        if (pa !== pb) return mode === 'gia-tang' ? pa - pb : pb - pa;
        return Number(a.ID || 0) - Number(b.ID || 0);
      }
      if (mode === 'a-z' || mode === 'z-a') {
        var cmp = productNameKey(a).localeCompare(productNameKey(b), 'vi', { sensitivity: 'base' });
        if (cmp !== 0) return mode === 'a-z' ? cmp : -cmp;
        return Number(a.ID || 0) - Number(b.ID || 0);
      }
      return 0;
    });
    return list;
  }

  function renderProductsInto(el, rows, section) {
    if (!el) return;
    if (!rows || !rows.length) {
      el.innerHTML = buildEmptyProductsHtml();
      return;
    }
    var html = '';
    for (var i = 0; i < rows.length; i++) {
      html += buildCardHtml(rows[i], { wrapFlash: section === 'flash' });
    }
    el.innerHTML = html;
  }

  function fetchAllCategoryProducts(categoryId) {
    var params = new URLSearchParams();
    params.set('PAGE', '1');
    params.set('PER_PAGE', '9999');
    params.set('IS_GET_ALL_ELEMENTS', 'true');
    params.set('BO_LOC', 'default');
    params.set('TRANG_THAI_HOAT_DONG', 'true');
    params.set('IS_API_PUBLIC', 'true');
    if (categoryId) {
      params.append('DANH_MUC_SAN_PHAM_ID[]', String(categoryId));
    }

    return fetch(cfg.apiUrl + '?' + params.toString(), {
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
          throw new Error((data && data.STATUS_DETAIL) || 'Phản hồi không hợp lệ');
        }
        return data.DATAS.PRODUCT.DATA || [];
      });
  }

  function loadProducts(section, containerSelector, opts) {
    var el = document.querySelector(containerSelector);
    if (!el) return;

    var params = new URLSearchParams();
    params.set('PAGE', '1');
    var per = (opts && opts.perPage) || (section === 'flash' ? 12 : 10);
    el.innerHTML = buildProductGridSkeletonHtml(per, section === 'flash');

    params.set('PER_PAGE', String(per));
    params.set('BO_LOC', (opts && opts.boLoc) || 'default');
    params.set('TRANG_THAI_HOAT_DONG', 'true');
    params.set('IS_API_PUBLIC', 'true');
    if (section === 'flash') {
      params.set('PRODUCT_VIP', 'true');
    } else if (opts && opts.productVip) {
      params.set('PRODUCT_VIP', 'true');
    } else if (opts && opts.productHot) {
      params.set('PRODUCT_HOT', 'true');
    }
    if (opts && opts.categoryId) {
      params.append('DANH_MUC_SAN_PHAM_ID[]', String(opts.categoryId));
    }

    var categoryId = opts && opts.trackCategoryId != null ? opts.trackCategoryId : opts && opts.categoryId;

    fetch(cfg.apiUrl + '?' + params.toString(), {
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
          throw new Error((data && data.STATUS_DETAIL) || 'Phản hồi không hợp lệ');
        }
        var rows = data.DATAS.PRODUCT.DATA || [];
        if (!rows.length) {
          if (section === 'flash') {
            el.innerHTML = '';
            hideHomeBlock(getFlashSectionEl());
          } else {
            el.innerHTML = buildEmptyProductsHtml();
            if (categoryId) {
              noteCategoryLoadResult(categoryId, false);
            }
          }
          return;
        }
        renderProductsInto(el, rows, section);
        if (section === 'flash') {
          showHomeBlock(getFlashSectionEl());
        } else if (categoryId) {
          noteCategoryLoadResult(categoryId, true);
        }
        window.dispatchEvent(new CustomEvent('home-product-cards-loaded', { detail: { section: section } }));
      })
      .catch(function () {
        if (section === 'flash') {
          el.innerHTML = '';
          hideHomeBlock(getFlashSectionEl());
        } else {
          el.innerHTML = buildEmptyProductsHtml();
          if (categoryId) {
            noteCategoryLoadResult(categoryId, false);
          }
        }
      });
  }

  function getCategoryChildren(cat) {
    var children = (cat && cat.DANH_SACH_CHILDREN) || [];
    if (!Array.isArray(children)) return [];
    return children
      .filter(function (c) {
        return c && c.ID && c.TRANG_THAI_HOAT_DONG !== false;
      })
      .slice()
      .sort(function (a, b) {
        var sa = a && a.SORT_ORDER != null ? Number(a.SORT_ORDER) : 0;
        var sb = b && b.SORT_ORDER != null ? Number(b.SORT_ORDER) : 0;
        return sa - sb;
      });
  }

  function buildCategoryListUrl(cat, opts) {
    opts = opts || {};
    var isAllProducts = opts.allProducts === true || (cat && cat.IS_ALL_PRODUCTS === true);
    if (isAllProducts) {
      var allParts = ['/tat-ca-san-pham'];
      if (opts.boLoc && opts.boLoc !== 'default') {
        allParts.push('sap-xep/' + encodeURIComponent(String(opts.boLoc)));
      }
      if (opts.productHot) {
        allParts.push('noi-bat');
      }
      return cfg.appUrl + allParts.join('/');
    }

    var cid = opts.categoryId != null ? opts.categoryId : cat && cat.ID;
    if (!cid) return cfg.appUrl + '/tat-ca-san-pham';
    var slug = (opts.slug || (cat && cat.TEN_DANH_MUC_SAN_PHAM_SLUG) || 'danh-muc').toString();
    var parts = ['/danh-muc/' + encodeURIComponent(slug + '-' + cid)];
    if (opts.boLoc && opts.boLoc !== 'default') {
      parts.push('sap-xep/' + encodeURIComponent(String(opts.boLoc)));
    }
    if (opts.productHot) {
      parts.push('noi-bat');
    }
    return cfg.appUrl + parts.join('/');
  }

  function buildFilterTabsHtml(cat, tabPrefix) {
    var children = getCategoryChildren(cat);
    // Tab "Tất cả" luôn đầu tiên; menu con nằm ngay sau; rồi mới tới các tab sắp xếp.
    var defs = [
      { label: 'Tất cả', boLoc: 'default', kind: 'all' },
      { label: 'Giá tăng dần', boLoc: 'gia-tang', kind: 'sort' },
      { label: 'Giá giảm dần', boLoc: 'gia-giam', kind: 'sort' },
      { label: 'Tên từ A-Z', boLoc: 'a-z', kind: 'sort' },
      { label: 'Tên từ Z-A', boLoc: 'z-a', kind: 'sort' },
    ];
    var html =
      '<ul class="heading-tabs heading-tabs--scroll mb-4 md:mb-6 w-full max-w-full overflow-x-auto list-none flex md:gap-3 gap-2 font-semibold whitespace-nowrap">';

    var tabIndex = 1;
    for (var i = 0; i < defs.length; i++) {
      var def = defs[i];
      if (def.kind === 'all') {
        var allHref = buildCategoryListUrl(cat, { boLoc: def.boLoc });
        html +=
          '<li aria-controls="' +
          tabPrefix +
          '-tab' +
          tabIndex +
          '" data-ww-href="' +
          escapeHtml(allHref) +
          '" class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground active inline-flex items-center md:gap-3 gap-2">' +
          escapeHtml(def.label) +
          '</li>';
        tabIndex += 1;

        for (var c = 0; c < children.length; c++) {
          var child = children[c];
          var childHref = buildCategoryListUrl(child, { boLoc: 'default' });
          html +=
            '<li aria-controls="' +
            tabPrefix +
            '-tab' +
            tabIndex +
            '" data-ww-href="' +
            escapeHtml(childHref) +
            '" class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2">' +
            escapeHtml(child.TEN_DANH_MUC_SAN_PHAM || 'Danh mục') +
            '</li>';
          tabIndex += 1;
        }
        continue;
      }

      var href = buildCategoryListUrl(cat, { boLoc: def.boLoc });
      html +=
        '<li aria-controls="' +
        tabPrefix +
        '-tab' +
        tabIndex +
        '" data-ww-href="' +
        escapeHtml(href) +
        '" class="tab-btn cursor-pointer heading-tab px-5 py-2 bg-white rounded-pill text-secondary font-semibold hover:text-foreground inline-flex items-center md:gap-3 gap-2">' +
        escapeHtml(def.label) +
        '</li>';
      tabIndex += 1;
    }
    html += '</ul>';
    return html;
  }

  function buildTabPanelsHtml(tabPrefix, baseGrid, tabCount, skHtml, activeIndex) {
    activeIndex = activeIndex || 1;
    var html = '';
    for (var i = 1; i <= tabCount; i++) {
      html +=
        '<div class="tab-content' +
        (i === activeIndex ? '' : ' hidden') +
        '" id="' +
        tabPrefix +
        '-tab' +
        i +
        '">' +
        '<div class="product-list grid tab-content-inner grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 mt-2" id="' +
        baseGrid +
        '-t' +
        i +
        '">' +
        skHtml +
        '</div></div>';
    }
    return html;
  }

  function buildCategorySectionHtml(cat, skeletonCount) {
    var sk = skeletonCount == null ? 10 : skeletonCount;
    var cid = cat && cat.ID ? String(cat.ID) : '0';
    var title = (cat && cat.TEN_DANH_MUC_SAN_PHAM) || 'Danh mục';
    var children = getCategoryChildren(cat);
    var defaultViewmoreHref = buildCategoryListUrl(cat, { boLoc: 'default' });
    var titleHref = buildCategoryListUrl(cat);
    var baseGrid = 'home-category-products-' + cid;
    var tabPrefix = 'home-cat-' + cid;
    var skHtml = buildProductGridSkeletonHtml(sk, false);
    var tabsHtml = buildFilterTabsHtml(cat, tabPrefix);
    var panelsHtml = buildTabPanelsHtml(
      tabPrefix,
      baseGrid,
      children.length + 5,
      skHtml,
      1
    );

    return (
      '<section class="section section-home-category-products section-product-tabs" data-home-category-id="' +
      escapeHtml(cid) +
      '" hidden aria-hidden="true" style="display:none;--section-padding: 0;--section-margin: 24px 0 24px;--section-padding-mb: 0;--section-margin-mb: 24px 0 24px;">' +
      '<div class="container">' +
      '<div class="section-card">' +
      '<tabs-section data-type=".card-product--vertical">' +
      '<div>' +
      '<div class="flex justify-between items-center md:gap-3 flex-wrap">' +
      '<div class="heading-bar">' +
      '<h2 class="heading w-auto font-semibold">' +
      '<a class="link" href="' +
      escapeHtml(titleHref) +
      '" title="' +
      escapeHtml(title) +
      '">' +
      escapeHtml(title) +
      '</a>' +
      '</h2>' +
      '</div>' +
      tabsHtml +
      '</div>' +
      panelsHtml +
      '<div class="ww-home-viewmore">' +
      '<a href="' +
      escapeHtml(defaultViewmoreHref) +
      '" title="Xem tất cả" class="ww-home-viewmore__btn ww-home-viewmore__btn--compact tab-viewmore">' +
      '<span class="ww-home-viewmore__label">Xem tất cả</span>' +
      '<span class="ww-home-viewmore__icon" aria-hidden="true"><i class="icon icon-carret-right"></i></span>' +
      '</a>' +
      '</div>' +
      '</div>' +
      '</tabs-section>' +
      '</div>' +
      '</div>' +
      '</section>'
    );
  }

  function loadCategorySectionTabs(cat, perPage) {
    var catId = cat && cat.ID;
    if (!catId) return;
    var isAllProducts = cat && cat.IS_ALL_PRODUCTS === true;
    var filterCategoryId = isAllProducts ? null : catId;
    var p = '#home-category-products-' + catId;
    var n = perPage == null ? 10 : perPage;
    var children = getCategoryChildren(cat);
    // Panel 1 = Tất cả; panel 2.. = menu con; sau đó mới tới tab sắp xếp.
    var sortStartIndex = children.length + 2;
    beginCategoryLoads(catId, children.length + 2);

    loadProducts('category', p + '-t1', {
      categoryId: filterCategoryId,
      perPage: n,
      boLoc: 'default',
      trackCategoryId: catId,
    });

    for (var i = 0; i < children.length; i++) {
      loadProducts('category', p + '-t' + (i + 2), {
        categoryId: children[i].ID,
        perPage: n,
        boLoc: 'default',
        trackCategoryId: catId,
      });
    }

    var sortTabs = [
      { idx: sortStartIndex, boLoc: 'gia-tang' },
      { idx: sortStartIndex + 1, boLoc: 'gia-giam' },
      { idx: sortStartIndex + 2, boLoc: 'a-z' },
      { idx: sortStartIndex + 3, boLoc: 'z-a' },
    ];

    for (var s = 0; s < sortTabs.length; s++) {
      var skEl = document.querySelector(p + '-t' + sortTabs[s].idx);
      if (skEl) skEl.innerHTML = buildProductGridSkeletonHtml(n, false);
    }

    fetchAllCategoryProducts(filterCategoryId)
      .then(function (allRows) {
        var hasAny = allRows && allRows.length > 0;
        for (var t = 0; t < sortTabs.length; t++) {
          var tab = sortTabs[t];
          var el = document.querySelector(p + '-t' + tab.idx);
          if (!el) continue;
          if (!hasAny) {
            el.innerHTML = buildEmptyProductsHtml();
            continue;
          }
          var sorted = sortProductsByBoLoc(allRows, tab.boLoc).slice(0, n);
          renderProductsInto(el, sorted, 'category');
        }
        noteCategoryLoadResult(catId, hasAny);
        if (hasAny) {
          window.dispatchEvent(
            new CustomEvent('home-product-cards-loaded', { detail: { section: 'category', categoryId: catId } })
          );
        }
      })
      .catch(function () {
        for (var t = 0; t < sortTabs.length; t++) {
          var el = document.querySelector(p + '-t' + sortTabs[t].idx);
          if (el) el.innerHTML = buildEmptyProductsHtml();
        }
        noteCategoryLoadResult(catId, false);
      });
  }

  function insertCategorySectionsAndLoad() {
    // Ưu anchor cố định (sau banner ngang) để không phụ thuộc section tĩnh đã xóa; fallback .section-product-tabs rồi main
    var anchor =
      document.getElementById('home-category-section-anchor') ||
      document.querySelector('.section-product-tabs');
    var mainEl = document.querySelector('main');
    if (!anchor && !mainEl) return;

    // Tránh chèn lặp
    if (document.getElementById('home-category-sections')) return;

    var wrapper = document.createElement('div');
    wrapper.id = 'home-category-sections';
    if (anchor && anchor.parentNode) {
      anchor.parentNode.insertBefore(wrapper, anchor.nextSibling);
    } else if (mainEl) {
      mainEl.appendChild(wrapper);
    }

    var catParams = new URLSearchParams();
    catParams.set('PAGE', '1');
    catParams.set('PER_PAGE', '999');
    catParams.set('IS_GET_ALL_ELEMENTS', 'true');

    fetch(cfg.categoryUrl + '?' + catParams.toString(), {
      method: 'GET',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) {
        var cats = (data && data.DATAS && data.DATAS.CATEGORY_P && data.DATAS.CATEGORY_P.DATA) || [];
        if (!cats || !cats.length) {
          hideHomeProductsSkeleton();
          return;
        }

        // Chỉ đổ section cho danh mục root (PARENT_ID = null)
        var roots = cats.filter(function (c) {
          if (!c || c.PARENT_ID != null) return false;
          var external = String(c.ATTR1 || '').trim();
          if (external.indexOf('http') === 0) return false;
          return true;
        });

        if (!roots.length) {
          hideHomeProductsSkeleton();
          return;
        }

        roots.sort(function (a, b) {
          var sa = a && a.SORT_ORDER != null ? Number(a.SORT_ORDER) : 0;
          var sb = b && b.SORT_ORDER != null ? Number(b.SORT_ORDER) : 0;
          return sa - sb;
        });

        var allProducts = {
          ID: 'all',
          TEN_DANH_MUC_SAN_PHAM: 'Tất cả sản phẩm',
          TEN_DANH_MUC_SAN_PHAM_SLUG: 'tat-ca-san-pham',
          DANH_SACH_CHILDREN: [],
          IS_ALL_PRODUCTS: true,
        };
        wrapper.insertAdjacentHTML('beforeend', buildCategorySectionHtml(allProducts, 10));
        loadCategorySectionTabs(allProducts, 10);

        for (var i = 0; i < roots.length; i++) {
          var c = roots[i];
          if (!c || !c.ID) continue;
          wrapper.insertAdjacentHTML('beforeend', buildCategorySectionHtml(c, 10));
          loadCategorySectionTabs(c, 10);
        }
      })
      .catch(function () {
        hideHomeProductsSkeleton();
      });
  }

  function run() {
    loadProducts('flash', '#home-flashsale-products');
    insertCategorySectionsAndLoad();

    // Cập nhật "Xem tất cả" theo tab đang chọn
    document.addEventListener(
      'click',
      function (e) {
        var tab = e.target && e.target.closest ? e.target.closest('.section-home-category-products .tab-btn[data-ww-href]') : null;
        if (!tab) return;
        var href = tab.getAttribute('data-ww-href');
        if (!href) return;
        var section = tab.closest('.section-home-category-products');
        var link = section && section.querySelector('a.tab-viewmore');
        if (link) {
          link.setAttribute('href', href);
        }
      },
      true
    );
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
</script>
