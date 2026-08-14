function themeApiUrl(path) {
  return typeof window.themeUrl === "function"
    ? window.themeUrl(path)
    : path.startsWith("/")
      ? path
      : "/" + path;
}

function syncCartBadge(count) {
  const ensureNumEl = (badge) => {
    let numEl = badge.querySelector(".cart-count__num");
    if (!numEl) {
      numEl = document.createElement("span");
      numEl.className = "cart-count__num";
      numEl.textContent = String(badge.textContent || "0").trim() || "0";
      badge.textContent = "";
      badge.appendChild(numEl);
    }
    return numEl;
  };

  const applyCount = (qty) => {
    const next = String(qty);
    document.querySelectorAll(".cart-count").forEach((el) => {
      const numEl = ensureNumEl(el);
      const prev = parseInt(String(numEl.textContent || "0").replace(/\D/g, ""), 10) || 0;
      const nextNum = parseInt(String(next).replace(/\D/g, ""), 10) || 0;
      numEl.textContent = next;
      if (nextNum > prev) {
        el.classList.remove("cart-count--bump");
        void el.offsetWidth;
        el.classList.add("cart-count--bump");
        const onEnd = () => {
          el.classList.remove("cart-count--bump");
          numEl.removeEventListener("animationend", onEnd);
        };
        numEl.addEventListener("animationend", onEnd);
      }
    });
  };

  if (count != null) {
    applyCount(count);
    return;
  }

  fetch(themeApiUrl("/cart?view=data"), { credentials: "same-origin" })
    .then((response) => response.text())
    .then((html) => {
      const doc = new DOMParser().parseFromString(html, "text/html");
      const badge = doc.querySelector(".cart-count");
      if (!badge) return;
      applyCount(badge.textContent.trim());
    })
    .catch(() => {});
}

window.__wwSyncCartBadge = syncCartBadge;

/** Prev/next luôn hiện; đầu/cuối disable — giống thumbnail gallery / SP liên quan */
window.wwBindThumbLikeCarouselNav = function (carousel) {
  if (!carousel || !carousel.emblaApi) return;
  var emblaApi = carousel.emblaApi;
  var prevBtn = carousel.querySelector(".embla__button--prev");
  var nextBtn = carousel.querySelector(".embla__button--next");
  if (!prevBtn || !nextBtn) return;

  var newPrev = prevBtn.cloneNode(true);
  var newNext = nextBtn.cloneNode(true);
  prevBtn.replaceWith(newPrev);
  nextBtn.replaceWith(newNext);
  prevBtn = newPrev;
  nextBtn = newNext;

  var wrap = prevBtn.closest(".embla__buttons");
  var forceVisible = function () {
    prevBtn.style.setProperty("display", "inline-flex", "important");
    nextBtn.style.setProperty("display", "inline-flex", "important");
    prevBtn.style.setProperty("opacity", prevBtn.disabled ? "0.35" : "1", "important");
    nextBtn.style.setProperty("opacity", nextBtn.disabled ? "0.35" : "1", "important");
    if (wrap) {
      wrap.style.setProperty("display", "block", "important");
      wrap.hidden = false;
      wrap.removeAttribute("hidden");
    }
  };

  var updateButtons = function () {
    if (emblaApi.canScrollPrev()) prevBtn.removeAttribute("disabled");
    else prevBtn.setAttribute("disabled", "disabled");
    if (emblaApi.canScrollNext()) nextBtn.removeAttribute("disabled");
    else nextBtn.setAttribute("disabled", "disabled");
    forceVisible();
  };
  var scrollPrev = function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (emblaApi.canScrollPrev()) emblaApi.scrollPrev();
  };
  var scrollNext = function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (emblaApi.canScrollNext()) emblaApi.scrollNext();
  };

  prevBtn.addEventListener("click", scrollPrev, false);
  nextBtn.addEventListener("click", scrollNext, false);
  emblaApi
    .on("init", updateButtons)
    .on("reInit", updateButtons)
    .on("select", updateButtons)
    .on("settle", updateButtons);
  if (typeof emblaApi.reInit === "function") emblaApi.reInit();
  updateButtons();
  requestAnimationFrame(function () {
    if (typeof emblaApi.reInit === "function") emblaApi.reInit();
    updateButtons();
  });
};

