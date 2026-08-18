<script>
  (function () {
    window.__wwCartScripts = true;

    function csrfToken() {
      var meta =
        (typeof window.__csrfToken === 'function' && window.__csrfToken()) ||
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
        '';
      if (meta) return meta;
      var match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);
      if (!match) return '';
      try {
        return decodeURIComponent(match[1]);
      } catch (err) {
        return match[1];
      }
    }

    function toParams(form) {
      var fd = new FormData(form);
      var params = new URLSearchParams();
      fd.forEach(function (value, key) {
        if (value == null) return;
        params.append(key, String(value));
      });
      return params;
    }

    function ensureVariantAndQty(params, btn, form) {
      var variant =
        params.get('variantId') ||
        params.get('VariantId') ||
        params.get('id') ||
        btn?.getAttribute('data-variant-id') ||
        '';
      if (!variant && form) {
        var variantInput = form.querySelector('[name="variantId"]');
        if (variantInput && variantInput.value) variant = variantInput.value;
      }
      if (variant) {
        params.set('variantId', String(variant));
        if (!params.get('VariantId') && !params.get('id')) {
          params.set('VariantId', String(variant));
        }
      }
      if (!params.get('quantity')) params.set('quantity', '1');
      return params;
    }

    function setLoading(btn, on) {
      if (!btn) return;
      btn.dataset.loading = on ? '1' : '0';
      btn.disabled = !!on;
      btn.style.opacity = on ? '0.75' : '';
      btn.style.pointerEvents = on ? 'none' : '';
      var loadingIcon = btn.querySelector('.loading-icon');
      if (loadingIcon) loadingIcon.classList.toggle('hidden', !on);
    }

    function cardAddingHost(btn) {
      var card = btn && btn.closest('card-product');
      if (card) return card.querySelector('.card-product__top') || card;
      var qv = btn && btn.closest('.ww-qv-shell, #quick-view-product');
      if (qv) return qv.querySelector('.ww-qv-media, .product-gallery, .card-product__top') || null;
      return null;
    }

    function ensureCardOverlay(host) {
      if (!host) return null;
      var overlay = host.querySelector(':scope > .ww-card-adding-overlay');
      if (overlay) return overlay;
      overlay = document.createElement('div');
      overlay.className = 'ww-card-adding-overlay';
      overlay.setAttribute('aria-hidden', 'true');
      overlay.innerHTML =
        '<span class="ww-card-adding-spinner"></span>' +
        '<span class="ww-card-adding-check">' +
        '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5l5 5 9-10" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
        '</span>' +
        '<span class="ww-card-adding-text">Đang thêm...</span>';
      if (getComputedStyle(host).position === 'static') host.style.position = 'relative';
      host.appendChild(overlay);
      return overlay;
    }

    function setCardAdding(btn, state) {
      var host = cardAddingHost(btn);
      if (!host) return;
      clearTimeout(host._wwAddTimer);
      if (state === 'off') {
        host.classList.remove('ww-card-adding', 'ww-card-adding--done');
        return;
      }
      ensureCardOverlay(host);
      var text = host.querySelector('.ww-card-adding-text');
      host.classList.add('ww-card-adding');
      if (state === 'done') {
        host.classList.add('ww-card-adding--done');
        if (text) text.textContent = 'Đã thêm';
        host._wwAddTimer = setTimeout(function () {
          host.classList.remove('ww-card-adding', 'ww-card-adding--done');
        }, 700);
        return;
      }
      host.classList.remove('ww-card-adding--done');
      if (text) text.textContent = 'Đang thêm...';
    }

    function runAddToCart(btn, form, buynow) {
      setLoading(btn, true);
      setCardAdding(btn, 'loading');
      addToCartFromForm(form, btn, buynow)
        .then(function (data) {
          setCardAdding(btn, data ? 'done' : 'off');
        })
        .catch(function (err) {
          console.error(err);
          setCardAdding(btn, 'off');
          showCartError((err && err.message) || 'Không thêm được vào giỏ hàng');
        })
        .finally(function () {
          setLoading(btn, false);
        });
    }

    function publishCartAdded(response, buynow) {
      if (!response) return;
      if (response.item_count != null) {
        if (typeof window.__wwSyncCartBadge === 'function') {
          window.__wwSyncCartBadge(response.item_count);
        } else {
          document.querySelectorAll('.cart-count').forEach(function (el) {
            el.textContent = String(response.item_count);
          });
        }
      }
      if (!window.EGATheme || !window.EGATheme.publish || !window.themeConfigs) return;
      try {
        var action = buynow ? 'buynow' : window.themeConfigs.addToCartAction || 'drawer';
        window.EGATheme.publish(window.themeConfigs.productAddEvent, {
          data: response,
          action: action,
        });
      } catch (err) {
        console.warn('Cart UI update skipped:', err);
      }
    }

    function showCartError(message) {
      if (window.EGATheme && window.EGATheme.publish && window.themeConfigs && window.themeConfigs.error) {
        try {
          window.EGATheme.publish(window.themeConfigs.error, {
            message: message,
            error: new Error(message),
          });
          return;
        } catch (err) {}
      }
      if (message) window.alert(message);
    }

    var addQueue = Promise.resolve();

    async function postCartAdd(params, retried) {
      var controller = new AbortController();
      var timer = setTimeout(function () {
        controller.abort();
      }, 15000);
      try {
        var res = await fetch(window.themeUrl('/cart/add'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
          },
          body: params.toString(),
          credentials: 'same-origin',
          signal: controller.signal,
        });
        var t = await res.text();
        var payload = {};
        try {
          payload = t ? JSON.parse(t) : {};
        } catch (parseErr) {
          payload = {};
        }
        if (res.status === 419 && !retried) {
          return postCartAdd(params, true);
        }
        if (payload && payload.login_required && payload.login_url) {
          window.location.href = payload.login_url;
          return null;
        }
        if (!res.ok) {
          throw new Error(payload.message || 'Không thêm được vào giỏ hàng');
        }
        return payload;
      } catch (err) {
        if (err && err.name === 'AbortError') {
          throw new Error('Kết nối chậm, thử lại giúp mình.');
        }
        throw err;
      } finally {
        clearTimeout(timer);
      }
    }

    function addToCartFromForm(form, btn, buynow) {
      var params = form ? toParams(form) : new URLSearchParams();
      params = ensureVariantAndQty(params, btn, form);
      if (!params.get('variantId') && !params.get('VariantId') && !params.get('id')) {
        return Promise.reject(new Error('Thiếu mã biến thể sản phẩm'));
      }
      addQueue = addQueue.catch(function () {}).then(function () {
        return postCartAdd(params, false);
      });
      return addQueue.then(function (data) {
        publishCartAdded(data, buynow);
        return data;
      });
    }

    function isLoggedIn() {
      if (window.wwAuth && window.wwAuth.user) return true;
      var box = document.querySelector('[data-ww-account-user]');
      return !!(box && !box.hidden);
    }

    function requireLogin() {
      if (isLoggedIn()) return false;
      window.location.href = @json(storefrontLoginUrl(url()->current()));
      return true;
    }

    function findCartButton(e) {
      return (
        e.target.closest('.add_to_cart') ||
        e.target.closest('.btn-addtocart-combo') ||
        e.target.closest('#add-to-cart-form button[name="addtocart"]') ||
        e.target.closest('#add-to-cart-form button[name="buynow"]')
      );
    }

    function markHandled(e) {
      e.wwCartHandled = true;
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    }

    document.addEventListener(
      'click',
      function (e) {
        var btn = findCartButton(e);
        if (!btn) return;
        if (btn.disabled || btn.getAttribute('aria-disabled') === 'true') return;
        if (requireLogin()) {
          markHandled(e);
          return;
        }
        if (btn.classList.contains('btn-addtocart-combo')) return;
        markHandled(e);
        if (btn.dataset.loading === '1') return;

        var inQuickView = !!btn.closest('#quick-view-product, .ww-qv-shell');
        var card = btn.closest('card-product');
        var requiresVariant =
          btn.getAttribute('data-requires-variant') === '1' ||
          (card && card.getAttribute('data-requires-variant') === '1');
        if (card && !inQuickView && btn.id !== 'ww-qv-addtocart' && requiresVariant) {
          var productId =
            parseInt(card.getAttribute('data-product-id') || card.dataset.productId || '0', 10) ||
            parseInt(btn.getAttribute('data-product-id') || '0', 10) ||
            parseInt(btn.getAttribute('data-variant-id') || '0', 10) ||
            0;
          if (!productId) {
            var qForm = btn.closest('form');
            var input = qForm && qForm.querySelector('[name="variantId"]');
            if (input && input.value) productId = parseInt(input.value, 10) || 0;
          }
          if (productId && typeof window.wwOpenQuickViewForCart === 'function') {
            window.wwOpenQuickViewForCart(productId);
          } else if (productId && typeof window.wwOpenQuickView === 'function') {
            window.wwOpenQuickView(productId, { promptVariant: true, addIfNoVariant: true });
          }
          return;
        }

        if (typeof window.__wwBeforeAddToCart === 'function') {
          try {
            if (window.__wwBeforeAddToCart(btn, btn.closest('form')) === false) {
              return;
            }
          } catch (guardErr) {
            console.warn(guardErr);
          }
        }

        var form = btn.closest('form');
        var buynow = btn.getAttribute('name') === 'buynow';
        runAddToCart(btn, form, buynow);
      },
      true
    );

    document.addEventListener(
      'submit',
      function (e) {
        var form = e.target;
        if (!form || form.id !== 'add-to-cart-form') return;
        markHandled(e);
        if (requireLogin()) return;

        var submitter = e.submitter;
        var btn =
          submitter ||
          form.querySelector('[name="addtocart"]') ||
          form.querySelector('[name="buynow"]');
        if (!btn || btn.dataset.loading === '1') return;

        var buynow = submitter && submitter.getAttribute('name') === 'buynow';
        runAddToCart(btn, form, buynow);
      },
      true
    );
  })();
</script>
<script src="100/531/894/themes/1018832/assets/cart.js?ww-cart-fast-2" defer fetchpriority="low"></script>
