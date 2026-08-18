{{-- Cache-bust ảnh storefront theo UPD_DT (?upd_time=) --}}
<script>
(function (w) {
  function toUpdTime(updDt) {
    if (updDt == null || updDt === '') return '';
    if (typeof updDt === 'number' && isFinite(updDt)) return String(Math.floor(updDt));
    var raw = String(updDt).trim();
    if (/^\d+$/.test(raw)) return raw;
    var t = Date.parse(raw.replace(' ', 'T'));
    if (!isNaN(t)) return String(Math.floor(t / 1000));
    var digits = raw.replace(/\D+/g, '');
    return digits || '';
  }

  function toCardThumb(url, size) {
    if (!url) return '';
    var u = String(url);
    if (!/dktcdn\.net/i.test(u)) return u;
    var s = size || 'large';
    if (!/^(small|compact|medium|large|grande)$/.test(s)) s = 'large';
    if (/\/thumb\/[a-z]+\//i.test(u)) {
      return u.replace(/\/thumb\/[a-z]+\//i, '/thumb/' + s + '/');
    }
    return u.replace(/(https?:)?(\/\/)?([^/]*dktcdn\.net)\//i, function (_, proto, slashes, host) {
      return (proto || 'https:') + (slashes || '//') + host + '/thumb/' + s + '/';
    });
  }

  function appendUpdTime(url, updDt) {
    if (!url) return '';
    if (/^https?:\/\//i.test(String(url)) && /dktcdn\.net/i.test(String(url))) {
      return url; // CDN Sapo đã có ?v= — không gắn upd_time
    }
    var bust = toUpdTime(updDt);
    if (!bust) return url;
    if (url.indexOf('upd_time=') !== -1) return url;
    return url + (url.indexOf('?') >= 0 ? '&' : '?') + 'upd_time=' + encodeURIComponent(bust);
  }

  /** URL ảnh: hỗ trợ path local và URL tuyệt đối (Sapo CDN) */
  function resolveMediaUrl(pathRel, updDt, appUrl) {
    if (!pathRel) return '';
    var p = String(pathRel).trim();
    if (/^https?:\/\//i.test(p) || p.indexOf('//') === 0) {
      return appendUpdTime(p, updDt);
    }
    var base = (appUrl != null && appUrl !== '') ? String(appUrl).replace(/\/+$/, '') : '';
    var url = base ? (base + '/' + p.replace(/^\/+/, '')) : p;
    return appendUpdTime(url, updDt);
  }

  /** URL ảnh card: CDN thumb/large, local giữ nguyên */
  function cardMediaUrl(pathRel, updDt, appUrl) {
    return toCardThumb(resolveMediaUrl(pathRel, updDt, appUrl), 'large');
  }

  var cardImgIo = null;

  function activateLazyCardImages(root) {
    var scope = root || document;
    var imgs = scope.querySelectorAll
      ? scope.querySelectorAll('img.ww-card-img-lazy[data-src]:not([data-img-loaded])')
      : [];
    if (!imgs.length) return;

    if (!('IntersectionObserver' in window)) {
      imgs.forEach(function (img) {
        var url = img.getAttribute('data-src');
        if (url) img.src = url;
        var srcset = img.getAttribute('data-srcset');
        if (srcset) img.setAttribute('srcset', srcset);
        var source = img.parentNode && img.parentNode.querySelector
          ? img.parentNode.querySelector('source[data-srcset]')
          : null;
        if (source) {
          source.setAttribute('srcset', source.getAttribute('data-srcset'));
        }
        img.setAttribute('data-img-loaded', '1');
      });
      return;
    }

    if (!cardImgIo) {
      cardImgIo = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var img = entry.target;
            var url = img.getAttribute('data-src');
            if (url) img.src = url;
            var picture = img.parentNode;
            var source = picture && picture.querySelector
              ? picture.querySelector('source[data-srcset]')
              : null;
            if (source) {
              source.setAttribute('srcset', source.getAttribute('data-srcset') || '');
            }
            img.setAttribute('data-img-loaded', '1');
            cardImgIo.unobserve(img);
          });
        },
        { rootMargin: '400px 0px', threshold: 0.01 }
      );
    }

    imgs.forEach(function (img) {
      if (img.getAttribute('data-img-watching') === '1') return;
      img.setAttribute('data-img-watching', '1');
      cardImgIo.observe(img);
    });
  }

  function bindHoverCardImages() {
    if (w.__wwCardHoverBound) return;
    w.__wwCardHoverBound = true;
    document.addEventListener(
      'mouseover',
      function (e) {
        var t = e.target;
        if (!t || !t.closest) return;
        var card = t.closest('card-product');
        if (!card) return;
        var img = card.querySelector('img.ww-card-hover-img[data-hover-src]');
        if (!img) return;
        img.src = img.getAttribute('data-hover-src');
        img.removeAttribute('data-hover-src');
      },
      true
    );
  }

  /** Ưu tiên UPD_DT entity (sản phẩm/tin), fallback UPD_DT ảnh */
  function pickUpdTime(entity, img) {
    if (entity && (entity.UPD_DT || entity.updDt)) return entity.UPD_DT || entity.updDt;
    if (img && (img.UPD_DT || img.updDt)) return img.UPD_DT || img.updDt;
    return '';
  }

  /** Tên file tải xuống: bỏ ?upd_time=... (tránh tên kiểu .jpg_upd_time=123) */
  function basenameFromUrl(url) {
    if (!url) return 'image.jpg';
    var path = String(url).split('#')[0].split('?')[0];
    try {
      path = decodeURIComponent(path);
    } catch (e) {}
    var name = path.substring(path.lastIndexOf('/') + 1);
    // Phòng trường hợp tên đã bị dính query kiểu .jpg_upd_time=123
    name = name.replace(/[?&]?upd_time=\d+/gi, '').replace(/_upd_time=\d+$/i, '');
    return name || 'image.jpg';
  }

  function downloadImageFile(url) {
    if (!url) return;
    var filename = basenameFromUrl(url);

    // Blob URL → browser luôn dùng đúng attribute download (không dính ?upd_time)
    if (typeof w.fetch === 'function') {
      w.fetch(url, { credentials: 'same-origin' })
        .then(function (res) {
          if (!res.ok) throw new Error('fetch failed');
          return res.blob();
        })
        .then(function (blob) {
          var objUrl = URL.createObjectURL(blob);
          var link = document.createElement('a');
          link.href = objUrl;
          link.download = filename;
          link.rel = 'noopener';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          setTimeout(function () {
            URL.revokeObjectURL(objUrl);
          }, 1500);
        })
        .catch(function () {
          var link = document.createElement('a');
          link.href = url;
          link.download = filename;
          link.rel = 'noopener';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        });
      return;
    }

    var link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.rel = 'noopener';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function currentSpotlightImageSrc() {
    var root = document.getElementById('spotlight');
    if (!root) return '';
    var img = root.querySelector('img');
    return (img && (img.currentSrc || img.src)) || '';
  }

  /**
   * Nút .spl-download gọi hàm nội bộ Spotlight (không qua Spotlight.download).
   * Chặn click capture → tự tải với tên file sạch.
   */
  function bindSpotlightDownloadClick() {
    if (w.__wwSplDlClickBound) return;
    w.__wwSplDlClickBound = true;
    document.addEventListener(
      'click',
      function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('.spl-download') : null;
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
        var src = currentSpotlightImageSrc();
        if (src) downloadImageFile(src);
      },
      true
    );
  }

  /** Spotlight.download API (nếu có chỗ gọi trực tiếp) */
  function patchSpotlightDownload() {
    if (!w.Spotlight || typeof w.Spotlight.download !== 'function') return;
    if (w.Spotlight.__wwDlNamePatched) return;
    w.Spotlight.__wwDlNamePatched = true;
    w.Spotlight.download = function () {
      var src = currentSpotlightImageSrc();
      if (src) downloadImageFile(src);
    };
  }

  function ensureSpotlightDownloadPatched() {
    bindSpotlightDownloadClick();
    patchSpotlightDownload();
    if (w.Spotlight && w.Spotlight.__wwDlNamePatched) return;
    var tries = 0;
    var timer = w.setInterval(function () {
      tries += 1;
      bindSpotlightDownloadClick();
      patchSpotlightDownload();
      if ((w.Spotlight && w.Spotlight.__wwDlNamePatched) || tries > 80) {
        w.clearInterval(timer);
      }
    }, 250);
  }

  w.wwStorefrontImage = {
    toUpdTime: toUpdTime,
    appendUpdTime: appendUpdTime,
    resolveMediaUrl: resolveMediaUrl,
    cardMediaUrl: cardMediaUrl,
    toCardThumb: toCardThumb,
    activateLazyCardImages: activateLazyCardImages,
    bindHoverCardImages: bindHoverCardImages,
    pickUpdTime: pickUpdTime,
    basenameFromUrl: basenameFromUrl,
    downloadImageFile: downloadImageFile,
    patchSpotlightDownload: patchSpotlightDownload,
  };

  bindHoverCardImages();
  ensureSpotlightDownloadPatched();
  if (typeof w.loadDefer === 'function') {
    var _loadDefer = w.loadDefer;
    w.loadDefer = function () {
      var r = _loadDefer.apply(this, arguments);
      ensureSpotlightDownloadPatched();
      return r;
    };
  }
})(window);
</script>
