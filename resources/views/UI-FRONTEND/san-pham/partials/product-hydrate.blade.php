<script>
(function () {
  var productId = {{ (int) $productId }};
  var cfg = {
    apiDetail: @json(url('/api/public/product/detail')),
    appUrl: @json(rtrim(url('/'), '/')),
    defaultImg: @json(asset('image/UI-BACKEND/default-image.png')),
  };

  document.body.classList.add('product-detail-loading');

  function markProductReady() {
    var form = document.getElementById('ww-pd-product-form');
    if (form) {
      form.classList.remove('is-loading');
      form.classList.add('is-ready');
    }
    document.body.classList.remove('product-detail-loading');
    var breadcrumbTitle = document.getElementById('ww-pd-breadcrumb-title');
    if (breadcrumbTitle) breadcrumbTitle.hidden = false;
  }

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

  /** Bỏ prefix 1x1_ / 3x2_ ... khỏi tên file trong path/URL */
  function stripAspectRatioPrefix(path) {
    if (!path) return '';
    return String(path).replace(/\/(\d+x\d+)_([^\/?#]+)(\?[^#]*)?(#.*)?$/i, '/$2$3$4');
  }

  function relativeDisplayImagePathFromImg(img) {
    if (!img) return '';
    if (img.PATH && /^https?:\/\//i.test(String(img.PATH))) return String(img.PATH);
    var fname = img.IMAGE_THUMNAIL || img.NAME || '';
    // Gallery / thumbnail: luôn dùng bản crop aspect ratio (mặc định 1x1)
    var ar = (img.ASPECT_RATIO && String(img.ASPECT_RATIO).trim()) || '1x1';
    if (img.DIRECTORY && fname) {
      return String(img.DIRECTORY).replace(/^\/+|\/+$/g, '') + '/' + ar + '_' + fname;
    }
    if (img.PATH) return String(img.PATH).replace(/\\/g, '/');
    return '';
  }

  /** Ảnh gốc (không prefix aspect ratio) — lightbox + download */
  function relativeOriginalImagePathFromImg(img) {
    if (!img) return '';
    if (img.PATH && /^https?:\/\//i.test(String(img.PATH))) return String(img.PATH);
    var fname = img.IMAGE_THUMNAIL || img.NAME || '';
    // Ưu tiên DIRECTORY/NAME — đúng file gốc lúc upload
    if (img.DIRECTORY && fname) {
      return String(img.DIRECTORY).replace(/^\/+|\/+$/g, '') + '/' + fname;
    }
    if (img.PATH) return stripAspectRatioPrefix(String(img.PATH).replace(/\\/g, '/'));
    return '';
  }

  function formatIntViDots(num) {
    var s = String(Math.floor(Math.abs(Number(num))));
    if (s.length <= 3) return s;
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function formatPriceShortVnd(amount) {
    var n = Math.round(Number(amount));
    if (!isFinite(n) || n <= 0) return 'Liên hệ';
    if (n < 1000) return String(n);
    return formatIntViDots(n) + ' ₫';
  }

  function collectImages(p) {
    var items = [];
    var seen = {};
    var productUpd = p && p.UPD_DT ? p.UPD_DT : '';
    function push(list) {
      if (!list) return;
      list.forEach(function (img) {
        var displayRel = relativeDisplayImagePathFromImg(img);
        var originalRel = relativeOriginalImagePathFromImg(img) || displayRel;
        if (!displayRel && !originalRel) return;
        var key = originalRel || displayRel;
        if (seen[key]) return;
        seen[key] = true;
        var upd = (window.wwStorefrontImage && window.wwStorefrontImage.pickUpdTime)
          ? window.wwStorefrontImage.pickUpdTime(p, img)
          : (productUpd || (img && img.UPD_DT) || '');
        items.push({
          display: joinAppUrl(displayRel || originalRel, upd),
          original: joinAppUrl(originalRel || displayRel, upd),
          displayRel: displayRel || originalRel,
          originalRel: originalRel || displayRel,
        });
      });
    }
    push(p.DANH_SACH_HINH_ANH_DAI_DIEN);
    push(p.DANH_SACH_HINH_ANH);
    if (Array.isArray(p.DANH_SACH_BIEN_THE)) {
      p.DANH_SACH_BIEN_THE.forEach(function (v) {
        if (v) push(v.DANH_SACH_HINH_ANH_DAI_DIEN);
      });
    }
    if (!items.length) {
      items.push({
        display: cfg.defaultImg,
        original: cfg.defaultImg,
        displayRel: '',
        originalRel: '',
      });
    }
    return {
      items: items,
      urls: items.map(function (it) { return it.display; }),
      originals: items.map(function (it) { return it.original; }),
      rels: items.map(function (it) { return it.displayRel; }),
    };
  }

  function renderGallery(items, title) {
    var main = document.getElementById('ww-pd-gallery-main');
    var thumbs = document.getElementById('ww-pd-gallery-thumbs');
    if (!main || !thumbs) return;
    var mainHtml = '', thumbHtml = '';
    items.forEach(function (item, i) {
      var displayUrl = item.display || item;
      var originalUrl = item.original || displayUrl;
      mainHtml +=
        '<div class="embla__slide w-full grow-0 shrink-0 aspect-square flex items-center justify-center relative swiper-spotlight cursor-zoom-in" data-src="' + originalUrl + '" data-original-src="' + originalUrl + '" data-display-src="' + displayUrl + '" data-index="' + i + '">' +
        '<img class="object-contain rounded-lg scale-[var(--image-scale)] gallery-main-img" src="' + displayUrl + '" alt="' + title + '" style="--image-scale:1" loading="' + (i === 0 ? 'eager' : 'lazy') + '"></div>';
      thumbHtml +=
        '<div class="embla__slide aspect-square cursor-pointer grow-0 shrink-0 w-[6.1rem] md:w-[9rem]' + (i === 0 ? ' embla-thumbs__slide--selected' : '') + '">' +
        '<div class="flex items-center justify-center w-full h-full"><img class="object-contain w-auto" src="' + displayUrl + '" width="64" height="64" loading="lazy" alt=""></div></div>';
    });
    main.innerHTML = mainHtml;
    thumbs.innerHTML = thumbHtml;
    reinitMediaGallery();
  }

  function reinitMediaGallery() {
    function run() {
      var gallery = document.querySelector('#ww-pd-product-form media-gallery');
      if (!gallery || typeof gallery.init !== 'function') return false;
      gallery._waitHydrate = false;
      if (typeof gallery.destroyGallery === 'function') {
        gallery.destroyGallery();
      }
      gallery.init();
      if (typeof gallery.bindSpotlightClicks === 'function') {
        gallery.bindSpotlightClicks();
      }
      return true;
    }

    if (run()) return;

    var tries = 0;
    var timer = setInterval(function () {
      tries += 1;
      if (run() || tries > 40) clearInterval(timer);
    }, 100);
  }

  function imageSrc(img) {
    return (img && (img.currentSrc || img.src)) || '';
  }

  function isSpotlightReady() {
    return !!(window.Spotlight && typeof window.Spotlight.show === 'function');
  }

  function ensureSpotlightReady(done) {
    if (isSpotlightReady()) {
      done(true);
      return;
    }
    if (typeof window.loadDefer === 'function') window.loadDefer();
    var attempts = 0;
    var timer = setInterval(function () {
      attempts += 1;
      if (isSpotlightReady()) {
        clearInterval(timer);
        done(true);
      } else if (attempts > 80) {
        clearInterval(timer);
        done(false);
      }
    }, 100);
  }

  function openDescLightbox(slides, activeIndex) {
    var active = slides[activeIndex];
    var src = active && active.dataset.src;
    if (!src) return;

    ensureSpotlightReady(function (ok) {
      if (!ok || !isSpotlightReady()) {
        window.open(src, '_blank');
        return;
      }
      var list = slides.map(function (s) {
        var display = s.dataset.src || '';
        var original = s.dataset.originalSrc || stripAspectRatioPrefix(display) || display;
        return { src: original };
      });
      window.Spotlight.show(list, { download: true });
      if (
        window.wwStorefrontImage &&
        typeof window.wwStorefrontImage.patchSpotlightDownload === 'function'
      ) {
        window.wwStorefrontImage.patchSpotlightDownload();
      }
      window.Spotlight.goto(activeIndex + 1);
    });
  }

  /** Ảnh trong "Đặc điểm nổi bật" — click mở lightbox giống gallery */
  function isInlineEmojiImage(img) {
    if (!img) return false;
    var src = (img.currentSrc || img.src || '').toLowerCase();
    if (src.indexOf('emoji.php') !== -1 || src.indexOf('/emoji/') !== -1) return true;
    var w = parseInt(img.getAttribute('width') || '0', 10);
    var h = parseInt(img.getAttribute('height') || '0', 10);
    if ((w > 0 && w <= 32) || (h > 0 && h <= 32)) return true;
    if (img.naturalWidth > 0 && img.naturalWidth <= 32 && img.naturalHeight <= 32) return true;
    return false;
  }

  function bindDescImageSpotlight(root) {
    if (!root) return;

    // Preload Spotlight (vendors) sớm để click lần đầu đã mở được
    if (typeof window.loadDefer === 'function') window.loadDefer();

    var slides = [];
    root.querySelectorAll('img').forEach(function (img) {
      // Không bọc emoji Facebook/icon nhỏ — tránh xuống dòng
      if (isInlineEmojiImage(img)) return;

      var src = imageSrc(img);
      if (!src || src.indexOf('default-image') !== -1) return;

      var wrap = img.closest('.swiper-spotlight');
      if (!wrap) {
        wrap = document.createElement('span');
        wrap.className = 'swiper-spotlight cursor-zoom-in ww-pd-desc-spotlight';
        wrap.style.display = 'block';
        wrap.style.maxWidth = '100%';
        wrap.style.cursor = 'zoom-in';
        if (img.parentNode) {
          img.parentNode.insertBefore(wrap, img);
          wrap.appendChild(img);
        }
      }
      wrap.dataset.src = src;
      wrap.dataset.originalSrc = stripAspectRatioPrefix(src) || src;
      slides.push(wrap);
    });

    slides.forEach(function (el, idx) {
      el.dataset.index = String(idx);
      if (el.dataset.spotlightBound === '1') return;
      el.dataset.spotlightBound = '1';
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openDescLightbox(slides, idx);
      });
    });

    // Nội dung đổi chiều cao → re-init expandable
    var expandable = root.closest('expandable-content');
    if (expandable && typeof expandable.init === 'function') {
      setTimeout(function () {
        expandable.init();
      }, 50);
    }
  }

  function escapeAttr(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  function normalizeImgKey(url) {
    var u = String(url || '').trim();
    if (!u) return '';
    try {
      var a = document.createElement('a');
      a.href = u;
      u = a.pathname || u;
    } catch (e) {
      u = u.split('?')[0].split('#')[0];
    }
    return String(u)
      .replace(/\/\d+x\d+_([^\/?#]+)(\?[^#]*)?(#.*)?$/i, '/$1')
      .toLowerCase();
  }

  function variantImageUrl(v, productUpd) {
    var list = v && v.DANH_SACH_HINH_ANH_DAI_DIEN;
    if (!Array.isArray(list) || !list[0]) return '';
    var img = list[0];
    var path = '';
    if (img.PATH && /^https?:\/\//i.test(String(img.PATH))) {
      path = String(img.PATH);
    } else {
      path = relativeDisplayImagePathFromImg(img) || relativeOriginalImagePathFromImg(img);
    }
    if (!path) return '';
    var upd = (window.wwStorefrontImage && window.wwStorefrontImage.pickUpdTime)
      ? window.wwStorefrontImage.pickUpdTime({ UPD_DT: productUpd }, img)
      : (productUpd || img.UPD_DT || '');
    return joinAppUrl(path, upd);
  }

  function parseVariants(p, imageUrls) {
    var raw = Array.isArray(p.DANH_SACH_BIEN_THE) ? p.DANH_SACH_BIEN_THE : [];
    var keys = (imageUrls || []).map(normalizeImgKey);
    var productUpd = p.UPD_DT || '';
    var out = [];
    raw.forEach(function (v) {
      if (!v) return;
      var title = String(v.TEN_BIEN_THE || v.MAU_SAC || '').trim() || 'Mặc định';
      var img = variantImageUrl(v, productUpd);
      var imageIndex = -1;
      if (img) {
        var vBase = imageFileName(img);
        for (var i = 0; i < keys.length; i++) {
          if (vBase && imageFileName(keys[i]) === vBase) {
            imageIndex = i;
            break;
          }
        }
      }
      out.push({
        id: Number(v.ID || 0),
        title: title,
        price: Math.round(Number(v.GIA_BAN) || 0),
        compare: Math.round(Number(v.GIA_GOC) || 0),
        inStock: !!(v.CON_HANG === true || v.CON_HANG === 1 || v.CON_HANG === '1'),
        image: img,
        imageIndex: imageIndex,
      });
    });
    return out;
  }

  function productHasVariants(variants) {
    if (!variants || !variants.length) return false;
    if (variants.length > 1) return true;
    var t = String(variants[0].title || '').toLowerCase();
    return t !== 'default title' && t !== 'mặc định' && t !== 'mac dinh';
  }

  function updatePriceUi(priceInt, compareInt, isContactPrice, displayText) {
    var priceLabel = isContactPrice
      ? 'Liên hệ'
      : (displayText || (priceInt > 0 ? formatPriceShortVnd(priceInt) : 'Liên hệ'));
    var priceEl = document.getElementById('ww-pd-price');
    if (priceEl) priceEl.textContent = priceLabel;
    var priceInput = document.getElementById('ww-pd-product-price');
    if (priceInput) priceInput.value = String(priceInt > 0 ? priceInt : 0);
    var compareEl = document.getElementById('ww-pd-compare');
    var badge = document.getElementById('ww-pd-badge');
    if (compareEl) {
      compareEl.hidden = true;
      compareEl.textContent = '';
    }
    if (badge) {
      badge.hidden = true;
      badge.textContent = '';
    }
    if (!isContactPrice && !displayText && priceInt > 0 && compareInt > priceInt) {
      if (compareEl) {
        compareEl.textContent = formatPriceShortVnd(compareInt);
        compareEl.hidden = false;
      }
      if (badge) {
        var pct = Math.min(99, Math.max(1, Math.round((1 - priceInt / compareInt) * 100)));
        badge.textContent = '-' + pct + '%';
        badge.hidden = false;
      }
    }
  }

  function setStockUi(inStock, productSold) {
    var isSold = !!productSold || !inStock;
    var stockLabel = document.getElementById('ww-pd-stock-label');
    if (stockLabel) {
      stockLabel.textContent = isSold ? 'Hết hàng' : 'Còn hàng';
      stockLabel.classList.toggle('text-error', isSold);
      stockLabel.classList.toggle('text-success', !isSold);
    }
    var soldOutBtn = document.getElementById('ww-pd-soldout');
    var cartForm = document.getElementById('add-to-cart-form');
    if (soldOutBtn) soldOutBtn.classList.toggle('hidden', !isSold);
    if (cartForm) cartForm.classList.toggle('hidden', isSold);
  }

  function clearVariantError() {
    var box = document.getElementById('ww-pd-variants');
    var err = document.getElementById('ww-pd-variant-error');
    if (box) box.classList.remove('is-error');
    if (err) err.hidden = true;
  }

  function showVariantError() {
    var box = document.getElementById('ww-pd-variants');
    var err = document.getElementById('ww-pd-variant-error');
    if (box) {
      box.classList.add('is-error');
      box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    if (err) err.hidden = false;
  }

  function imageFileName(url) {
    var k = normalizeImgKey(url);
    return (k.split('/').pop() || '').split('?')[0];
  }

  function findPdGalleryImageIndex(imageUrl, preferredIndex) {
    var slides = document.querySelectorAll('#ww-pd-gallery-main .embla__slide');
    if (!slides.length) return -1;

    var want = imageFileName(imageUrl);
    if (want) {
      for (var i = 0; i < slides.length; i++) {
        var slide = slides[i];
        var img = slide.querySelector('img');
        var candidates = [
          slide.getAttribute('data-display-src'),
          slide.getAttribute('data-original-src'),
          slide.getAttribute('data-src'),
          img && img.getAttribute('src'),
        ];
        for (var c = 0; c < candidates.length; c++) {
          if (imageFileName(candidates[c] || '') === want) return i;
        }
      }
    }

    var pref = parseInt(preferredIndex, 10);
    if (!isNaN(pref) && pref >= 0 && pref < slides.length) return pref;
    return -1;
  }

  function scrollPdGalleryTo(index, imageUrl) {
    var gallery = document.querySelector('#ww-pd-product-form media-gallery');
    var slides = document.querySelectorAll('#ww-pd-gallery-main .embla__slide');
    var thumbs = document.querySelectorAll('#ww-pd-gallery-thumbs .embla__slide');
    if (!slides.length) return;

    if (index >= 0 && index < slides.length) {
      if (gallery && gallery.mainGallery && typeof gallery.mainGallery.scrollTo === 'function') {
        gallery.mainGallery.scrollTo(index);
      } else if (gallery && typeof gallery.slideTo === 'function') {
        gallery.slideTo(index);
      }
      if (gallery && gallery.thumbGallery && typeof gallery.thumbGallery.scrollTo === 'function') {
        gallery.thumbGallery.scrollTo(index);
      }
      if (thumbs[index]) {
        thumbs[index].click();
      }
      thumbs.forEach(function (slide, i) {
        slide.classList.toggle('embla-thumbs__slide--selected', i === index);
      });
      return;
    }

    if (!imageUrl) return;
    var currentIdx =
      gallery && gallery.mainGallery && typeof gallery.mainGallery.selectedScrollSnap === 'function'
        ? gallery.mainGallery.selectedScrollSnap()
        : 0;
    var slide = slides[currentIdx] || slides[0];
    var mainImg = slide.querySelector('img.gallery-main-img') || slide.querySelector('img');
    if (mainImg) mainImg.src = imageUrl;
    slide.setAttribute('data-src', imageUrl);
    slide.setAttribute('data-original-src', imageUrl);
    slide.setAttribute('data-display-src', imageUrl);
  }

  function selectPdVariant(btn, opts) {
    opts = opts || {};
    var box = document.getElementById('ww-pd-variants');
    if (!box || !btn) return;
    box.querySelectorAll('.ww-pd-variant-btn').forEach(function (el) {
      el.classList.toggle('is-active', el === btn);
    });
    clearVariantError();

    var title = btn.getAttribute('data-title') || '';
    var price = Math.round(Number(btn.getAttribute('data-price')) || 0);
    var compare = Math.round(Number(btn.getAttribute('data-compare')) || 0);
    var inStock = btn.getAttribute('data-in-stock') === '1';
    var variantId = btn.getAttribute('data-variant-id') || '';
    var imageRel = btn.getAttribute('data-image-rel') || '';
    var imageUrl = btn.getAttribute('data-image') || '';
    var imageIndexAttr = btn.getAttribute('data-image-index');

    var label = document.getElementById('ww-pd-variant-label');
    if (label) label.textContent = title || 'Đã chọn';

    var variantInput = document.getElementById('ww-pd-variant-id');
    if (variantInput) variantInput.value = String(variantId);
    var titleInput = document.getElementById('ww-pd-variant-title');
    if (titleInput) titleInput.value = title;

    updatePriceUi(price, compare, price <= 0, '');
    setStockUi(inStock, false);

    var imgInput = document.getElementById('ww-pd-product-image');
    if (imgInput && (imageRel || imageUrl)) imgInput.value = imageRel || imageUrl;

    if (!opts.skipGallery) {
      var idx = findPdGalleryImageIndex(imageUrl, imageIndexAttr);
      scrollPdGalleryTo(idx, imageUrl);
    }
  }

  function bindPdVariants() {
    var box = document.getElementById('ww-pd-variants');
    if (!box || box.dataset.bound === '1') return;
    box.dataset.bound = '1';
    box.addEventListener('click', function (e) {
      var btn = e.target && e.target.closest ? e.target.closest('.ww-pd-variant-btn') : null;
      if (!btn || btn.classList.contains('is-soldout')) return;
      selectPdVariant(btn);
    });

    window.__wwBeforeAddToCart = function (btn, form) {
      var inDetail = !!(form && form.id === 'add-to-cart-form') || !!(btn && btn.closest && btn.closest('#ww-pd-product-form'));
      if (!inDetail) return true;
      var list = document.getElementById('ww-pd-variants');
      if (!list || list.hidden || list.getAttribute('data-requires-variant') !== '1') return true;
      var active = list.querySelector('.ww-pd-variant-btn.is-active');
      if (active) {
        clearVariantError();
        return true;
      }
      showVariantError();
      return false;
    };
  }

  function renderVariants(p, imgs) {
    var box = document.getElementById('ww-pd-variants');
    var list = document.getElementById('ww-pd-variant-list');
    if (!box || !list) return { hasVariants: false, variants: [] };

    var variants = parseVariants(p, imgs.urls || []);
    var hasVariants = productHasVariants(variants);
    var groupLabel = String(p.TEN_NHOM_BIEN_THE || 'Phân loại').trim() || 'Phân loại';

    var groupLabelEl = document.getElementById('ww-pd-variant-group-label');
    if (groupLabelEl) groupLabelEl.textContent = groupLabel;
    var err = document.getElementById('ww-pd-variant-error');
    if (err) {
      err.textContent =
        'Vui lòng chọn ' + (groupLabel.toLowerCase() === 'phân loại' ? 'Phân loại hàng' : groupLabel);
    }
    if (list) list.setAttribute('aria-label', groupLabel);

    ['ww-pd-addtocart', 'ww-pd-buynow'].forEach(function (id) {
      var btn = document.getElementById(id);
      if (btn) btn.setAttribute('data-requires-variant', hasVariants ? '1' : '0');
    });
    box.setAttribute('data-requires-variant', hasVariants ? '1' : '0');

    if (!hasVariants) {
      box.hidden = true;
      list.innerHTML = '';
      return { hasVariants: false, variants: variants };
    }

    var html = '';
    variants.forEach(function (v) {
      var thumb = v.image || (imgs.urls && imgs.urls[0]) || '';
      html +=
        '<button type="button" class="ww-pd-variant-btn' +
        (v.inStock ? '' : ' is-soldout') +
        (thumb ? ' has-thumb' : '') +
        '" role="option" data-variant-id="' +
        escapeAttr(String(v.id)) +
        '" data-title="' +
        escapeAttr(v.title) +
        '" data-price="' +
        escapeAttr(String(v.price)) +
        '" data-compare="' +
        escapeAttr(String(v.compare)) +
        '" data-in-stock="' +
        (v.inStock ? '1' : '0') +
        '" data-image="' +
        escapeAttr(v.image) +
        '" data-image-rel="' +
        escapeAttr(v.imageIndex >= 0 && imgs.rels ? imgs.rels[v.imageIndex] || '' : '') +
        '" data-image-index="' +
        escapeAttr(String(v.imageIndex)) +
        '" title="' +
        escapeAttr(v.title) +
        '">' +
        (thumb
          ? '<span class="ww-pd-variant-thumb" aria-hidden="true"><img src="' +
            escapeAttr(thumb) +
            '" alt="" width="28" height="28" loading="lazy" decoding="async"></span>'
          : '') +
        '<span class="ww-pd-variant-text">' +
        escapeAttr(v.title) +
        '</span></button>';
    });
    list.innerHTML = html;
    box.hidden = false;
    bindPdVariants();
    clearVariantError();

    var label = document.getElementById('ww-pd-variant-label');
    if (label) label.textContent = 'Vui lòng chọn';

    return { hasVariants: true, variants: variants };
  }

  function renderAttachments(files) {
    var wrap = document.getElementById('ww-pd-attachments');
    var list = document.getElementById('ww-pd-attachments-list');
    if (!wrap || !list) return;

    list.innerHTML = '';
    if (!Array.isArray(files) || files.length === 0) {
      wrap.hidden = true;
      return;
    }

    var html = '';
    files.forEach(function (file) {
      if (!file) return;
      var name = file.ORIGINAL_NAME || file.NAME || 'Tải file';
      var path = file.PATH || '';
      if (!path && file.DIRECTORY && file.NAME) {
        path = String(file.DIRECTORY).replace(/^\/+|\/+$/g, '') + '/' + file.NAME;
      }
      if (!path) return;
      var url = joinAppUrl(path, file.UPD_DT || null);
      html +=
        '<li>' +
          '<a href="' + escapeAttr(url) + '" class="ww-pd-attachment-link" target="_blank" rel="noopener noreferrer" download>' +
            '<i class="icon icon-stickynote" aria-hidden="true"></i>' +
            '<span>' + escapeAttr(name) + '</span>' +
          '</a>' +
        '</li>';
    });

    if (!html) {
      wrap.hidden = true;
      return;
    }
    list.innerHTML = html;
    wrap.hidden = false;
  }

  function renderProduct(p) {
    var title = p.TEN_SAN_PHAM || 'Sản phẩm';
    var priceInt = Math.round(Number(p.GIA_CA) || 0);
    var compareInt = Math.round(Number(p.GIA_GOC) || 0);
    var displayText = p.GIA_HIEN_THI != null ? String(p.GIA_HIEN_THI).trim() : '';
    var isContactPrice =
      p.IS_GIA_CA_LIEN_HE === true ||
      p.IS_GIA_CA_LIEN_HE === 1 ||
      p.IS_GIA_CA_LIEN_HE === '1';
    var slug = (p.TEN_SAN_PHAM_SLUG && String(p.TEN_SAN_PHAM_SLUG).trim()) || 'sp';
    var imgs = collectImages(p);
    var productSold = String(p.TRANG_THAI || '').toUpperCase() === 'SOLD';

    document.title = title + ' — Win Win';
    var metaDesc = document.getElementById('ww-meta-description');
    if (metaDesc) metaDesc.setAttribute('content', (p.MO_TA_NGAN || title).substring(0, 160));

    var setText = function (id, text) {
      var el = document.getElementById(id);
      if (el) el.textContent = text;
    };
    setText('ww-page-title', title + ' — Win Win');
    setText('ww-pd-breadcrumb-title', title);
    setText('ww-pd-title', title);

    var metaWrap = document.getElementById('ww-pd-meta');
    var skuEl = document.getElementById('ww-pd-sku');
    if (skuEl) {
      skuEl.textContent = String(p.MA_SAN_PHAM || p.ID || productId || '—');
    }
    if (metaWrap) metaWrap.hidden = false;

    var handleEl = document.getElementById('ww-pd-product-handle');
    if (handleEl) handleEl.value = slug;
    var titleInput = document.getElementById('ww-pd-product-title');
    if (titleInput) titleInput.value = title;
    var imgInput = document.getElementById('ww-pd-product-image');
    if (imgInput) imgInput.value = imgs.rels[0] || '';

    var defaultVariantId = String(p.ATTR1 || p.ID || productId || '');
    var variantInput = document.getElementById('ww-pd-variant-id');
    if (variantInput) variantInput.value = defaultVariantId;
    var variantTitleInput = document.getElementById('ww-pd-variant-title');
    if (variantTitleInput) variantTitleInput.value = 'Mặc định';

    var variantState = renderVariants(p, imgs);
    if (variantState.hasVariants) {
      // Shopee-style: hiện giá thấp nhất, chưa chọn biến thể
      var minPrice = 0;
      var minCompare = 0;
      var anyInStock = false;
      variantState.variants.forEach(function (v) {
        if (v.inStock) anyInStock = true;
        if (v.price <= 0) return;
        if (minPrice <= 0 || v.price < minPrice) {
          minPrice = v.price;
          minCompare = v.compare || 0;
        }
      });
      if (minPrice > 0) {
        priceInt = minPrice;
        compareInt = minCompare;
        isContactPrice = false;
        displayText = '';
      }
      if (variantInput) variantInput.value = '';
      if (variantTitleInput) variantTitleInput.value = '';
      setStockUi(anyInStock, productSold);
    } else {
      setStockUi(!productSold, productSold);
      if (!defaultVariantId && variantState.variants.length === 1 && variantState.variants[0].id) {
        if (variantInput) variantInput.value = String(variantState.variants[0].id);
      }
    }

    updatePriceUi(priceInt, compareInt, isContactPrice, displayText);
    renderGallery(imgs.items, title);

    var desc = p.MO_TA_CHI_TIET || p.MO_TA_NGAN || '';
    var descEl = document.getElementById('ww-pd-desc');
    if (descEl && desc) {
      descEl.innerHTML = desc;
      bindDescImageSpotlight(descEl);
    }

    renderAttachments(p.DANH_SACH_FILE_DINH_KEM);

    var jsonEl = document.getElementById('ww-pd-product-json');
    if (jsonEl) {
      jsonEl.textContent = JSON.stringify({
        id: p.ID,
        name: title,
        alias: slug,
        price: priceInt,
        available: !productSold,
        status: p.TRANG_THAI || 'USING',
        images: imgs.items.map(function (it) {
          return { src: it.display, original: it.original };
        }),
        variants: variantState.variants,
      });
    }

    var cat = p.DANH_MUC_SAN_PHAM;
    var categoryId = cat && cat.ID ? Number(cat.ID) : 0;
    var categoryName = cat && cat.TEN_DANH_MUC_SAN_PHAM ? String(cat.TEN_DANH_MUC_SAN_PHAM) : '';
    var categoryInput = document.getElementById('ww-pd-product-category-id');
    if (categoryInput) categoryInput.value = categoryId ? String(categoryId) : '';
    window.__wwCurrentProduct = {
      id: Number(p.ID || productId),
      categoryId: categoryId,
      categoryName: categoryName,
    };
    document.dispatchEvent(
      new CustomEvent('ww:product-hydrated', { detail: window.__wwCurrentProduct })
    );

    if (window.EGATheme && window.EGATheme.publish && window.themeConfigs) {
      window.EGATheme.publish(window.themeConfigs.productLoaded);
    }

    markProductReady();
  }

  fetch(cfg.apiDetail + '/' + productId, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
  })
    .then(function (r) {
      if (!r.ok) throw new Error('404');
      return r.json();
    })
    .then(function (res) {
      var p = res && res.DATAS && res.DATAS.PRODUCT;
      if (!p) throw new Error('empty');
      renderProduct(p);
    })
    .catch(function (err) {
      console.error(err);
      markProductReady();
    });
})();
</script>
