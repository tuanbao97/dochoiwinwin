function themeApiUrl(path) {
  return typeof window.themeUrl === "function"
    ? window.themeUrl(path)
    : path.startsWith("/")
      ? path
      : "/" + path;
}

function initReview() {
  if (window.BPR && window.BPR.loadBadges) {
    window.BPR.init();
  }
}
subscribe(window.themeConfigs.productLoaded, () => {
  initReview();
});

subscribe(window.themeConfigs.firstInteraction, () => {
  class RelatedProducts extends HTMLElement {
    constructor() {
      super();
      this.getProducts();
    }
    renderEmpty() {
      let productList = this.querySelector(".product-list");

      if (this.dataset.emptyContent) {
        productList.innerHTML = `<div class="w-full">${this.dataset.emptyContent}</div>`;
		        this.classList.add( "no-products-content");

      } else {
        this.classList.add("hidden", "no-products");
        if (this.closest(".section-products"))
          this.closest(".section-products").classList.add("hidden");
      }
    }
    getProducts() {
      if (this.dataset.skipSearch === "1") {
        this.renderEmpty();
        return;
      }
      let query = this.dataset.query;
      let productList = this.querySelector(".product-list");
      if (!query) {
        this.renderEmpty();
        return;
      }
      let url = `/search?q=${query}&view=product`;
      if (this.dataset.productType == "row") {
        url = `/search?q=${query}&view=product-row`;
      }
      fetch(themeApiUrl(url))
        .then((response) => response.text())
        .then((res) => {
          let html = new DOMParser().parseFromString(res, "text/html");
          let limit = this.dataset.limit || 4;

          if (html.querySelector("card-product")) {
            let carousel = this.querySelector("carousel-slider");
            productList.innerHTML = "";
            Array.from(html.querySelectorAll("card-product"))
              .slice(0, limit)
              .forEach((el) => {
                let item = document.createElement("div");
                item.appendChild(el);
                if (carousel) {
                  item.classList.add("embla__slide", "h-inherit");
                }
                productList.appendChild(item);
              });
            this.classList.remove("hidden", "no-products");
            if (this.closest(".section-products"))
              this.closest(".section-products").classList.remove("hidden");
            if (carousel) carousel.init();
            publish(window.themeConfigs.productLoaded);
          } else {
            this.renderEmpty();
          }
        });
    }
  }

  defineElement("related-products", RelatedProducts);
  class RecentviewProducts extends RelatedProducts {
    constructor() {
      super();
      this.dataset.query = this.getQueryStorage() || "";
      this.getProducts();
    }
    getQueryStorage() {
      let query = "";
      let storage =
        JSON.parse(localStorage.getItem(window.themeConfigs.recentStorage)) ||
        [];

      if (storage && storage.length && Array.isArray(storage)) {
        let productId = this.dataset.product;
        if (productId) {
          storage = storage.filter((item) => item !== productId);
        }
        query = "(id:" + storage.join(" OR id:") + ")";
      }
      return query;
    }
  }
  defineElement("recentview-products", RecentviewProducts);

  class MediaGallery extends EmblaComponent {
    constructor() {
      super();
      this.elements = {
        thumbnails: this.querySelector('[id^="GalleryThumbnails"]'),
        mainGallery: this.querySelector('[id^="GalleryMain"]'),
      };
      this.mql = window.themeConfigs.mbBreakpoint;
      this.prevBtnNode = this.elements.mainGallery
        ? this.elements.mainGallery.querySelector(".embla__button--prev")
        : null;
      this.nextBtnNode = this.elements.mainGallery
        ? this.elements.mainGallery.querySelector(".embla__button--next")
        : null;
      this.thumbPrevBtnNode = this.elements.thumbnails
        ? this.elements.thumbnails.querySelector(".embla__button--prev")
        : null;
      this.thumbNextBtnNode = this.elements.thumbnails
        ? this.elements.thumbnails.querySelector(".embla__button--next")
        : null;
    }
    connectedCallback() {
      this.mql.addEventListener("change", this.init.bind(this));
      const mainContainer =
        this.querySelector("#ww-pd-gallery-main") ||
        this.elements.mainGallery?.querySelector(".embla__container");
      const slideCount = mainContainer
        ? mainContainer.querySelectorAll(".embla__slide").length
        : 0;
      const isAjaxProduct =
        !!document.getElementById("ww-pd-product-form") && slideCount === 0;

      if (isAjaxProduct) {
        this._waitHydrate = true;
        subscribe(window.themeConfigs.productLoaded, () => {
          if (!this._waitHydrate) return;
          this._waitHydrate = false;
          this.init(this.mql);
          this.bindSpotlightClicks();
        });
        return;
      }

      this.init(this.mql);
      this.bindSpotlightClicks();
    }
    bindSpotlightClicks() {
      this.querySelectorAll("img").forEach((el) =>
        el.removeAttribute("loading")
      );
      this.querySelectorAll(".swiper-spotlight").forEach((el) => {
        if (el.dataset.spotlightBound === "1") return;
        el.dataset.spotlightBound = "1";
        el.addEventListener("click", () => {
          this.lightBox(el);
        });
      });
    }
    replaceNavButtons() {
      if (this.prevBtnNode && this.nextBtnNode) {
        const newPrev = this.prevBtnNode.cloneNode(true);
        const newNext = this.nextBtnNode.cloneNode(true);
        this.prevBtnNode.replaceWith(newPrev);
        this.nextBtnNode.replaceWith(newNext);
        this.prevBtnNode = newPrev;
        this.nextBtnNode = newNext;
      }
      if (this.thumbPrevBtnNode && this.thumbNextBtnNode) {
        const newThumbPrev = this.thumbPrevBtnNode.cloneNode(true);
        const newThumbNext = this.thumbNextBtnNode.cloneNode(true);
        this.thumbPrevBtnNode.replaceWith(newThumbPrev);
        this.thumbNextBtnNode.replaceWith(newThumbNext);
        this.thumbPrevBtnNode = newThumbPrev;
        this.thumbNextBtnNode = newThumbNext;
      }
    }
    destroyGallery() {
      if (this._galleryTeardown) {
        this._galleryTeardown();
        this._galleryTeardown = null;
      }
      if (this.thumbGallery) {
        this.thumbGallery.destroy();
        this.thumbGallery = null;
      }
      if (this.mainGallery) {
        this.mainGallery.destroy();
        this.mainGallery = null;
      }
    }
    disconnectedCallback() {
      this.destroyGallery();
    }
    slideTo(index) {
      this.mainGallery && this.mainGallery.scrollTo(index);
    }
    lightBox(el) {
      let index = parseInt(el.dataset.index) + 1;
      if (!Spotlight) return;

      const toOriginalImageUrl = (url) => {
        if (!url) return "";
        return String(url).replace(
          /\/(\d+x\d+)_([^\/?#]+)(\?[^#]*)?(#.*)?$/i,
          "/$2$3$4"
        );
      };

      let imageList = Array.from(
        this.querySelectorAll(".swiper-spotlight")
      ).map((slideEl) => {
        let src =
          slideEl.dataset.originalSrc ||
          toOriginalImageUrl(slideEl.dataset.src) ||
          slideEl.dataset.src ||
          "";
        if (slideEl.dataset.video) {
          let src = slideEl.dataset.video;
          return {
            media: "node",
            src: (function () {
              const video = document.createElement("video");
              video.classList.add("aspect-video", "container");
              video.src = src;
              video.dataset.src = video.src;
              video.width = 560;
              video.height = 315;
              video.controls = true;
              video.autoplay = true;
              video.autoplay = true;
              return video;
            })(),
          };
        }
        if (src && src.includes("youtube")) {
          return {
            media: "node",
            src: (function () {
              const iframe = document.createElement("iframe");
              iframe.allow =
                "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
              iframe.classList.add("aspect-video", "iframe", "container");
              iframe.src = src;
              iframe.dataset.src = iframe.src;
              iframe.wdith = 560;
              iframe.height = 315;
              iframe.frameborder = "0";
              iframe.allowfullscreen = true;

              return iframe;
            })(),
          };
        }
        // Lightbox + nút download luôn dùng ảnh gốc (không prefix aspect ratio)
        return {
          src: src,
        };
      });
      Spotlight.show(imageList, {
        download: true,
        onchange: function (index, options) {
          let slide = index - 1;
          if (
            options.media == "node" &&
            options.src.classList.contains("iframe")
          ) {
            options.src.src = options.src.dataset.src;
          }
        },
      });
      if (
        window.wwStorefrontImage &&
        typeof window.wwStorefrontImage.patchSpotlightDownload === "function"
      ) {
        window.wwStorefrontImage.patchSpotlightDownload();
      }
      Spotlight.goto(index);
    }
    init() {
      if (!this.elements?.mainGallery) return;

      // Refresh refs (quick-view inject HTML mới mỗi lần mở)
      this.elements.thumbnails = this.querySelector('[id^="GalleryThumbnails"]');
      this.elements.mainGallery = this.querySelector('[id^="GalleryMain"]');
      this.prevBtnNode = this.elements.mainGallery
        ? this.elements.mainGallery.querySelector(".embla__button--prev")
        : null;
      this.nextBtnNode = this.elements.mainGallery
        ? this.elements.mainGallery.querySelector(".embla__button--next")
        : null;
      this.thumbPrevBtnNode = this.elements.thumbnails
        ? this.elements.thumbnails.querySelector(".embla__button--prev")
        : null;
      this.thumbNextBtnNode = this.elements.thumbnails
        ? this.elements.thumbnails.querySelector(".embla__button--next")
        : null;

      const mainContainer =
        this.querySelector("#ww-pd-gallery-main") ||
        this.elements.mainGallery.querySelector(".embla__container");
      if (!mainContainer?.querySelectorAll(".embla__slide").length) return;

      this.destroyGallery();
      this.replaceNavButtons();

      const OPTIONS = { align: "start", containScroll: "trimSnaps" };
      const OPTIONS_THUMBS = {
        align: "start",
        containScroll: "trimSnaps",
        dragFree: true,
      };
      mainContainer.querySelectorAll(".embla__slide").forEach((slide, index) => {
        slide.dataset.index = index;
      });
      const viewportNodeMainCarousel =
        this.elements.mainGallery.querySelector(".embla__viewport");

      const emblaApiMain = EmblaCarousel(viewportNodeMainCarousel, OPTIONS);
      const teardowns = [];

      if (this.elements.thumbnails) {
        const viewportNodeThumbCarousel =
          this.elements.thumbnails.querySelector(".embla__viewport");
        const emblaApiThumb = EmblaCarousel(
          viewportNodeThumbCarousel,
          OPTIONS_THUMBS
        );
        this.thumbGallery = emblaApiThumb;
        teardowns.push(
          this.addThumbBtnsClickHandlers(emblaApiMain, emblaApiThumb)
        );
        teardowns.push(
          this.addToggleThumbBtnsActive(emblaApiMain, emblaApiThumb)
        );
        const removePrevNext = this.addPrevNextBtnsClickHandlers(
          emblaApiMain,
          this.prevBtnNode,
          this.nextBtnNode
        );
        if (removePrevNext) teardowns.push(removePrevNext);
        const removeThumbPrevNext = this.addThumbStripNavHandlers(
          emblaApiThumb,
          this.thumbPrevBtnNode,
          this.thumbNextBtnNode
        );
        if (removeThumbPrevNext) teardowns.push(removeThumbPrevNext);

        teardowns.push(
          this.watchGalleryImages([emblaApiMain, emblaApiThumb])
        );
      } else {
        const removePrevNext = this.addPrevNextBtnsClickHandlers(
          emblaApiMain,
          this.prevBtnNode,
          this.nextBtnNode
        );
        if (removePrevNext) teardowns.push(removePrevNext);
        teardowns.push(this.watchGalleryImages([emblaApiMain]));
      }

      this.mainGallery = emblaApiMain;
      this._galleryTeardown = () => {
        teardowns.forEach((fn) => fn && fn());
      };
    }
    addThumbStripNavHandlers(emblaApiThumb, prevBtn, nextBtn) {
      if (!prevBtn || !nextBtn || !emblaApiThumb) return;
      const updateButtons = () => {
        if (emblaApiThumb.canScrollPrev()) prevBtn.removeAttribute("disabled");
        else prevBtn.setAttribute("disabled", "disabled");
        if (emblaApiThumb.canScrollNext()) nextBtn.removeAttribute("disabled");
        else nextBtn.setAttribute("disabled", "disabled");
      };
      const scrollPrev = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const idx = emblaApiThumb.selectedScrollSnap();
        if (idx > 0) {
          emblaApiThumb.scrollTo(idx - 1);
        } else if (emblaApiThumb.canScrollPrev()) {
          emblaApiThumb.scrollPrev();
        }
      };
      const scrollNext = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const snaps = emblaApiThumb.scrollSnapList();
        const idx = emblaApiThumb.selectedScrollSnap();
        if (idx < snaps.length - 1) {
          emblaApiThumb.scrollTo(idx + 1);
        } else if (emblaApiThumb.canScrollNext()) {
          emblaApiThumb.scrollNext();
        }
      };
      prevBtn.addEventListener("click", scrollPrev, false);
      nextBtn.addEventListener("click", scrollNext, false);
      emblaApiThumb
        .on("init", updateButtons)
        .on("reInit", updateButtons)
        .on("select", updateButtons)
        .on("settle", updateButtons);
      updateButtons();
      return () => {
        prevBtn.removeEventListener("click", scrollPrev, false);
        nextBtn.removeEventListener("click", scrollNext, false);
        if (typeof emblaApiThumb.off === "function") {
          emblaApiThumb.off("init", updateButtons);
          emblaApiThumb.off("reInit", updateButtons);
          emblaApiThumb.off("select", updateButtons);
          emblaApiThumb.off("settle", updateButtons);
        }
      };
    }
    watchGalleryImages(apis) {
      const imgs = Array.from(this.querySelectorAll("img"));
      let done = false;
      const reinit = () => {
        if (done) return;
        apis.forEach((api) => {
          if (api && typeof api.reInit === "function") api.reInit();
        });
      };
      const onReady = () => {
        requestAnimationFrame(() => {
          reinit();
          requestAnimationFrame(reinit);
        });
      };
      imgs.forEach((img) => {
        if (img.complete) return;
        img.addEventListener("load", onReady, { once: true });
        img.addEventListener("error", onReady, { once: true });
      });
      const t1 = setTimeout(onReady, 100);
      const t2 = setTimeout(onReady, 400);
      return () => {
        done = true;
        clearTimeout(t1);
        clearTimeout(t2);
      };
    }
  }
  defineElement("media-gallery", MediaGallery);

  class ProductForm extends HTMLElement {
    constructor() {
      super();
      this.form = this.querySelector('[action="/cart/add"]');
      if (!this.form || this.form.id === "add-to-cart-form") return;
      this.checkoutButton = this.querySelector('[name="buynow"]');
      this.addToCartButton = this.querySelector('[name="addtocart"]');
      this.checkoutButton &&
        this.checkoutButton.addEventListener("click", () => {
          this.checkout = true;
        });
      this.addToCartButton &&
        this.addToCartButton.addEventListener("click", () => {
          this.checkout = false;
        });
      this.form.addEventListener("submit", this.onSubmit.bind(this));
    }
    toggleLoading(loading) {
      const button = this.checkout ? this.checkoutButton : this.addToCartButton;

      if (loading) {
        button.classList.add("loading");
      } else {
        button.classList.remove("loading");
      }
    }
    onSubmit(e) {
      e.preventDefault();
      this.toggleLoading(true);
      const data = serializeForm(this.form);
      const url = this.form.action;
      const { addToCartAction, productAddEvent } = window.themeConfigs;
      fetch(themeApiUrl("/cart/add"), {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") ||
            (typeof window.__csrfToken === "function"
              ? window.__csrfToken()
              : ""),
        },
        credentials: "same-origin",
        body: data,
      })
        .then(async (res) => {
          const response = await res.json().catch(() => ({}));
          if (!res.ok) throw new Error(response.message || "Không thể thêm sản phẩm vào giỏ");
          return response;
        })
        .then((response) => {
          publish(productAddEvent, {
            data: response,
            action: this.checkout ? "buynow" : addToCartAction,
          });
          this.toggleLoading(false);
        })
        .catch((err) => {
          this.toggleLoading(false);
          publish(window.themeConfigs.error, {
            error: err,
          });
        });
    }
  }
  defineElement("product-form", ProductForm);

  class VariantPicker extends HTMLElement {
    constructor() {
      super();
      this.addEventListener("change", this.onVariantChange);
      this.section =
        this.closest(`#main-product-${this.dataset.id}`) ||
        this.closest(".main-product") ||
        this.closest("product-form");
      if (!this.section) return;

      const jsonEl = this.section.querySelector(
        '[data-type="product-json"], #ww-pd-product-json'
      );
      if (!jsonEl || !String(jsonEl.textContent || "").trim()) return;

      try {
        this.productData = JSON.parse(jsonEl.textContent);
      } catch (e) {
        return;
      }
      if (!this.productData || !Array.isArray(this.productData.variants)) return;

      this.variantData = this.productData.variants;
      if (!this.variantData.length) return;

      this.updateOptions();
      this.updateCurrentVariant();
      this.updateOptionStatus();

      this.querySelectorAll("label").forEach((el) =>
        el.addEventListener("click", this.updateGallery.bind(this))
      );
    }
    connectedCallback() {
      subscribe(window.themeConfigs.quickViewShow, () => {
        let otherVariants = document.querySelectorAll(
          `variant-picker[data-id="${this.dataset.id}"]`
        );
        if (otherVariants && otherVariants.length > 1) {
          otherVariants.forEach((variantPicker) => {
            this.syncOptions(variantPicker, this);
          });
        }
      });
      this.abortController = new AbortController();
    }
    disconnectedCallback() {
      this.abortController.abort();
    }
    onVariantChange(e) {
      this.updateOptions(e);
      this.updateCurrentVariant();
      this.updateOptionStatus();

      if (!this.currentVariant) {
        this.updateButtons(true, true);
        this.updatePrice(
          `  <span class="price-contact text-h4 text-error"> Liên hệ </span>`
        );
      } else {
        document
          .querySelectorAll(
            `variant-picker:not(#${this.id})[data-id="${this.dataset.id}"]`
          )
          .forEach((variantPicker) => {
            this.syncOptions(this, variantPicker);
          });
        this.updateVariantInput();
        this.updateProductInfo();
        this.updateGallery();
      }
    }
    updateButtons(disabled, hide, notsoldout) {
      let productCtaGroup = this.section.querySelector(".product-cta");
      let form = this.section.querySelector('[action="/cart/add"]');
	  let croCta = document.querySelector(`.cro-btn-item  [data-product-id="${this.productData.id}"]`)
      if (notsoldout) {
        productCtaGroup.querySelector(".btn--soldout").classList.add("hidden");
      } else {
        productCtaGroup
          .querySelector(".btn--soldout")
          .classList.remove("hidden");
      }
      if (disabled) {
        form.classList.add("hidden");
      } else {
        form.classList.remove("hidden");
      }

      if (hide) {
        productCtaGroup.classList.add("hidden");
      } else {
        productCtaGroup.classList.remove("hidden");
      }
	  if(croCta){
	   	croCta.innerHTML = !notsoldout ? 'Hết hàng' : disabled ? 'Liên hệ' : 'Thêm vào giỏ'
	  
	  }
		
    }
    updatePrice(priceBox) {
      this.section.querySelector(".price-box").innerHTML = priceBox;
    }
    updateProductInfo() {
      if (this.abortController) {
        this.abortController.abort();
      }
      this.abortController = new AbortController();
      this.section.classList.add("loading");
      fetch(
        themeApiUrl(
          `/${this.productData.alias}?variantId=${this.currentVariant.id}&view=quickview`
        ),
        { signal: this.abortController.signal }
      )
        .then((response) => response.text())
        .then((res) => {
          setTimeout(() => this.section.classList.remove("loading"), 300);

          let html = new DOMParser().parseFromString(res, "text/html");
          let replaceSelectors = [
            ".group-status",
            ".price-box",
            ".inventory-status",
          ];
          let quantityInput = this.section.querySelector(
            ".product-quantity input"
          );
          let soldout = html.querySelector(".btn--soldout");
          let productCtaGroup = html.querySelector(".product-cta");
          let form = html.querySelector('[action="/cart/add"]');
          let installment = html.querySelector(".installment-button");

          replaceSelectors.forEach((el) => {
            if (!this.section.querySelector(el)) return;
            this.section.querySelector(el).innerHTML = html.querySelector(el)
              ? html.querySelector(el).innerHTML
              : "";
          });

          this.updateButtons(
            form.classList.contains("hidden"),
            productCtaGroup.classList.contains("hidden"),
            soldout.classList.contains("hidden")
          );
          quantityInput.max = html
            .querySelector(".product-quantity input")
            .getAttribute("max");
          if (installment) {
            const installmentButton = this.section.querySelector(
              ".installment-button"
            );
            installmentButton.classList.toggle(
              "hidden",
              installment.classList.contains("hidden")
            );
          }
          publish(window.themeConfigs.variantChanged, {
            target: this,
            data: html,
          });
        })
        .catch((err) => {
          this.section.classList.remove("loading");
          publish(window.themeConfigs.error, {
            error: err,
          });
        });
    }
    updateGallery() {
      let gallery = this.section.querySelector("media-gallery");
      if (this.currentVariant && this.currentVariant.image && gallery) {
        let src = this.currentVariant.image.src;
        let image = gallery.querySelector(`[data-href="${src}"]`);
        if (!image) return;
        let index = image.dataset.index;
        gallery.slideTo && gallery.slideTo(index);
      }
    }
    updateVariantInput() {
      let form = this.section.querySelector('[ action="/cart/add"]');
      let input = form.querySelector('[name="variantId"]');
      input.value = this.currentVariant.id;
    }
    updateCurrentVariant() {
      this.currentVariant = this.variantData.find((variant) => {
        return !variant.options
          .map((option, index) => {
            return this.options[index] === option;
          })
          .includes(false);
      });
    }
    syncOptions(variantPicker, variantPickerUpdate) {
      const fieldsets = Array.from(variantPicker.querySelectorAll(".fieldset"));
      const _this = variantPickerUpdate;
      if (
        variantPicker.currentVariant &&
        _this.currentVariant &&
        _this.currentVariant.id == variantPicker.currentVariant.id
      )
        return;
      fieldsets.map((fieldset) => {
        let radio = fieldset.querySelector("input:checked");
        let name = fieldset.dataset.option;
        if (radio && radio.value) {
          let checkbox = _this.querySelector(
            `.fieldset[data-option="${name}"] input[value="${radio.value}"]`
          );
          if (checkbox) checkbox.checked = true;
        }
      });
      _this.onVariantChange();
    }
    updateOptions(e) {
      const fieldsets = Array.from(this.querySelectorAll(".fieldset"));
      this.options = fieldsets.map((fieldset) => {
        if (fieldset.querySelector("input:checked")) {
          fieldset.classList.add("selected");
          return Array.from(fieldset.querySelectorAll("input")).find(
            (radio) => radio.checked
          ).value;
        } else {
          fieldset.classList.remove("selected");
        }
      });
    }
    updateOptionStatus() {
      // lấy tất cả variant có giá trị như option1 đang được chọn
      const option1Selected = this.querySelector(":checked").value;
      const variantOption1 = this.variantData.filter((variant) => {
        return variant.option1 == option1Selected;
      });
      const inputWrappers = [
        ...this.querySelectorAll(".variant-picker__input"),
      ];
      // loop qua các option khác option1
      inputWrappers.forEach((option, index) => {
        if (index === 0) return;
        // lấy các  option trong đó
        const optionInputs = [
          ...option.querySelectorAll('input[type="radio"]'),
        ];
        // lấy giá trị của option trước đó đang được chọn
        let previousOption =
          inputWrappers[index - 1].querySelector(":checked").value;
        // lấy ra danh sách những giá trị có variant còn hàng từ danh sách option1
        const availableOptions = variantOption1
          .filter(
            (variant) =>
              variant.available && variant[`option${index}`] === previousOption
          )
          .map((variantOption) => variantOption[`option${index + 1}`]);
        optionInputs.forEach((input) => {
          if (availableOptions.includes(input.value)) {
            input.classList.remove("disabled");
          } else {
            input.classList.add("disabled");
          }
        });
      });
    }
  }

  defineElement("variant-picker", VariantPicker);

  class CardProduct extends HTMLElement {
    constructor() {
      super();
      this.form = this.querySelector("form");
      this.addToCartBtn = this.querySelector(".add_to_cart");
      if (this.addToCartBtn) {
        this.addToCartBtn.addEventListener("click", this.onClick.bind(this));
      }
      this.addEventListener("click", (e) => {
        e.stopPropagation();
      });

      if (this.form) {
        this.form.addEventListener("submit", (e) => e.preventDefault());
      }
      this.querySelectorAll(".card-product__option").forEach((el) => {
        el.addEventListener("click", this.onVariantClick.bind(this));
      });
    }

    onVariantClick(e) {
      e.preventDefault();
      e.stopPropagation();
      let image = e.currentTarget.dataset.image;
      if (image) {
        this.querySelector(".card-product__image").src = image;
        this.querySelector(".card-product__image").srcset = image;
      }
    }
    onClick(e) {
      e.preventDefault();
      this.addToCart(e);
    }
    addToCart(e) {
      e.preventDefault();
      if (!this.form) return;
      const data = serializeForm(this.form);
      const { addToCartAction, productAddEvent } = window.themeConfigs;
      fetch(themeApiUrl("/cart/add"), {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") ||
            (typeof window.__csrfToken === "function"
              ? window.__csrfToken()
              : ""),
        },
        credentials: "same-origin",
        body: data,
      })
        .then(async (res) => {
          const response = await res.json().catch(() => ({}));
          if (!res.ok) throw new Error(response.message || "Không thể thêm sản phẩm vào giỏ");
          return response;
        })
        .then((response) => {
          publish(productAddEvent, {
            data: response,
            action: addToCartAction,
          });
        })
        .catch((err) => {
          publish(window.themeConfigs.error, {
            error: err,
          });
        });
    }
  }
  defineElement("card-product", CardProduct);

  class QuickView extends PortalComponent {
    constructor() {
      super();
      this.mql = window.themeConfigs.mbBreakpoint;
      this.mql.addEventListener("change", (media) =>
        this.changAnimationType(media)
      );
      this.changAnimationType(this.mql);
    }
    changAnimationType(media) {
      this.dataset.animation = media.matches
        ? "slide-in-bottom"
        : "scale-in-hor-left";
    }
    loadProduct() {
      if (!this.url) return;
      const inner = this.querySelector(".portal-inner");
      inner.classList.add("loading");
      fetch(themeApiUrl(this.url))
        .then((response) => response.text())
        .then((res) => {
          inner.classList.remove("loading");
          let html = new DOMParser().parseFromString(res, "text/html");
          const shell = html.querySelector(".ww-qv-shell");
          const pf = shell || html.querySelector("product-form");
          if (!pf) {
            publish(window.themeConfigs.error, {
              error: new Error("Không tải được nội dung xem nhanh"),
            });
            return;
          }
          this.querySelector(".portal-inner .product-wrapper").innerHTML =
            pf.outerHTML;
          publish(window.themeConfigs.productLoaded);
          publish(window.themeConfigs.quickViewShow);
        })
        .catch((err) => {
          inner.classList.remove("loading");
          publish(window.themeConfigs.error, {
            error: err,
          });
        });
    }
    show(opener) {
      let url = opener.dataset.product + "/?view=quickview";
      if (this.url != url) {
        this.url = url;
        this.querySelector(".portal-inner .product-wrapper").innerHTML = "";
        this.loadProduct();
      }

      super.show(opener);
    }
    hide() {
      super.hide();
    }
  }

  defineElement("quick-view", QuickView);
});

