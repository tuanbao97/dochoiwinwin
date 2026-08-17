<script>
  (function () {
    function csrfToken() {
      return (
        (typeof window.__csrfToken === 'function' && window.__csrfToken()) ||
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
        ''
      );
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
      btn.style.opacity = on ? '0.75' : '';
      btn.style.pointerEvents = on ? 'none' : '';
      var loadingIcon = btn.querySelector('.loading-icon');
      if (loadingIcon) loadingIcon.classList.toggle('hidden', !on);
    }

    function publishCartAdded(response, buynow) {
      if (response && response.item_count != null) {
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

    async function addToCartFromForm(form, btn, buynow) {
      var params = form ? toParams(form) : new URLSearchParams();
      params = ensureVariantAndQty(params, btn, form);
      if (!params.get('variantId') && !params.get('VariantId') && !params.get('id')) {
        throw new Error('Thiếu mã biến thể sản phẩm');
      }
      var res = await fetch(window.themeUrl('/cart/add'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken(),
        },
        body: params.toString(),
        credentials: 'same-origin',
      });
      if (!res.ok) {
        var t = await res.text();
        throw new Error('Add to cart thất bại (HTTP ' + res.status + '): ' + t.slice(0, 120));
      }
      var data = await res.json();
      publishCartAdded(data, buynow);
      return data;
    }

    function findCartButton(e) {
      return (
        e.target.closest('.add_to_cart') ||
        e.target.closest('#add-to-cart-form button[name="addtocart"]') ||
        e.target.closest('#add-to-cart-form button[name="buynow"]')
      );
    }

    document.addEventListener(
      'click',
      function (e) {
        var btn = findCartButton(e);
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        if (btn.dataset.loading === '1') return;

        // Nút giỏ trên danh sách/card: có nhiều biến thể → mở QV; 1 biến thể → add thẳng
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
            var form = btn.closest('form');
            var input = form && form.querySelector('[name="variantId"]');
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
        setLoading(btn, true);
        addToCartFromForm(form, btn, buynow)
          .catch(function (err) {
            console.error(err);
            if (window.EGATheme && window.EGATheme.publish && window.themeConfigs && window.themeConfigs.error) {
              try {
                window.EGATheme.publish(window.themeConfigs.error, {
                  message: (err && err.message) || 'Không thêm được vào giỏ hàng',
                });
              } catch (pubErr) {}
            }
          })
          .finally(function () {
            setLoading(btn, false);
          });
      },
      true
    );

    document.addEventListener(
      'submit',
      function (e) {
        var form = e.target;
        if (!form || form.id !== 'add-to-cart-form') return;
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();

        var submitter = e.submitter;
        var btn =
          submitter ||
          form.querySelector('[name="addtocart"]') ||
          form.querySelector('[name="buynow"]');
        if (!btn || btn.dataset.loading === '1') return;

        var buynow = submitter && submitter.getAttribute('name') === 'buynow';
        setLoading(btn, true);
        addToCartFromForm(form, btn, buynow)
          .catch(function (err) {
            console.error(err);
          })
          .finally(function () {
            setLoading(btn, false);
          });
      },
      true
    );
  })();
</script>
<script src="100/531/894/themes/1018832/assets/cart.js?ww-stock-guard-1" defer fetchpriority="low"></script>