function wwEscapeHtml(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function wwFormatIntViDots(num) {
  const s = String(Math.floor(Math.abs(Number(num) || 0)));
  return s.length <= 3 ? s : s.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function wwFormatPriceShortVnd(amount) {
  const n = Math.round(Number(amount) || 0);
  if (!Number.isFinite(n) || n <= 0) return "Liên hệ";
  if (n < 1000) return String(n);
  return wwFormatIntViDots(n) + " ₫";
}

function wwRelativeImagePathFromImg(img) {
  if (!img) return "";
  if (img.PATH) return String(img.PATH).replace(/\\/g, "/");
  if (!img.DIRECTORY) return "";
  const fname = img.IMAGE_THUMNAIL || img.NAME || "";
  if (!fname) return "";
  const ar = img.ASPECT_RATIO || "1x1";
  return String(img.DIRECTORY).replace(/^\/+|\/+$/g, "") + "/" + ar + "_" + fname;
}

function wwAssetUrl(pathRel) {
  if (!pathRel) return "";
  if (/^https?:\/\//i.test(pathRel) || String(pathRel).startsWith("//")) return pathRel;
  return themeApiUrl("/" + String(pathRel).replace(/^\/+/, ""));
}

function wwProductImageUrl(product) {
  const list = product.DANH_SACH_HINH_ANH_DAI_DIEN || [];
  const rel = list.length ? wwRelativeImagePathFromImg(list[0]) : "";
  return rel ? wwAssetUrl(rel) : themeApiUrl("/image/UI-BACKEND/default-image.png");
}

function wwProductImageRel(product) {
  const list = product.DANH_SACH_HINH_ANH_DAI_DIEN || [];
  return list.length ? wwRelativeImagePathFromImg(list[0]) : "";
}

function wwProductDetailUrl(product) {
  const slug = String(product.TEN_SAN_PHAM_SLUG || "sp").trim() || "sp";
  return themeApiUrl("/san-pham/chi-tiet/" + slug + "-" + product.ID);
}

function wwProductListPriceInfo(product) {
  const list = product && product.DANH_SACH_BIEN_THE;
  let minPrice = 0;
  let minCompare = 0;
  if (Array.isArray(list)) {
    for (let i = 0; i < list.length; i++) {
      const v = list[i];
      if (!v) continue;
      const vp = Math.round(Number(v.GIA_BAN) || 0);
      if (vp <= 0) continue;
      if (minPrice <= 0 || vp < minPrice) {
        minPrice = vp;
        minCompare = Math.round(Number(v.GIA_GOC) || 0);
      }
    }
  }
  if (minPrice > 0) return { price: minPrice, compare: minCompare };
  return {
    price: Math.round(Number(product && product.GIA_CA) || 0),
    compare: Math.round(Number(product && product.GIA_GOC) || 0),
  };
}

function wwBuildCartRecommendationCard(product, slideClass) {
  const title = product.TEN_SAN_PHAM || "Sản phẩm";
  const href = wwProductDetailUrl(product);
  const imgUrl = wwProductImageUrl(product);
  const imgRel = wwProductImageRel(product);
  const priceInfo = wwProductListPriceInfo(product);
  const priceInt = priceInfo.price;
  const priceLabel = wwFormatPriceShortVnd(priceInt);
  const categoryId = product.DANH_MUC_SAN_PHAM && product.DANH_MUC_SAN_PHAM.ID
    ? product.DANH_MUC_SAN_PHAM.ID
    : "";
  const compareInt = priceInfo.compare;
  const showCompare = priceInt > 0 && compareInt > priceInt;
  const compareLabel = showCompare ? wwFormatPriceShortVnd(compareInt) : "";
  const frameSrc = themeApiUrl("/UI-FRONTEND/images/Khung vien xanh.png");
  const variants = Array.isArray(product.DANH_SACH_BIEN_THE) ? product.DANH_SACH_BIEN_THE : [];
  const requiresVariant = variants.length > 1 ? "1" : "0";
  const cartVariantId =
    (variants[0] && variants[0].ID) || product.ATTR1 || product.ID;

  const priceBlock =
    priceInt <= 0
      ? '<div class="flex flex-col gap-0.5"><span class="price text-h6 font-semibold leading-tight text-neutral-500">Liên hệ</span></div>'
      : '<div class="flex flex-col gap-1 min-w-0">' +
        '<span class="price text-h6 font-semibold leading-tight text-rose-600">' + wwEscapeHtml(priceLabel) + '</span>' +
        (showCompare
          ? '<span class="compare-price price--struck line-through text-sm font-medium text-neutral-400">' +
            wwEscapeHtml(compareLabel) +
            '</span>'
          : '') +
        '</div>';

  return (
    '<div class="' + wwEscapeHtml(slideClass || "") + '">' +
    '<card-product class="h-full card-product--vertical" data-product-id="' + wwEscapeHtml(product.ID) + '" data-requires-variant="' + requiresVariant + '">' +
    '<div class="item_product_main card-product relative transition-transform duration-200 ease-in-out h-full">' +
    '<form action="/cart/add" method="post" enctype="multipart/form-data" class="bg-background relative z-10 m-0 h-full" style="border: 1px solid rgba(2, 132, 199, 0.18);">' +
    '<input type="hidden" name="product_title" value="' + wwEscapeHtml(title) + '">' +
    '<input type="hidden" name="product_handle" value="' + wwEscapeHtml(product.TEN_SAN_PHAM_SLUG || "") + '">' +
    '<input type="hidden" name="price" value="' + wwEscapeHtml(priceInt) + '">' +
    '<input type="hidden" name="image" value="' + wwEscapeHtml(imgRel) + '">' +
    '<input type="hidden" name="category_id" value="' + wwEscapeHtml(categoryId) + '">' +
    '<div class="card-product__top relative overflow-hidden group/card p-2">' +
    '<a class="link aspect-square flex items-center justify-center w-full relative overflow-hidden" href="' + wwEscapeHtml(href) + '" title="' + wwEscapeHtml(title) + '">' +
    '<img class="product-frame w-full object-contain absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10" src="' + wwEscapeHtml(frameSrc) + '" alt="" loading="lazy" width="480" height="480">' +
    '<img class="card-product__image max-h-full w-auto object-contain scale-[var(--image-scale)] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition duration-300 ease-out" width="480" height="480" loading="lazy" style="--image-scale:0.9" src="' + wwEscapeHtml(imgUrl) + '" alt="' + wwEscapeHtml(title) + '">' +
    '</a></div>' +
    '<div class="card-product__body flex flex-col gap-2 px-2 pb-2 md:gap-1 md:px-2 md:pb-2">' +
    '<a class="link block" href="' + wwEscapeHtml(href) + '" title="' + wwEscapeHtml(title) + '">' +
    '<div class="card-product__title text-base line-clamp-3">' + wwEscapeHtml(title) + '</div>' +
    '</a>' +
    '<div class="card-product__price-row flex justify-between items-center gap-3 w-full min-w-0">' +
    '<a class="link flex-1 min-w-0" href="' + wwEscapeHtml(href) + '" title="' + wwEscapeHtml(title) + '">' +
    '<div class="price-box flex-1 min-w-0 flex flex-col items-start gap-1">' +
    priceBlock +
    '</div></a>' +
    '<div class="card-product__cart-btn shrink-0">' +
    '<input type="hidden" name="variantId" value="' + wwEscapeHtml(cartVariantId) + '">' +
    '<button type="button" class="btn bg-relative addtocart-btn font-semibold add_to_cart flex justify-center items-center gap-3" data-variant-id="' + wwEscapeHtml(cartVariantId) + '" data-requires-variant="' + requiresVariant + '" data-action="addtocart" aria-label="Thêm vào giỏ">' +
    '<span class="loading-icon gap-1 hidden items-center justify-center"><span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span><span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span><span class="w-1.5 h-1.5 bg-[currentColor] rounded-full animate-pulse"></span></span>' +
    '<span class="flex items-center justify-center"><i class="icon icon-cart text-[1.35rem]"></i></span>' +
    '</button></div></div></div></form></div></card-product></div>'
  );
}

function renderCartRecommendations(products) {
  document.querySelectorAll(".cart-releated-products").forEach((box) => {
    const list = box.querySelector(".product-list");
    if (!list) return;
    if (!products.length) {
      list.innerHTML = "";
      box.classList.add("hidden", "no-products");
      return;
    }

    const hasCarousel = !!box.querySelector("carousel-slider");
    list.innerHTML = products
      .map((product) => wwBuildCartRecommendationCard(product, hasCarousel ? "embla__slide h-inherit" : ""))
      .join("");
    box.classList.remove("hidden", "no-products", "no-products-content");

    const carousel = box.querySelector("carousel-slider");
    if (carousel && typeof carousel.init === "function") {
      carousel.init();
      if (typeof window.wwBindThumbLikeCarouselNav === "function") {
        window.wwBindThumbLikeCarouselNav(carousel);
      }
    }
  });

  if (window.EGATheme && window.EGATheme.publish && window.themeConfigs) {
    window.EGATheme.publish(window.themeConfigs.productLoaded);
  }
}

function loadCartRecommendations() {
  return fetch(themeApiUrl("/cart/recommendations?limit=10"), {
    credentials: "same-origin",
    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      renderCartRecommendations(data && data.products ? data.products : []);
    })
    .catch(() => renderCartRecommendations([]));
}

window.__wwLoadCartRecommendations = loadCartRecommendations;

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => syncCartBadge());
} else {
  syncCartBadge();
}

function isCartPathname() {
  return /\/cart$/.test(window.location.pathname);
}

class RewardsBar extends HTMLElement {
  constructor() {
    super();

    this.init();
  }

  init() {
    this.itemsWrapper = this.querySelector(".reward-items");
    if (!this.itemsWrapper) return;
    const items = Array.from(
      this.itemsWrapper.querySelectorAll(".rewards-item")
    );
    const totalCart = parseInt(this.dataset.total);
    const sortedElements = items.sort((a, b) => {
      const amountA = parseInt(a.dataset.amount);
      const amountB = parseInt(b.dataset.amount);
      return amountA - amountB;
    });
    let nextReward = null;
    let currentReward = null;
    let maxAmount = parseInt(sortedElements[items.length - 1].dataset.amount);
    sortedElements.forEach((el) => {
      const amount = parseInt(el.dataset.amount);
      if (!nextReward && amount > totalCart) {
        nextReward = el;
        let price = amount - totalCart;
        let message = el.querySelector(".reward-message").textContent;
        this.querySelector(".rewards-messages").textContent = message;
        el.querySelectorAll(".rewards-icon").forEach((icon) =>
          icon.classList.remove("hidden")
        );
        el.querySelector(".rewards-icon-success").classList.add("hidden");
      }
      if (amount <= totalCart) {
        currentReward = el;
        el.querySelectorAll(".rewards-icon").forEach((icon) =>
          icon.classList.add("hidden")
        );
        el.querySelector(".rewards-icon-success").classList.remove("hidden");
      }
      let position = (amount / maxAmount) * 100;
      el.style.left = position + "%";
      el.style.setProperty(
        "--tooltip-position",
        position > 50 ? "-100px" : "0"
      );
    });
    let percent = (totalCart / maxAmount) * 100;
    if (!nextReward) {
      this.querySelector(".rewards-messages").textContent =
        this.querySelector(".rewards-success").textContent;
      percent = 100;
    }
    if (currentReward) {
      let title = currentReward.querySelector(".reward-title").textContent;
      let coupon = currentReward.dataset.coupon;
      let copyButton = coupon
        ? `<copy-button data-copied-text="Đã sao chép"  >
                <input type="hidden" value="${coupon}" />
                <button type="button" class=" btn leading-0 inline-flex ml-1 px-1 text-neutral-400 copy-button p-0 border border-neutral-100 rounded-sm ">
  					copy <i class="icon icon-paste"></i>
      			</button>
				</copy-button>`
        : "";
      let couponHTML = `<div>Đã nhận : <span class="font-semibold">${title}</span>${copyButton}</div>`;
      this.querySelector(".rewards-coupon").innerHTML = couponHTML;
    } else {
      this.querySelector(".rewards-coupon").innerHTML = "";
    }
    percent = percent > 100 ? 100 : percent;
    this.querySelector(".rewards-percent").style.width = percent + "%";
    this.classList.remove("opacity-0");
  }
  update(cartRewards) {
    this.dataset.total = cartRewards.dataset.total;
    this.innerHTML = cartRewards.innerHTML;
    this.init();
  }
}

defineElement("rewards-bar", RewardsBar);
function debounce(fn, wait) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), wait);
  };
}

