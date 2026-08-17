(function () {
  if (window.__wwQuickViewEnhanceLoaded) return;
  window.__wwQuickViewEnhanceLoaded = true;

  function themeApiUrl(path) {
    return typeof window.themeUrl === "function"
      ? window.themeUrl(path)
      : path.startsWith("/")
        ? path
        : "/" + path;
  }

  function escapeHtml(value) {
    return String(value == null ? "" : value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function productIdFromCard(card) {
    if (!card) return 0;
    if (card.dataset && card.dataset.productId) {
      return parseInt(card.dataset.productId, 10) || 0;
    }
    const btn = card.querySelector("[data-product-id], .ww-quick-view-btn[data-product-id]");
    if (btn && btn.dataset && btn.dataset.productId) {
      return parseInt(btn.dataset.productId, 10) || 0;
    }
    const cartBtn = card.querySelector(".add_to_cart[data-variant-id], .addtocart-btn[data-variant-id]");
    if (cartBtn && cartBtn.dataset && cartBtn.dataset.variantId) {
      return parseInt(cartBtn.dataset.variantId, 10) || 0;
    }
    const input = card.querySelector('input[name="variantId"]');
    if (input && input.value) return parseInt(input.value, 10) || 0;
    const form = card.querySelector("form[data-id]");
    const match = form && String(form.dataset.id || "").match(/(\d+)$/);
    return match ? parseInt(match[1], 10) || 0 : 0;
  }

  function isCartActionTarget(target) {
    return !!(
      target &&
      target.closest &&
      target.closest(
        ".addtocart-btn, .add_to_cart, .card-product__cart-btn, button[name='addtocart'], button[name='buynow'], quantity-input, .custom-number-input, .ww-search-price-clear, .ww-search-price-chip"
      )
    );
  }

  function removeQuickViewEyeButtons(root) {
    (root || document).querySelectorAll(".ww-quick-view-btn").forEach(function (btn) {
      btn.remove();
    });
  }

  function ensureModalInBody() {
    const modal = document.getElementById("quick-view-product");
    if (modal && modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
    return modal;
  }

  function hasActivePortal() {
    return !!document.querySelector(
      ".portal.active, quick-view.active, quick-view.ww-open, dialog.portal-dialog[open]"
    );
  }

  function forceCloseQuickViewDialog(modal) {
    if (!modal) return;
    const dialog = modal.querySelector("dialog");
    modal.classList.remove("active", "ww-open");
    if (!dialog) return;
    try {
      if (dialog.open && typeof dialog.close === "function") {
        dialog.close();
      }
    } catch (e) {
      /* ignore */
    }
    dialog.removeAttribute("open");
  }

  function unlockPageInteraction(force) {
    if (!force && hasActivePortal()) return;
    document.body.classList.remove("overflow-hidden");
    document.documentElement.classList.remove("overflow-hidden");
    document.body.style.removeProperty("overflow");
    document.documentElement.style.removeProperty("overflow");
  }

  window.__wwUnlockPageIfIdle = function () {
    // Bỏ qua portal đang đóng (không còn .active) nhưng dialog còn sót [open]
    document.querySelectorAll("quick-view").forEach(function (el) {
      if (!el.classList.contains("active") && !el.classList.contains("ww-open")) {
        forceCloseQuickViewDialog(el);
      }
    });
    document.querySelectorAll(".portal:not(.active) > dialog[open], .portal:not(.active) dialog.portal-dialog[open]").forEach(function (dialog) {
      try {
        if (dialog.open && typeof dialog.close === "function") dialog.close();
      } catch (e) {
        /* ignore */
      }
      dialog.removeAttribute("open");
    });
    unlockPageInteraction(false);
  };

  function releasePageInteraction() {
    const modal = document.getElementById("quick-view-product");
    forceCloseQuickViewDialog(modal);
    const otherBlocker = document.querySelector(
      ".portal.active:not(#quick-view-product), quick-view.active:not(#quick-view-product), quick-view.ww-open:not(#quick-view-product)"
    );
    if (!otherBlocker) {
      unlockPageInteraction(true);
    }
  }

  function resetQuickViewAnimation(modal) {
    if (!modal) return;
    modal.querySelectorAll(".animation").forEach(function (el) {
      el.classList.remove("scale-in-hor-left", "slide-in-bottom", "slide-in-left", "animating");
      el.style.transform = "";
      el.style.animation = "";
    });
  }

  function openModal() {
    const modal = ensureModalInBody();
    if (!modal) return;
    const dialog = modal.querySelector("dialog");
    const isMobile =
      (window.themeConfigs &&
        window.themeConfigs.mbBreakpoint &&
        window.themeConfigs.mbBreakpoint.matches) ||
      window.matchMedia("(max-width: 767px)").matches;

    resetQuickViewAnimation(modal);
    if (isMobile) {
      modal.dataset.animation = "slide-in-bottom";
    }

    modal.classList.add("active", "ww-open");
    document.body.classList.add("overflow-hidden");
    document.documentElement.classList.add("overflow-hidden");
    if (!dialog) return;
    dialog.setAttribute("open", "");
  }

  function closeModal() {
    const modal = document.getElementById("quick-view-product");
    if (!modal) return;
    modal.classList.remove("active", "ww-open");
    releasePageInteraction();
    if (document.activeElement && modal.contains(document.activeElement)) {
      document.activeElement.blur();
    }
  }

  function bindQuickViewPortalHooks() {
    const modal = document.getElementById("quick-view-product");
    if (!modal) return;

    // Re-bind sau khi <quick-view> được upgrade (product.js defer)
    const canWrapHide = typeof modal.hide === "function";
    if (modal.dataset.wwQvHideWrapped === "1" && canWrapHide) return;

    if (!modal.dataset.wwQvHooks) {
      modal.dataset.wwQvHooks = "1";
      modal.addEventListener("keyup", function (event) {
        if (event.code && event.code.toUpperCase() === "ESCAPE") {
          closeModal();
        }
      });
    }

    if (canWrapHide && modal.dataset.wwQvHideWrapped !== "1") {
      const nativeHide = modal.hide.bind(modal);
      modal.hide = function () {
        releasePageInteraction();
        modal.classList.remove("active", "ww-open");
        const result = nativeHide();
        window.setTimeout(function () {
          releasePageInteraction();
          if (typeof window.__wwUnlockPageIfIdle === "function") {
            window.__wwUnlockPageIfIdle();
          }
        }, (window.themeConfigs && window.themeConfigs.defaultTransitionTime) || 400);
        return result;
      };
      modal.dataset.wwQvHideWrapped = "1";
    }
  }

  function destroyQuickViewGallery(gallery) {
    if (!gallery) return;
    if (typeof gallery.destroyGallery === "function") {
      gallery.destroyGallery();
    }
    if (gallery._wwQvTeardown) {
      gallery._wwQvTeardown();
      gallery._wwQvTeardown = null;
    }
  }

  function initQuickViewGalleryEmbla(gallery) {
    if (!gallery || typeof EmblaCarousel !== "function") return false;

    const mainGalleryEl = gallery.querySelector('[id^="GalleryMain"]');
    if (!mainGalleryEl) return false;

    const mainViewport = mainGalleryEl.querySelector(".embla__viewport");
    const mainSlides = mainGalleryEl.querySelectorAll(".embla__viewport > .embla__container > .embla__slide");
    if (!mainViewport || !mainSlides.length) return false;

    destroyQuickViewGallery(gallery);

    const emblaMain = EmblaCarousel(mainViewport, {});
    gallery._wwQvEmblaMain = emblaMain;
    let emblaThumb = null;

    const thumbsEl = gallery.querySelector('[id^="GalleryThumbnails"]');
    if (thumbsEl && mainSlides.length > 1) {
      const thumbViewport = thumbsEl.querySelector(".embla__viewport");
      if (thumbViewport) {
        emblaThumb = EmblaCarousel(thumbViewport, {
          containScroll: "keepSnaps",
          dragFree: true,
        });
        gallery._wwQvEmblaThumb = emblaThumb;
        const thumbSlides = emblaThumb.slideNodes();
        thumbSlides.forEach(function (slide, index) {
          slide.addEventListener(
            "click",
            function () {
              emblaMain.scrollTo(index);
            },
            false
          );
        });

        const syncThumbs = function () {
          const selected = emblaMain.selectedScrollSnap();
          const idx = Math.min(Math.max(0, selected), thumbSlides.length - 1);
          emblaThumb.scrollTo(idx);
          thumbSlides.forEach(function (slide, i) {
            slide.classList.toggle("embla-thumbs__slide--selected", i === idx);
          });
        };

        emblaMain.on("select", syncThumbs);
        emblaThumb.on("init", syncThumbs);
        emblaThumb.on("reInit", syncThumbs);
        syncThumbs();
      }
    }

    const prevBtn = mainGalleryEl.querySelector(".embla__button--prev");
    const nextBtn = mainGalleryEl.querySelector(".embla__button--next");
    const onPrev = function () {
      emblaMain.scrollPrev();
    };
    const onNext = function () {
      emblaMain.scrollNext();
    };
    if (prevBtn) prevBtn.addEventListener("click", onPrev, false);
    if (nextBtn) nextBtn.addEventListener("click", onNext, false);

    const buttonsWrap = mainGalleryEl.querySelector(".embla__buttons");
    if (buttonsWrap && mainSlides.length <= 1) {
      buttonsWrap.style.display = "none";
    }

    gallery._wwQvTeardown = function () {
      if (prevBtn) prevBtn.removeEventListener("click", onPrev, false);
      if (nextBtn) nextBtn.removeEventListener("click", onNext, false);
      gallery._wwQvEmblaMain = null;
      gallery._wwQvEmblaThumb = null;
      emblaMain.destroy();
      if (emblaThumb) emblaThumb.destroy();
    };

    return true;
  }

  function refreshGalleryElements(gallery) {
    if (!gallery) return;
    gallery.elements = {
      thumbnails: gallery.querySelector('[id^="GalleryThumbnails"]'),
      mainGallery: gallery.querySelector('[id^="GalleryMain"]'),
    };
    gallery.prevBtnNode = gallery.querySelector(".embla__button--prev");
    gallery.nextBtnNode = gallery.querySelector(".embla__button--next");
  }

  function initQuickViewGallery(root) {
    const gallery = (root || document).querySelector("#quick-view-product media-gallery");
    if (!gallery) return;

    function run() {
      destroyQuickViewGallery(gallery);
      refreshGalleryElements(gallery);
      if (gallery.elements && gallery.elements.mainGallery && typeof gallery.init === "function") {
        gallery.init();
        if (typeof gallery.bindSpotlightClicks === "function") {
          gallery.bindSpotlightClicks();
        }
        if (gallery.mainGallery) return true;
      }
      return initQuickViewGalleryEmbla(gallery);
    }

    function scheduleInit() {
      if (run()) return;
      let tries = 0;
      const timer = window.setInterval(function () {
        tries += 1;
        if (run() || tries > 40) window.clearInterval(timer);
      }, 100);
    }

    if (typeof window.requestAnimationFrame === "function") {
      window.requestAnimationFrame(scheduleInit);
    } else {
      scheduleInit();
    }
  }

  function quickViewSkeletonHtml() {
    return (
      '<div class="ww-qv-skeleton" aria-hidden="true">' +
      '<div class="ww-qv-skeleton__grid">' +
      '<div class="ww-qv-skeleton__gallery">' +
      '<div class="ww-qv-skeleton__img"></div>' +
      '<div class="ww-qv-skeleton__thumbs">' +
      '<span></span><span></span><span></span><span></span>' +
      "</div>" +
      "</div>" +
      '<div class="ww-qv-skeleton__info">' +
      '<div class="ww-qv-skeleton__line ww-qv-skeleton__line--title"></div>' +
      '<div class="ww-qv-skeleton__line ww-qv-skeleton__line--price"></div>' +
      '<div class="ww-qv-skeleton__box"></div>' +
      '<div class="ww-qv-skeleton__line ww-qv-skeleton__line--short"></div>' +
      '<div class="ww-qv-skeleton__btn"></div>' +
      '<div class="ww-qv-skeleton__btn ww-qv-skeleton__btn--outline"></div>' +
      "</div>" +
      "</div>" +
      "</div>"
    );
  }

  function setQuickViewLoading(loading) {
    const modal = document.getElementById("quick-view-product");
    if (!modal) return;
    const inner = modal.querySelector(".portal-inner");
    const wrapper = modal.querySelector(".product-wrapper");
    if (inner) inner.classList.toggle("loading", loading);
    if (wrapper) wrapper.classList.toggle("is-loading", loading);
  }

  function showQuickViewSkeleton(wrapper) {
    setQuickViewLoading(true);
    wrapper.innerHTML = quickViewSkeletonHtml();
  }

  function injectQuickViewHtml(html, options) {
    options = options || {};
    const wrapper = document.querySelector("#quick-view-product .product-wrapper");
    if (!wrapper) return false;

    const doc = new DOMParser().parseFromString(html, "text/html");
    // Prefer full shell (scroll body + frozen "Xem chi tiết" footer)
    const shell = doc.querySelector(".ww-qv-shell");
    const productForm = doc.querySelector("product-form");
    const content = shell || productForm;
    if (!content) return false;

    wrapper.replaceChildren();
    wrapper.appendChild(document.importNode(content, true));
    setQuickViewLoading(false);
    wrapper.classList.remove("is-ready");
    void wrapper.offsetWidth;
    wrapper.classList.add("is-ready");
    window.setTimeout(function () {
      wrapper.classList.remove("is-ready");
    }, 320);
    initQuickViewGallery(wrapper);
    bindQuickViewVariants(wrapper);

    if (options.promptVariant) {
      window.setTimeout(function () {
        const liveShell = wrapper.querySelector(".ww-qv-shell") || wrapper;
        const list = liveShell.querySelector("#ww-qv-variants");
        if (list) {
          showVariantRequiredError(liveShell, list);
        } else if (options.addIfNoVariant) {
          const addBtn = liveShell.querySelector("#ww-qv-addtocart");
          if (addBtn && typeof addBtn.click === "function") {
            addBtn.click();
          }
        }
      }, 60);
    }

    if (window.EGATheme && window.EGATheme.publish && window.themeConfigs) {
      window.EGATheme.publish(window.themeConfigs.productLoaded);
      window.EGATheme.publish(window.themeConfigs.quickViewShow);
    }

    return true;
  }

  function formatQvPrice(n) {
    n = Math.round(Number(n) || 0);
    if (n <= 0) return "Liên hệ";
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function normalizeImageKey(url) {
    if (!url) return "";
    try {
      const u = new URL(url, window.location.origin);
      let path = (u.pathname || "").toLowerCase();
      path = path.replace(/\/\d+x\d+_([^/]+)$/i, "/$1");
      return path;
    } catch (e) {
      return String(url)
        .split("?")[0]
        .toLowerCase()
        .replace(/\/\d+x\d+_([^/]+)$/i, "/$1");
    }
  }

  function findGalleryImageIndex(galleryRoot, imageUrl, preferredIndex) {
    const slides = galleryRoot
      ? galleryRoot.querySelectorAll("#ww-qv-gallery-main .embla__slide")
      : [];
    if (!slides.length) return -1;

    const pref = parseInt(preferredIndex, 10);
    if (!isNaN(pref) && pref >= 0 && pref < slides.length) return pref;

    const key = normalizeImageKey(imageUrl);
    if (!key) return -1;

    for (let i = 0; i < slides.length; i++) {
      const slide = slides[i];
      const img = slide.querySelector("img");
      const candidates = [
        slide.getAttribute("data-display-src"),
        slide.getAttribute("data-original-src"),
        slide.getAttribute("data-src"),
        img && img.getAttribute("src"),
        img && img.currentSrc,
      ];
      for (let c = 0; c < candidates.length; c++) {
        const k = normalizeImageKey(candidates[c] || "");
        if (k && (k === key || k.indexOf(key) !== -1 || key.indexOf(k) !== -1)) {
          return i;
        }
      }
    }
    return -1;
  }

  function scrollQuickViewGalleryTo(index) {
    if (index < 0) return;
    const gallery = document.querySelector("#quick-view-product media-gallery");
    if (!gallery) return;

    if (gallery._wwQvEmblaMain && typeof gallery._wwQvEmblaMain.scrollTo === "function") {
      gallery._wwQvEmblaMain.scrollTo(index);
      return;
    }
    if (gallery.mainGallery && typeof gallery.mainGallery.scrollTo === "function") {
      gallery.mainGallery.scrollTo(index);
      return;
    }

    const slides = gallery.querySelectorAll("#ww-qv-gallery-main .embla__slide");
    const thumbs = gallery.querySelectorAll("#ww-qv-gallery-thumbs .embla__slide");
    slides.forEach(function (slide, i) {
      slide.style.display = i === index ? "" : "none";
    });
    thumbs.forEach(function (slide, i) {
      slide.classList.toggle("embla-thumbs__slide--selected", i === index);
    });
  }

  function setQuickViewAddCartEnabled(shell, enabled, hintText) {
    const addBtn = shell.querySelector("#ww-qv-addtocart");
    if (!addBtn) return;
    // Nút luôn bấm được; chỉ khóa khi biến thể đã chọn mà hết hàng
    const requires = addBtn.getAttribute("data-requires-variant") === "1";
    const lockSoldout = requires && enabled === false && hintText && /hết hàng/i.test(hintText);
    addBtn.disabled = !!lockSoldout;
    addBtn.setAttribute("aria-disabled", lockSoldout ? "true" : "false");
    addBtn.classList.toggle("opacity-60", !!lockSoldout);
    addBtn.classList.toggle("pointer-events-none", !!lockSoldout);
    const hint = addBtn.querySelector("span");
    if (hint) {
      hint.textContent = hintText || "Giao hàng tận nơi hoặc nhận tại cửa hàng";
    }
  }

  function variantErrorMessage(listRoot) {
    const group = (listRoot && listRoot.getAttribute("data-group-label")) || "Phân loại";
    if (!group || /^phân loại$/i.test(group.trim())) {
      return "Vui lòng chọn Phân loại hàng";
    }
    return "Vui lòng chọn " + group;
  }

  function showVariantRequiredError(shell, listRoot, message) {
    const box = listRoot || (shell && shell.querySelector("#ww-qv-variants"));
    if (!box) return;
    box.classList.add("is-error");
    const err = box.querySelector("#ww-qv-variant-error") || box.querySelector(".ww-qv-variant-error");
    if (err) {
      err.hidden = false;
      err.textContent = message || variantErrorMessage(box);
    }
    try {
      box.scrollIntoView({ behavior: "smooth", block: "nearest" });
    } catch (e) {
      /* ignore */
    }
  }

  function clearVariantRequiredError(shellOrList) {
    const box =
      shellOrList && shellOrList.id === "ww-qv-variants"
        ? shellOrList
        : shellOrList && shellOrList.querySelector
          ? shellOrList.querySelector("#ww-qv-variants")
          : document.querySelector("#ww-qv-variants");
    if (!box) return;
    box.classList.remove("is-error");
    const err = box.querySelector("#ww-qv-variant-error") || box.querySelector(".ww-qv-variant-error");
    if (err) err.hidden = true;
  }

  function quickViewHasSelectedVariant(shell) {
    if (!shell) return false;
    const input = shell.querySelector("#ww-qv-variant-id");
    const id = input && String(input.value || "").trim();
    if (!id) return false;
    const active = shell.querySelector("#ww-qv-variants .ww-qv-variant-btn.is-active");
    return !!active;
  }

  window.__wwBeforeAddToCart = function (btn, form) {
    if (!btn || btn.id !== "ww-qv-addtocart") return true;
    if (btn.getAttribute("data-requires-variant") !== "1") return true;
    const shell = btn.closest(".ww-qv-shell") || document.getElementById("quick-view-product");
    const list = shell && shell.querySelector("#ww-qv-variants");
    if (!quickViewHasSelectedVariant(shell)) {
      showVariantRequiredError(shell, list);
      return false;
    }
    const active = list && list.querySelector(".ww-qv-variant-btn.is-active");
    if (active && active.getAttribute("data-in-stock") !== "1") {
      showVariantRequiredError(shell, list, "Biến thể đã hết hàng");
      return false;
    }
    clearVariantRequiredError(list);
    return true;
  };

  function applyQuickViewVariant(btn, root) {
    if (!btn) return;
    const shell = root && root.closest ? root.closest(".ww-qv-shell") || root : document;
    const listRoot = shell.querySelector("#ww-qv-variants") || root;
    const alreadyActive = btn.classList.contains("is-active");

    // Click lại biến thể đang chọn → bỏ chọn (giống Shopee)
    if (alreadyActive && listRoot && listRoot.getAttribute("data-require-select") === "1") {
      listRoot.querySelectorAll(".ww-qv-variant-btn").forEach(function (el) {
        el.classList.remove("is-active");
        el.setAttribute("aria-selected", "false");
      });
      const label = shell.querySelector("#ww-qv-variant-label");
      if (label) {
        label.textContent = "Vui lòng chọn";
        label.classList.add("text-neutral-300");
        label.classList.remove("text-foreground");
      }
      const minPrice = parseInt(listRoot.getAttribute("data-min-price") || "0", 10) || 0;
      const minCompare = parseInt(listRoot.getAttribute("data-min-compare") || "0", 10) || 0;
      const priceEl = shell.querySelector("#ww-qv-price");
      if (priceEl) {
        priceEl.innerHTML =
          minPrice > 0 ? formatQvPrice(minPrice) + '<span class="ww-vnd">&#8363;</span>' : "Liên hệ";
      }
      const compareEl = shell.querySelector("#ww-qv-compare");
      const discountEl = shell.querySelector("#ww-qv-discount");
      const showCompare = minPrice > 0 && minCompare > minPrice;
      if (compareEl) {
        compareEl.classList.toggle("hidden", !showCompare);
        if (showCompare) {
          compareEl.innerHTML = formatQvPrice(minCompare) + '<span class="ww-vnd">&#8363;</span>';
        }
      }
      if (discountEl) {
        const pct = showCompare
          ? Math.min(99, Math.max(1, Math.round((1 - minPrice / minCompare) * 100)))
          : 0;
        discountEl.classList.toggle("hidden", pct <= 0);
        if (pct > 0) discountEl.textContent = "-" + pct + "%";
      }
      const skuEl = shell.querySelector("#ww-qv-sku");
      const productSku = listRoot.getAttribute("data-product-sku") || "";
      if (skuEl && productSku) skuEl.textContent = productSku;
      const stockEl = shell.querySelector("#ww-qv-stock");
      if (stockEl) {
        stockEl.textContent = "Còn hàng";
        stockEl.classList.add("text-success");
        stockEl.classList.remove("text-danger");
      }
      const variantInput = shell.querySelector("#ww-qv-variant-id");
      if (variantInput) variantInput.value = "";
      const titleInput = shell.querySelector("#ww-qv-variant-title");
      if (titleInput) titleInput.value = "";
      const priceInput = shell.querySelector("#ww-qv-price-input");
      if (priceInput) priceInput.value = String(minPrice || 0);
      const addBtn = shell.querySelector("#ww-qv-addtocart");
      if (addBtn) addBtn.dataset.variantId = "";
      const quantityInput = shell.querySelector('.ww-qv-cart-form [name="quantity"]');
      if (quantityInput) {
        quantityInput.max = "1";
        quantityInput.removeAttribute("data-stock");
        if (window.wwQuantityStockHint) window.wwQuantityStockHint.reset(quantityInput);
      }
      setQuickViewAddCartEnabled(shell, true, "Giao hàng tận nơi hoặc nhận tại cửa hàng");
      return;
    }

    clearVariantRequiredError(listRoot);

    const id = btn.getAttribute("data-variant-id") || "";
    const title = btn.getAttribute("data-title") || "";
    const price = parseInt(btn.getAttribute("data-price") || "0", 10) || 0;
    const compare = parseInt(btn.getAttribute("data-compare") || "0", 10) || 0;
    const inStock = btn.getAttribute("data-in-stock") === "1";
    const stock = Math.max(0, parseInt(btn.getAttribute("data-stock") || "0", 10) || 0);
    const image = btn.getAttribute("data-image") || "";
    const imageIndexAttr = btn.getAttribute("data-image-index");

    if (listRoot) {
      listRoot.querySelectorAll(".ww-qv-variant-btn").forEach(function (el) {
        el.classList.toggle("is-active", el === btn);
        el.setAttribute("aria-selected", el === btn ? "true" : "false");
      });
    }

    const label = shell.querySelector("#ww-qv-variant-label");
    if (label) {
      label.textContent = title;
      label.classList.remove("text-neutral-300");
      label.classList.add("text-foreground");
    }

    const priceEl = shell.querySelector("#ww-qv-price");
    if (priceEl) {
      priceEl.innerHTML =
        price > 0 ? formatQvPrice(price) + '<span class="ww-vnd">&#8363;</span>' : "Liên hệ";
    }

    const compareEl = shell.querySelector("#ww-qv-compare");
    const discountEl = shell.querySelector("#ww-qv-discount");
    const showCompare = price > 0 && compare > price;
    if (compareEl) {
      compareEl.classList.toggle("hidden", !showCompare);
      if (showCompare) {
        compareEl.innerHTML = formatQvPrice(compare) + '<span class="ww-vnd">&#8363;</span>';
      }
    }
    if (discountEl) {
      const pct = showCompare ? Math.min(99, Math.max(1, Math.round((1 - price / compare) * 100))) : 0;
      discountEl.classList.toggle("hidden", pct <= 0);
      if (pct > 0) discountEl.textContent = "-" + pct + "%";
    }

    const skuEl = shell.querySelector("#ww-qv-sku");
    if (skuEl && id) skuEl.textContent = id;

    const stockEl = shell.querySelector("#ww-qv-stock");
    if (stockEl) {
      stockEl.textContent = inStock ? "Còn hàng" : "Hết hàng";
      stockEl.classList.toggle("text-success", inStock);
      stockEl.classList.toggle("text-danger", !inStock);
    }

    const variantInput = shell.querySelector("#ww-qv-variant-id");
    if (variantInput) variantInput.value = id;
    const titleInput = shell.querySelector("#ww-qv-variant-title");
    if (titleInput) titleInput.value = title;
    const priceInput = shell.querySelector("#ww-qv-price-input");
    if (priceInput) priceInput.value = String(price);

    const addBtn = shell.querySelector("#ww-qv-addtocart");
    if (addBtn) addBtn.dataset.variantId = id;
    const quantityInput = shell.querySelector('.ww-qv-cart-form [name="quantity"]');
    if (quantityInput) {
      quantityInput.max = String(stock > 0 ? stock : 1);
      quantityInput.setAttribute("data-stock", String(stock));
      if (stock > 0 && Number(quantityInput.value || 1) > stock) quantityInput.value = String(stock);
      if (window.wwQuantityStockHint) window.wwQuantityStockHint.reset(quantityInput);
    }
    setQuickViewAddCartEnabled(
      shell,
      inStock,
      inStock ? "Giao hàng tận nơi hoặc nhận tại cửa hàng" : "Biến thể đã hết hàng"
    );

    const galleryRoot = shell.querySelector("media-gallery") || shell;
    let idx = findGalleryImageIndex(galleryRoot, image, imageIndexAttr);
    if (idx < 0 && image) {
      const mainImg = shell.querySelector("#ww-qv-gallery-main .embla__slide img.gallery-main-img");
      if (mainImg) {
        mainImg.src = image;
        const slide = mainImg.closest(".embla__slide");
        if (slide) {
          slide.setAttribute("data-src", image);
          slide.setAttribute("data-original-src", image);
          slide.setAttribute("data-display-src", image);
        }
        idx = 0;
      }
    }
    if (idx >= 0) {
      scrollQuickViewGalleryTo(idx);
    }

    if (window.EGATheme && window.EGATheme.publish && window.themeConfigs && window.themeConfigs.variantChanged) {
      window.EGATheme.publish(window.themeConfigs.variantChanged, {
        variantId: id,
        title: title,
        price: price,
        image: image,
        imageIndex: idx,
      });
    }
  }

  function bindQuickViewVariants(root) {
    const list = (root || document).querySelector("#ww-qv-variants");
    if (!list || list.dataset.bound === "1") return;
    list.dataset.bound = "1";
    list.addEventListener("click", function (e) {
      const btn = e.target && e.target.closest ? e.target.closest(".ww-qv-variant-btn") : null;
      if (!btn || !list.contains(btn)) return;
      e.preventDefault();
      applyQuickViewVariant(btn, list);
    });
  }

  let loadingId = 0;

  function loadProduct(id, options) {
    if (!id) return;
    loadingId = id;
    options = options || {};

    const wrapper = document.querySelector("#quick-view-product .product-wrapper");
    if (wrapper) {
      showQuickViewSkeleton(wrapper);
    }

    openModal();

    fetch(themeApiUrl("/san-pham/chi-tiet/sp-" + id + "?view=quickview"), {
      headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
      credentials: "same-origin",
    })
      .then(function (res) {
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.text();
      })
      .then(function (html) {
        if (loadingId !== id) return;
        if (!injectQuickViewHtml(html, options)) throw new Error("empty");
      })
      .catch(function () {
        if (loadingId !== id) return;
        setQuickViewLoading(false);
        if (wrapper) {
          wrapper.innerHTML =
            '<div class="ww-qv-error bg-background p-6 rounded-lg text-center min-h-[20rem] flex items-center justify-center">Không tải được nội dung xem nhanh.</div>';
        }
      });
  }

  window.wwOpenQuickView = loadProduct;
  window.wwOpenQuickViewForCart = function (id) {
    loadProduct(parseInt(id, 10) || 0, { promptVariant: true, addIfNoVariant: true });
  };
  window.wwQuickViewClick = function (e, btn) {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    }
    const id = btn && (btn.dataset ? btn.dataset.productId : btn.getAttribute("data-product-id"));
    loadProduct(parseInt(id, 10) || 0);
    return false;
  };

  function openQuickViewFromCard(card) {
    const id = productIdFromCard(card);
    if (!id) return false;
    loadProduct(id);
    return true;
  }

  function ensureCardQuickView(root) {
    removeQuickViewEyeButtons(root);
    (root || document).querySelectorAll("card-product").forEach(function (card) {
      const pid = productIdFromCard(card);
      if (pid && (!card.dataset.productId || card.dataset.productId === "")) {
        card.dataset.productId = String(pid);
      }
      card.classList.add("ww-card-opens-qv");
    });
  }

  document.addEventListener(
    "click",
    function (e) {
      if (e.target.closest("#PortalClose-quick-view-product, #quick-view-product .portal-overlay")) {
        e.preventDefault();
        e.stopPropagation();
        closeModal();
        if (typeof window.__wwUnlockPageIfIdle === "function") {
          window.setTimeout(window.__wwUnlockPageIfIdle, 50);
        }
        return;
      }

      if (isCartActionTarget(e.target)) return;

      const card = e.target.closest("card-product");
      if (!card) return;

      // Click ảnh / chữ / vùng card → mở xem nhanh (không vào trang chi tiết)
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      openQuickViewFromCard(card);
    },
    true
  );

  function initQuickViewButtons() {
    ensureModalInBody();
    bindQuickViewPortalHooks();
    ensureCardQuickView(document);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initQuickViewButtons);
  } else {
    initQuickViewButtons();
  }

  // product.js defer: retry wrap hide sau khi custom element sẵn sàng
  window.setTimeout(bindQuickViewPortalHooks, 0);
  window.setTimeout(bindQuickViewPortalHooks, 500);
  window.setTimeout(bindQuickViewPortalHooks, 1500);

  document.addEventListener("PortalClosed", function () {
    window.setTimeout(function () {
      if (typeof window.__wwUnlockPageIfIdle === "function") {
        window.__wwUnlockPageIfIdle();
      }
    }, 30);
  });

  if (window.EGATheme && window.EGATheme.subscribe && window.themeConfigs) {
    window.EGATheme.subscribe(window.themeConfigs.productAddEvent, function () {
      window.setTimeout(function () {
        bindQuickViewPortalHooks();
        const qv = document.getElementById("quick-view-product");
        if (qv && (qv.classList.contains("active") || qv.classList.contains("ww-open"))) {
          if (typeof qv.hide === "function") qv.hide();
          else closeModal();
        } else {
          releasePageInteraction();
        }
        window.setTimeout(function () {
          if (typeof window.__wwUnlockPageIfIdle === "function") {
            window.__wwUnlockPageIfIdle();
          }
        }, (window.themeConfigs && window.themeConfigs.defaultTransitionTime) || 400);
      }, 0);
    });
  }

  document.addEventListener("home-product-cards-loaded", initQuickViewButtons);
  if (window.EGATheme && window.EGATheme.subscribe && window.themeConfigs) {
    window.EGATheme.subscribe(window.themeConfigs.productLoaded, initQuickViewButtons);
  }

  new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType === 1) ensureCardQuickView(node);
      });
    });
  }).observe(document.documentElement, { childList: true, subtree: true });
})();