/** Đăng ký compare-* sau khi main.js đã define PortalOpener / PortalComponent
 *  (product.js defer có thể chạy trước main.js trên một số trang). */
function wwRegisterCompareElements() {
  if (typeof PortalOpener === "undefined" || typeof PortalComponent === "undefined") {
    return false;
  }

  if (!customElements.get("compare-button")) {
    class CompareButton extends PortalOpener {
      constructor() {
        super();
        this.storageKey = window.themeConfigs.compareProStorage;
        this.eventUpdate = window.themeConfigs.copmareProUpdate;
        this.updateText();
      }
      connectedCallback() {
        this.buttonSubsciber = subscribe(this.eventUpdate, (e) => {
          let data = e.data;
          this.updateText(data);
        });
      }
      disconnectedCallback() {
        if (this.buttonSubsciber) {
          this.buttonSubsciber();
        }
      }
      getStorage() {
        return JSON.parse(localStorage.getItem(this.storageKey)) || [];
      }
      updateText(data) {
        let label = this.button && this.button.querySelector("span");
        if (!label || !this.button) return;
        const productId = this.button.dataset.product;
        let compareProducts = data || this.getStorage();
        label.textContent =
          compareProducts.indexOf(productId) > -1 ? "Đã thêm so sánh" : "So sánh";
      }
      updateStorage() {
        let compareProducts = this.getStorage();
        const productId = this.button.dataset.product;
        if (compareProducts.indexOf(productId) === -1) {
          if (compareProducts.length >= 3) {
            compareProducts.pop();
          }
          compareProducts.unshift(productId);
          localStorage.setItem(this.storageKey, JSON.stringify(compareProducts));
        }

        publish(this.eventUpdate, {
          data: compareProducts,
        });
      }
      onClick(e) {
        const type = this.button.dataset.productType;
        if (document.querySelector("compare-qv.loading")) return;
        if (
          document.querySelector(".compare-product") &&
          !document.querySelector(`.compare-product[data-product-type="${type}"]`)
        ) {
          publish(window.themeConfigs.error, {
            error: {
              message: "Sản phẩm bạn đang so sánh không cùng loại",
            },
          });
          return;
        }
        this.updateStorage();
        super.onClick(e);
      }
    }

    defineElement("compare-button", CompareButton);
  }

  if (!customElements.get("compare-qv")) {
    class CompareQV extends PortalComponent {
      constructor() {
        super();
        this.storageKey = window.themeConfigs.compareProStorage;
        this.eventUpdate = window.themeConfigs.copmareProUpdate;
      }
      connectedCallback() {
        super.connectedCallback();
        subscribe(this.eventUpdate, (e) => {
          this.getProductCompare();
        });
        this.getProductCompare();
      }
      getStorage() {
        return JSON.parse(localStorage.getItem(this.storageKey)) || [];
      }
      getProductCompare() {
        let compareProducts = this.getStorage();
        if (compareProducts && compareProducts.length) {
          const searchTerm = "(id:" + compareProducts.join(" OR id:") + ")";
          this.classList.add("loading");
          fetch(themeApiUrl(`/search?q=${searchTerm}&view=compare-item`))
            .then((resposne) => resposne.text())
            .then((res) => {
              this.classList.remove("loading");
              let html = new DOMParser().parseFromString(res, "text/html");
              if (html.querySelector(".compare-product__qv-item")) {
                this.querySelector(".compare-product-list").innerHTML = res;
                this.initEvent();
                let numberPro = html.querySelectorAll(".compare-product").length;
                document.querySelector(".compare-count").textContent =
                  "(" + numberPro + ")";
                this.toggelFloatButn(true);
              } else {
                this.hide();
                this.toggelFloatButn(false);
              }
            })
            .catch((err) => {
              this.classList.remove("loading");
              publish(window.themeConfigs.error, {
                error: err,
              });
            });
        } else {
          this.hide();
          this.toggelFloatButn(false);
          this.querySelector(".compare-product-list").innerHTML = "";
        }
      }
      toggelFloatButn(show) {
        if (!document.querySelector(".compare-opener")) return;
        if (show) {
          document.querySelector(".compare-opener").classList.remove("hidden");
        } else {
          document.querySelector(".compare-opener").classList.add("hidden");
          document.querySelector(".compare-count").textContent = 0;
        }
      }
      initEvent() {
        this.querySelectorAll(".compare-product__qv-remove").forEach((el) => {
          el.addEventListener("click", this.removeItem.bind(this));
        });
        this.querySelector(".js-compare-product-remove-all").addEventListener(
          "click",
          this.removeAll.bind(this)
        );
      }

      removeItem(e) {
        const id = e.currentTarget.dataset.id;
        const newCompareList = this.getStorage();
        if (newCompareList.indexOf(id) > -1) {
          newCompareList.splice(newCompareList.indexOf(id), 1);
          localStorage.setItem(this.storageKey, JSON.stringify(newCompareList));

          publish(this.eventUpdate, {
            data: newCompareList,
          });
        }
      }
      removeAll() {
        localStorage.setItem(this.storageKey, JSON.stringify([]));
        publish(this.eventUpdate, {
          data: [],
        });
      }

      show() {
        this.getProductCompare();
        super.show();
      }
    }

    defineElement("compare-qv", CompareQV);
  }

  return true;
}