function __wwBootCartComponents(bootFn) {
  const run = () => {
    if (window.__wwCartComponentsBooted) return;
    window.__wwCartComponentsBooted = true;
    try {
      bootFn();
    } catch (err) {
      window.__wwCartComponentsBooted = false;
      console.error("Cart components boot failed:", err);
    }
  };
  if (typeof subscribe === "function" && window.themeConfigs && window.themeConfigs.firstInteraction) {
    subscribe(window.themeConfigs.firstInteraction, run);
  }
  // Boot ngay — không phụ thuộc CDN Bizweb (defer-vendors) để add-to-cart luôn hiện drawer/badge
  if (window.__wwFirstInteractionDone || document.readyState !== "loading") {
    run();
  } else {
    document.addEventListener("DOMContentLoaded", run, { once: true });
  }
}

__wwBootCartComponents(() => {
  class CartForm extends HTMLElement {
    constructor() {
      super();
      this.form = this.querySelector("form");

      const debouncedOnChange = debounce((event) => {
        this.onChange(event);
      }, 300);
      this.addEventListener("change", debouncedOnChange.bind(this));

      this.form.addEventListener("submit", this.onSubmit.bind(this));
    }

    onChange(e) {
      if (e.target.name === "Lines") {
        let input = e.target;
        let index = input.dataset.lineIndex;
        let quanity = input.value;
        this.quantityUpdate(index, quanity);
      }
      if (e.target.id == "vat") {
        let vatDrawer = document.querySelector("#cart-vat-drawer");
        if (e.target.checked) {
          vatDrawer.show();
        } else {
          vatDrawer.reset();
          vatDrawer.syncCartForm();
        }
      }
    }

    quantityUpdate(index, quanity) {
      let line = this.querySelector(`.cart-item[data-line-index="${index}"]`);
      this.classList.add("loading");
      line.classList.add("loading");
      fetch(themeApiUrl(`/cart/change?line=${index}&quantity=${quanity}`))
        .then((response) => {
          if (response.ok) {
            this.updateCart();
          } else {
            throw new Error("Đã có lỗi xảy ra");
          }
        })
        .catch((err) => {
          console.log(err);
          this.classList.remove("loading");
          line.classList.remove("loading");
        });
    }
    updateCartAttribute() {
      const data = serializeForm(this.form, true);
      fetch(themeApiUrl("/cart/update"), {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: data,
      })
        .then((res) => res.json())
        .then((response) => {
          this.updateCart();
        })
        .catch((err) => {
          console.log(err);
        });
    }
    updateCart() {
      this.classList.add("loading");
      fetch(themeApiUrl("/cart?view=data"))
        .then((response) => response.text())
        .then((res) => {
          this.renderCart(res);
          this.classList.remove("loading");
        })
        .catch((err) => {
          this.classList.remove("loading");
        });
    }
    renderCart(res) {
      let html = new DOMParser().parseFromString(res, "text/html");
      let replaceSelectors = [".cart-table", ".cart-empty", ".cart-summary"];
      let relatedProducts = html.querySelector(".cart-releated-products");
      let cartRewards = this.querySelector("rewards-bar");
      let cro = document.querySelector(".cro-btns");
      let isEmpty = html.querySelector(".is-empty");
      replaceSelectors.forEach((el) => {
        if (!this.querySelector(el)) return;
        const from = html.querySelector(el);
        if (from) this.querySelector(el).innerHTML = from.innerHTML;
      });

      const mcFrom = html.querySelector(".mini-cart");
      const mcTo = document.querySelector(".mini-cart");
      if (mcFrom && mcTo) mcTo.innerHTML = mcFrom.innerHTML;
      if (isEmpty) {
        this.classList.add("is-empty");
      } else {
        this.classList.remove("is-empty");
      }
      const ccFrom = html.querySelector(".cart-count");
      if (ccFrom) {
        syncCartBadge((ccFrom.textContent || "").trim());
      }
      if (relatedProducts) {
        document.querySelectorAll(".cart-releated-products").forEach((el) => {
          if (el.dataset.query != relatedProducts.dataset.query) {
            el.dataset.query = relatedProducts.dataset.query;
            el.getProducts();
          }
        });
      }
      if (typeof window.__wwLoadCartRecommendations === "function") {
        window.__wwLoadCartRecommendations();
      }
      if (cartRewards) {
        cartRewards.update(html.querySelector("rewards-bar"));
      }
      if (cro && cro.querySelector(".cart-bottom")) {
        isEmpty ? cro.classList.add("hidden") : cro.classList.remove("hidden");
        cro.querySelector(".cart-bottom").innerHTML =
          html.querySelector(".cart-bottom").innerHTML;
      }
      this.scrollCartListToTop();
    }
    scrollCartListToTop() {
      const scroller =
        this.querySelector(".cart-left") ||
        (this.closest("cart-drawer") &&
          this.closest("cart-drawer").querySelector(".cart-left")) ||
        document.querySelector("cart-drawer .cart-left");
      if (!scroller) return;
      try {
        scroller.scrollTo({ top: 0, behavior: "smooth" });
      } catch (e) {
        scroller.scrollTop = 0;
      }
    }
    onSubmit(e) {
      e.preventDefault();
      const formData = new FormData(this.form);

      const actionURL = this.form.getAttribute("action");

      // Gửi dữ liệu form bằng fetch API
      fetch(actionURL, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          window.location.href = themeApiUrl("/thanh-toan");
        })
        .catch((error) => {
          console.error("Lỗi:", error);
        });
    }
  }

  defineElement("cart-form", CartForm);

  function wwEnsureCartRemoveConfirm() {
    let el = document.getElementById("ww-cart-remove-confirm");
    if (el) return el;

    el = document.createElement("div");
    el.id = "ww-cart-remove-confirm";
    el.className = "ww-cart-remove-confirm";
    el.setAttribute("hidden", "");
    el.setAttribute("role", "dialog");
    el.setAttribute("aria-modal", "true");
    el.setAttribute("aria-labelledby", "ww-cart-remove-confirm-title");
    el.innerHTML = `
      <div class="ww-cart-remove-confirm__overlay" data-ww-confirm-dismiss></div>
      <div class="ww-cart-remove-confirm__panel" role="document">
        <div class="ww-cart-remove-confirm__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
            <path d="M10 11v6"></path>
            <path d="M14 11v6"></path>
            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
          </svg>
        </div>
        <h3 id="ww-cart-remove-confirm-title" class="ww-cart-remove-confirm__title">Xóa khỏi giỏ hàng</h3>
        <div class="ww-cart-remove-confirm__product">
          <img class="ww-cart-remove-confirm__thumb" src="" alt="" width="56" height="56" hidden>
          <p class="ww-cart-remove-confirm__name"></p>
        </div>
        <div class="ww-cart-remove-confirm__actions">
          <button type="button" class="ww-cart-remove-confirm__btn ww-cart-remove-confirm__btn--ghost" data-ww-confirm-cancel>Giữ lại</button>
          <button type="button" class="ww-cart-remove-confirm__btn ww-cart-remove-confirm__btn--danger" data-ww-confirm-ok>Xóa sản phẩm</button>
        </div>
      </div>
    `;
    document.body.appendChild(el);
    return el;
  }

  function wwConfirmCartRemove({ title, image } = {}) {
    return new Promise((resolve) => {
      const modal = wwEnsureCartRemoveConfirm();
      const nameEl = modal.querySelector(".ww-cart-remove-confirm__name");
      const thumbEl = modal.querySelector(".ww-cart-remove-confirm__thumb");
      const panel = modal.querySelector(".ww-cart-remove-confirm__panel");
      const okBtn = modal.querySelector("[data-ww-confirm-ok]");
      const cancelBtn = modal.querySelector("[data-ww-confirm-cancel]");

      const productTitle = (title || "").trim();
      nameEl.textContent = productTitle || "Sản phẩm trong giỏ";
      if (image) {
        thumbEl.src = image;
        thumbEl.alt = productTitle || "Sản phẩm";
        thumbEl.hidden = false;
      } else {
        thumbEl.removeAttribute("src");
        thumbEl.hidden = true;
      }

      let settled = false;
      const cleanup = (confirmed) => {
        if (settled) return;
        settled = true;
        document.removeEventListener("keydown", onKey, true);
        document.removeEventListener("keyup", onKey, true);
        modal.removeEventListener("click", onClick);
        modal.classList.remove("is-open");
        panel.classList.remove("is-in");
        window.setTimeout(() => {
          modal.setAttribute("hidden", "");
          resolve(confirmed);
        }, 180);
      };

      const onKey = (e) => {
        if (e.key !== "Escape" && e.code !== "Escape") return;
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        if (e.type === "keydown") cleanup(false);
      };

      const onClick = (e) => {
        if (e.target.closest("[data-ww-confirm-ok]")) {
          e.preventDefault();
          cleanup(true);
          return;
        }
        if (e.target.closest("[data-ww-confirm-cancel]") || e.target.closest("[data-ww-confirm-dismiss]")) {
          e.preventDefault();
          cleanup(false);
        }
      };

      modal.removeAttribute("hidden");
      // force reflow for enter animation
      void modal.offsetWidth;
      modal.classList.add("is-open");
      requestAnimationFrame(() => panel.classList.add("is-in"));
      document.addEventListener("keydown", onKey, true);
      document.addEventListener("keyup", onKey, true);
      modal.addEventListener("click", onClick);
      (cancelBtn || okBtn)?.focus?.();
    });
  }

  class RemoveCartButton extends HTMLElement {
    constructor() {
      super();
      this.addEventListener("click", async (event) => {
        event.preventDefault();
        event.stopPropagation();
        if (this.dataset.confirming === "1") return;

        const cartForm = this.closest("cart-form");
        if (!cartForm) return;

        const item = this.closest(".cart-item");
        const title =
          item?.querySelector(".cart-product-col .link")?.textContent?.trim() ||
          item?.querySelector("img")?.alt ||
          "";
        const image = item?.querySelector(".ww-cart-item-img")?.getAttribute("src") || "";

        this.dataset.confirming = "1";
        try {
          const ok = await wwConfirmCartRemove({ title, image });
          if (ok) {
            cartForm.quantityUpdate(this.dataset.lineIndex, 0);
          }
        } finally {
          this.dataset.confirming = "0";
        }
      });
    }
  }
  defineElement("remove-cart-button", RemoveCartButton);

  class CartDrawer extends PortalComponent {
    constructor() {
      super();
      this.cartForm = this.querySelector("cart-form");
      this.addEventListener("click", (event) => {
        const btn = event.target.closest(".ww-cart-continue-shopping");
        if (!btn) return;
        event.preventDefault();
        this.hide();
      });
    }
    changeHash(open) {
      if (window.themeConfigs.mbBreakpoint.matches) {
        if (!window.location.hash == "#cart") {
          window.location.hash = open ? "#cart" : "";
        }
      }
    }
    connectedCallback() {
      if (
        window.location.hash == "#cart" &&
        window.themeConfigs.mbBreakpoint.matches
      ) {
        this.show();
      }
      subscribe(window.themeConfigs.productAddEvent, (e) => {
        if (
          e.action == "drawer" &&
          !isCartPathname() &&
          !this.classList.contains("active")
        ) {
          setTimeout(
            () => this.show(),
            document.querySelector("quick-view.active, quick-view.ww-open")
              ? window.themeConfigs.defaultTransitionTime
              : 0
          );
        }
		    document.querySelectorAll("quick-view").forEach((el) => {
          if (el && typeof el.hide === "function") el.hide();
        });
        document.querySelectorAll("cart-form").forEach((el) => {
          if (el && typeof el.updateCart === "function") el.updateCart();
        });
        // Đảm bảo danh sách trong drawer scroll lên đầu để thấy sp vừa thêm
        window.setTimeout(function () {
          document.querySelectorAll("cart-form").forEach(function (el) {
            if (el && typeof el.scrollCartListToTop === "function") {
              el.scrollCartListToTop();
            }
          });
        }, 120);
        if(e.action == "popup"){
          const activeQuickView = document.querySelector("quick-view.active, quick-view.ww-open");
          if (activeQuickView && typeof activeQuickView.hide === "function") {
            activeQuickView.hide();
          }
          const delay = activeQuickView ? window.themeConfigs.defaultTransitionTime : 0;
          setTimeout(() => {
            const atcPopup = document.querySelector("add-to-cart-popup");
            if (atcPopup && typeof atcPopup.showPopup === "function") {
              atcPopup.showPopup(e.data);
              return;
            }
            // Popup ATC đang tắt: mở cart drawer hoặc chỉ unlock trang
            if (
              !isCartPathname() &&
              window.themeConfigs.cartAction === "drawer" &&
              typeof this.show === "function" &&
              !this.classList.contains("active")
            ) {
              this.show();
              return;
            }
            if (typeof window.__wwUnlockPageIfIdle === "function") {
              window.__wwUnlockPageIfIdle();
            }
          }, delay);
          return;
        }
        if (e.action == "cart") {
          window.location.href = themeApiUrl("/cart");
          return;
        }

        if (e.action == "buynow") {
          window.location.href = themeApiUrl("/thanh-toan");
          return;
        }
      
        if (isCartPathname()) {
          window.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        }

        window.setTimeout(function () {
          if (typeof window.__wwUnlockPageIfIdle === "function") {
            window.__wwUnlockPageIfIdle();
          }
        }, window.themeConfigs.defaultTransitionTime || 400);
      });
    }
    hide() {
      this.changeHash(false);
      super.hide();
    }
    show(opener) {
      try {
        if (!this.inited) {
          if (this.cartForm && typeof this.cartForm.updateCart === "function") {
            this.cartForm.updateCart();
          }
          this.inited = true;
        } else if (typeof window.__wwLoadCartRecommendations === "function") {
          window.__wwLoadCartRecommendations();
        }
      } catch (err) {
        console.warn("Cart drawer refresh skipped:", err);
      }
      this.changeHash(true);
      super.show(opener);
    }
  }

  defineElement("cart-drawer", CartDrawer);

  class VatDrawer extends PortalComponent {
    constructor() {
      super();
      this.addEventListener("change", this.onChange.bind(this));
      this.inputElements = this.querySelectorAll("[name*=attributes]");
      this.form = this.querySelector("form");
      this.form.addEventListener("submit", this.onSubmit.bind(this));
    }
    renderError(errors, input) {}
    validate(input) {
      let errors = validateInput(input);
      if (!errors) return true;
      let wrapper = input.closest(".form-group");
      let error = wrapper.querySelector(".error");
      error.innerHTML = errors.length ? errors.filter(Boolean).join(", ") : "";
      return errors.length ? false : true;
    }
    onChange(e) {
      this.validate(e.target);
    }
    reset() {
      this.inputElements.forEach((el) => {
        el.value = "";
      });
    }
    syncCartForm() {
      const cartForm = document.querySelector("cart-form");
      if (cartForm) {
        this.inputElements.forEach((el) => {
          let name = el.name;
          let input = cartForm.querySelector(`[name="${name}"]`);
          if (input) {
            input.value = el.value;
          }
        });
        cartForm.updateCartAttribute();
      }
    }
    onSubmit(e) {
      e.preventDefault();
      let isValid = Array.from(this.inputElements).map(
        this.validate.bind(this)
      );
      if (isValid.includes(false)) return;
      let isEnabled = this.querySelector(".invoice-checkbox").checked;
      this.querySelector(".invoice").value = isEnabled ? "có" : "không";
      if (!isEnabled) {
        //this.inputElements.forEach((el) => { el.value = '' });
      }
      this.syncCartForm();
      this.hide();
    }
  }

  defineElement("cart-vat-drawer", VatDrawer);

  class CartNote extends HTMLElement {
    constructor() {
      super();
      this.form = this.querySelector("form");
      this.form.addEventListener("submit", this.onSubmit.bind(this));
      this.note = this.querySelector('[name="note"]');
    }

    syncCartForm() {
      const cartForm = document.querySelector("cart-form");
      if (cartForm) {
        let input = cartForm.querySelector(`[name="note"]`);
        if (input) {
          input.value = this.note.value;
        }
        cartForm.updateCartAttribute();
      }
    }
    onSubmit(e) {
      e.preventDefault();
      this.syncCartForm();
      document.querySelector("#cart-note-drawer").hide();
    }
  }

  defineElement("cart-note", CartNote);

  class DatepickerInput extends HTMLElement {
    constructor() {
      super();

      this.input = this.querySelector("input");
    }
    init() {
      if (typeof Datepicker == "undefined" || this.loaded) return;
      Datepicker.locales.vi = {
        days: [
          "Chủ nhật",
          "Thứ hai",
          "Thứ ba",
          "Thứ tư",
          "Thứ năm",
          "Thứ sáu",
          "Thứ bảy",
        ],
        daysShort: ["CN", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"],
        daysMin: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
        months: [
          "Tháng 1",
          "Tháng 2",
          "Tháng 3",
          "Tháng 4",
          "Tháng 5",
          "Tháng 6",
          "Tháng 7",
          "Tháng 8",
          "Tháng 9",
          "Tháng 10",
          "Tháng 11",
          "Tháng 12",
        ],
        monthsShort: [
          "Th1",
          "Th2",
          "Th3",
          "Th4",
          "Th5",
          "Th6",
          "Th7",
          "Th8",
          "Th9",
          "Th10",
          "Th11",
          "Th12",
        ],
        today: "Hôm nay",
        clear: "Xóa",
        format: "dd/mm/yyyy",
      };
      const datepicker = new Datepicker(this.input, {
        // ...options
        autohide: true,

        datesDisabled: function (date, viewId, rangeEnd) {
          let isDateDisabled = true;
          // ...your evaluation logic
          let currentDate = new Date();
          if (date >= currentDate) {
            isDateDisabled = false;
          }
          return isDateDisabled;
        },
        language: "vi",
      });
      this.loaded = true;
    }
    connectedCallback() {
      this.init();

      subscribe(window.themeConfigs.firstInteraction, (e) => {
        this.init();
      });
    }
  }

  defineElement("datepicker-input", DatepickerInput);

  class CartDelivery extends HTMLElement {
    constructor() {
      super();
      this.inputElements = this.querySelectorAll("[name*=attributes]");
      this.form = this.querySelector("form");
      this.form.addEventListener("submit", this.onSubmit.bind(this));
    }

    reset() {
      this.inputElements.forEach((el) => {
        el.value = "";
      });
    }
    syncCartForm() {
      const cartForm = document.querySelector("cart-form");
      if (cartForm) {
        this.inputElements.forEach((el) => {
          let name = el.name;
          let input = cartForm.querySelector(`[name="${name}"]`);
          if (input) {
            input.value = el.value;
          }
        });
        cartForm.updateCartAttribute();
      }
    }
    onSubmit(e) {
      e.preventDefault();
      let isEnabled = this.querySelector("#delivery-enabled").checked;
      this.querySelector("#use-delivery").value = isEnabled ? "có" : "không";

      if (!this.querySelector("#delivery-date-input").value) {
        this.querySelector("#use-delivery").value = "không";
        this.querySelector("#delivery-enabled").removeAttribute("checked");
      }
      this.syncCartForm();
      document.querySelector("#cart-delivery-drawer").hide();
    }
  }

  defineElement("cart-delivery", CartDelivery);

  class AddToCartPopup extends PortalComponent {
    constructor() {
      super();
      this.closeBtn = this.querySelector('.popup-close');
      if (this.closeBtn) {
        this.closeBtn.addEventListener('click', () => this.hide());
      }
      // Đóng popup khi click ra ngoài overlay
      if (this.querySelector('.portal-overlay')) {
        this.querySelector('.portal-overlay').addEventListener('click', () => this.hide());
      }
		this.querySelector('.btn-cart').addEventListener('click', () => this.onCartClick());
    }
	  
	 onCartClick(){
	 	this.hide();
	
		if(isCartPathname()){
		  window.scrollTo({
            top: 0,
            behavior: "smooth",
          });
			
			return;
		}
		if(window.themeConfigs.cartAction == 'drawer' ){
			if(document.querySelector('cart-drawer.active')) return;
			setTimeout(()=>{
				document.querySelector('cart-drawer').show();
			},window.themeConfigs.defaultTransitionTime)
			
		}else{
		window.location.href = themeApiUrl("/cart");
		
		}
	     
	 
	 }

    /**
     * Hiển thị popup với dữ liệu từ cart
     */
    showPopup(data) {
      fetch(themeApiUrl('/cart?view=data'))
        .then(res => res.text())
        .then(html => {
          const doc = new DOMParser().parseFromString(html, 'text/html');
          // Lấy sản phẩm vừa thêm (hoặc sản phẩm cuối cùng)
          const item = doc.querySelector(`.cart-item[data-variant-id="${data.variant_id}"]`);
          if (!item) return;

          const img = item.querySelector('img').src || '';
          const title = data.title;
          const variant = data.variant_title ;
          // Tổng giá và tổng số lượng
          const totalPrice = doc.querySelector('.cart-total .price').textContent || '';
          const totalQuantity = doc.querySelector('.cart-count').textContent || '';

          // Render nội dung popup
          this.querySelector('.popup-product-img').src = img;
          this.querySelector('.popup-product-title').innerHTML = `<a href="${data.url}" class="link" title="${title}">${title}</a>`;
          this.querySelector('.popup-product-variant').textContent = variant;
          this.querySelector('.popup-cart-total').textContent = totalPrice;
          this.querySelector('.popup-cart-quantity').textContent = totalQuantity;
          this.show();
        });
        
    }
  }

  defineElement('add-to-cart-popup', AddToCartPopup);
});