/** Cảnh báo đỏ khi tăng số lượng vượt tồn kho (trang sản phẩm + xem nhanh).
 *  Tồn kho lấy từ data-stock do hydrate/quick-view gán; thiếu attribute thì bỏ qua. */
(function wwQuantityStockHint() {
  var HIDE_AFTER = 4000;

  function stockOf(input) {
    var raw = input.getAttribute("data-stock");
    if (raw === null || raw === "") return null;
    var stock = Math.floor(Number(raw));
    return isFinite(stock) && stock >= 0 ? stock : null;
  }

  function hintOf(input, create) {
    var host = input.closest("quantity-input");
    if (!host) return null;
    var hint = host.parentElement
      ? host.parentElement.querySelector(".ww-qty-stock-msg")
      : null;
    if (!hint && create) {
      hint = document.createElement("span");
      hint.className = "ww-qty-stock-msg";
      hint.setAttribute("role", "alert");
      host.insertAdjacentElement("afterend", hint);
    }
    return hint;
  }

  function show(input, text) {
    var hint = hintOf(input, true);
    if (!hint) return;
    hint.textContent = text;
    hint.classList.add("is-visible");
    clearTimeout(hint._wwHideTimer);
    hint._wwHideTimer = setTimeout(function () {
      hint.classList.remove("is-visible");
    }, HIDE_AFTER);
  }

  function hide(input) {
    var hint = hintOf(input, false);
    if (!hint) return;
    clearTimeout(hint._wwHideTimer);
    hint.classList.remove("is-visible");
  }

  function check(input, wanted) {
    var stock = stockOf(input);
    if (stock === null) return;
    if (stock <= 0) {
      show(input, "Sản phẩm đã hết hàng");
      return;
    }
    if (wanted > stock) {
      show(input, "Chỉ còn " + stock + " sản phẩm trong kho");
      return;
    }
    hide(input);
  }

  document.addEventListener(
    "click",
    function (e) {
      var target = e.target;
      if (!target || !target.closest) return;

      var btn = target.closest('quantity-input button[name="plus"], quantity-input button[name="minus"]');
      if (btn) {
        var host = btn.closest("quantity-input");
        var input = host && host.querySelector("input");
        if (!input) return;
        if (btn.getAttribute("name") === "minus") {
          hide(input);
          return;
        }
        check(input, Math.floor(Number(input.value) || 1) + 1);
        return;
      }

      if (target.closest("[data-variant-id]")) {
        document.querySelectorAll("quantity-input input").forEach(hide);
      }
    },
    true
  );

  document.addEventListener(
    "input",
    function (e) {
      var input = e.target;
      if (!input || input.tagName !== "INPUT" || !input.closest) return;
      if (!input.closest("quantity-input")) return;
      var value = Math.floor(Number(input.value) || 0);
      if (value <= 0) {
        hide(input);
        return;
      }
      check(input, value);
    },
    true
  );

  window.wwQuantityStockHint = { reset: hide };
})();

(function wwWaitCompareDeps() {
  if (wwRegisterCompareElements()) return;
  var tries = 0;
  var timer = setInterval(function () {
    tries += 1;
    if (wwRegisterCompareElements() || tries >= 200) {
      clearInterval(timer);
    }
  }, 50);
})();
