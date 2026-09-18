class TabsComponent extends HTMLElement {
  constructor() {
    super();
    this.tabBtns = Array.from(this.querySelectorAll(".tab-btn"));
    this.tabContent = Array.from(this.querySelectorAll(".tab-content"));
    this.tabBtns.forEach((el) => {
      el.addEventListener("click", this.onTabClick.bind(this));
    });
  }

  onTabClick(e) {
    e.preventDefault && e.preventDefault();
    const target = e.currentTarget.getAttribute("aria-controls");
    this.tabBtns.forEach((el) => {
      el.classList.remove("active");
      el.ariaSelected = false;
    });
    const activeTab = this.querySelector(`#${target}`);
    this.activeTab = activeTab;
    if (activeTab) {
      activeTab.classList.remove("hidden");
      publish("EGA:tab-update", {
        target: this,
        activeTab: activeTab,
      });
    }
    this.tabContent.forEach(
      (el) => el.id !== target && el.classList.add("hidden")
    );

    e.currentTarget.classList.add("active");
    e.currentTarget.ariaSelected = true;
  }
}
defineElement("tabs-component", TabsComponent);

/** Khóa scroll body khi mở modal/drawer — bù padding để tránh nhảy layout khi ẩn/hiện scrollbar */
(function () {
  function scrollbarWidth() {
    return Math.max(0, window.innerWidth - document.documentElement.clientWidth);
  }

  window.__wwLockBodyScroll = function () {
    var pad = scrollbarWidth();
    document.documentElement.style.setProperty("--ww-scroll-lock-pad", pad + "px");
    document.body.classList.add("overflow-hidden");
    document.documentElement.classList.add("overflow-hidden");
  };

  window.__wwUnlockBodyScroll = function (force) {
    if (
      !force &&
      document.querySelector(".portal.active, quick-view.active, quick-view.ww-open")
    ) {
      return;
    }
    document.body.classList.remove("overflow-hidden");
    document.documentElement.classList.remove("overflow-hidden");
    document.body.style.removeProperty("overflow");
    document.documentElement.style.removeProperty("overflow");
    document.documentElement.style.removeProperty("--ww-scroll-lock-pad");
  };
})();

class PortalComponent extends HTMLElement {
  constructor() {
    super();
    this.popup = this.querySelector("dialog");
    this.inner = this.querySelectorAll(".animation");
    this.animationTime = this.dataset.animation ? 400 : 0;
    this.querySelectorAll('[id^="PortalClose-"]').forEach((el) => {
      el.addEventListener("click", this.hide.bind(this, false));
    });
    this.addEventListener("keyup", (event) => {
      if (event.code && event.code.toUpperCase() === "ESCAPE") this.hide();
    });
    if (this.querySelector(".portal-overlay")) {
      this.querySelector(".portal-overlay").addEventListener(
        "click",
        this.hide.bind(this, false)
      );
    }
  }

  connectedCallback() {
    if (this.moved) return;
    this.moved = true;
    document.body.appendChild(this);
  }

  show(opener) {
    this.openedBy = opener;
    playAnimation({
      query: this.inner,
      animation: this.dataset.animation,
      time: this.animationTime,
    });
    if (typeof window.__wwLockBodyScroll === "function") {
      window.__wwLockBodyScroll();
    } else {
      document.body.classList.add("overflow-hidden");
    }
    this.popup.setAttribute("open", "");
    this.classList.add("active");
  }

  hide() {
    playAnimation({
      query: this.inner,
      animation: this.dataset.animation,
      time: this.animationTime,
      direction: "reverse",
      callback: () => {
        document.body.dispatchEvent(new CustomEvent("PortalClosed"));
        if (this.popup) {
          try {
            if (this.popup.open && typeof this.popup.close === "function") {
              this.popup.close();
            }
          } catch (e) {
            /* ignore */
          }
          this.popup.removeAttribute("open");
        }
        this.classList.remove("active", "ww-open");
        if (typeof window.__wwUnlockBodyScroll === "function") {
          window.__wwUnlockBodyScroll(false);
        } else if (!document.querySelector(".portal.active, quick-view.active, quick-view.ww-open")) {
          document.body.classList.remove("overflow-hidden");
          document.documentElement.classList.remove("overflow-hidden");
        }
        if (typeof window.__wwUnlockPageIfIdle === "function") {
          window.__wwUnlockPageIfIdle();
        }
      },
    });
  }
}
defineElement("portal-component", PortalComponent);

class PortalOpener extends HTMLElement {
  constructor() {
    super();

    this.button = this.querySelector("[data-portal]");

    if (!this.button) return;
    this.button.addEventListener("click", this.onClick.bind(this));
  }
  onClick(e) {
    e.preventDefault();
    e.stopPropagation();
    const selector = e.currentTarget.getAttribute("data-portal");
    if (!selector) return;

    const openPortal = () => {
      const portal = document.querySelector(selector);
      if (portal && typeof portal.show === "function") {
        portal.show(this.button);
        return true;
      }
      return false;
    };

    if (openPortal()) return;

    // cart-drawer đôi khi chưa upgrade (miss firstInteraction) — retry ngắn rồi fallback /cart
    let left = 40;
    const timer = setInterval(() => {
      left -= 1;
      if (openPortal() || left <= 0) {
        clearInterval(timer);
        if (left <= 0 && !openPortal()) {
          const href = this.button.getAttribute("href") || "";
          if (href && href !== "#" && href.indexOf("javascript:") !== 0 && !/\.html$/i.test(href)) {
            window.location.href = href;
          }
        }
      }
    }, 50);
  }
}
defineElement("portal-opener", PortalOpener);

// load after interaction
subscribe(window.themeConfigs.firstInteraction, () => {
  class SlideShow extends HTMLElement {
    constructor() {
      super();
      this.slider = this.querySelector("carousel-slider");
    }
    connectedCallback() {
      if (!this.slider) return;
      const media = window.themeConfigs.mbBreakpoint;
      media.addEventListener("change", this.onSlideChange.bind(this));
      this.onSlideChange();
      this.querySelectorAll("carousel-slider  img").forEach((img) => {
        img.removeAttribute("loading");
      });
    }

    onSlideChange() {
      if (this.slider && this.slider.emblaApi) {
        this.slider.emblaApi.on("slidesChanged", (slide) => {
          console.log(slide);
        });
      }
    }
  }
  defineElement("slideshow-component", SlideShow);
  class CopyButton extends HTMLElement {
    constructor() {
      super();
      this.copyBtn = this.querySelector(".copy-button");
      this.input = this.querySelector("input");
      this.copyBtn.addEventListener("click", this.copyToClipboard.bind(this));
      this.addEventListener("click", (e) => e.stopPropagation());
    }
    copyToClipboard(e) {
      e.stopPropagation();
      if (this.classList.contains("copied")) return;
      navigator.clipboard.writeText(this.input.value).then(() => {
        this.classList.add("copied");
        let textBeforeCopy = this.copyBtn.textContent;
        this.copyBtn.textContent = "Đã sao chép";
        setTimeout(() => {
          this.copyBtn.textContent = textBeforeCopy;
          this.classList.remove("copied");
        }, 3000);
      });
    }
  }
  defineElement("copy-button", CopyButton);

  class QuantityInput extends HTMLElement {
    constructor() {
      super();
      this.input = this.querySelector("input");
      this.changeEvent = new Event("change", { bubbles: true });

      this.input.addEventListener("input", this.onInputChange.bind(this));
      this.querySelectorAll("button").forEach((button) =>
        button.addEventListener("click", this.onButtonClick.bind(this))
      );
    }

    quantityUpdateUnsubscriber = undefined;

    connectedCallback() {
      this.validateQtyRules();
      this.quantityUpdateUnsubscriber = subscribe(
        window.themeConfigs.quantityUpdate,
        this.validateQtyRules.bind(this)
      );
    }

    disconnectedCallback() {
      if (this.quantityUpdateUnsubscriber) {
        this.quantityUpdateUnsubscriber();
      }
    }

    onInputChange(event) {
      this.validateQtyRules();
    }

    onButtonClick(event) {
      event.preventDefault();
      const previousValue = this.input.value;
      event.currentTarget.name === "plus"
        ? this.input.stepUp()
        : this.input.stepDown();
      if (previousValue !== this.input.value)
        this.input.dispatchEvent(this.changeEvent);
    }

    validateQtyRules() {
      const value = parseInt(this.input.value);
      if (this.input.min) {
        const min = parseInt(this.input.min);
        const buttonMinus = this.querySelector("[name='minus']");
        buttonMinus.classList.toggle("disabled", value <= min);
        if (value < min) {
          this.input.value = this.input.min;
        }
      }
      if (this.input.max) {
        const max = parseInt(this.input.max);
        const buttonPlus = this.querySelector("[name='plus']");
        buttonPlus.classList.toggle("disabled", value >= max);
        if (value > max) {
          this.input.value = max;
        }
      }

      if (this.input.value.length > 3) {
        this.input.value = this.input.value.substring(0, 2);
      }
    }
  }
  defineElement("quantity-input", QuantityInput);
  class MenuDrawer extends PortalComponent {
    constructor() {
      super();
      this.loadMenu();
    }

     loadMenu() {
       this.querySelectorAll("[data-toggle-submenu]").forEach((el) =>
          el.addEventListener("click", this.toggleSubmenu.bind(this))
        );
    }
    toggleSubmenu(e) {
      e.preventDefault();
      e.stopPropagation();

      const { currentTarget } = e;
      const menuItem = currentTarget.closest(".menu-item");
      const media = window.themeConfigs.lgBreakpoint;

      if (menuItem && media.matches) {
        const submenuElements = menuItem.querySelectorAll(".submenu");
        const isMenuActive = menuItem.classList.contains("menu-active");
        !isMenuActive && menuItem.classList.add("menu-active");
        playAnimation({
          query: submenuElements,
          animation: "slide-in-left",
          time: 400,
          direction: !isMenuActive ? "normal" : "reverse",
          callback: () => {
            if (isMenuActive) {
              menuItem.classList.remove("menu-active");
            }
          },
        });
      }
    }
  }
  defineElement("menu-drawer", MenuDrawer);

  class QuickSearch extends HTMLElement {
    constructor() {
      super();

      this.searchResult = this.querySelector(".search-result");
      this.input = this.querySelector('[name="query"]');
      this.collectionOptions = this.querySelector(".collection-options");
      this.collectionId = this.collectionOptions
        ? this.collectionOptions.value
        : "";
      this.query = "";
      this.storageKey = window.themeConfigs.searchStorage;
      this.input.addEventListener("input", (e) => {
        setTimeout(() => {
          this.onChange(e);
        }, 500);
      });
      this.collectionOptions &&
        this.collectionOptions.addEventListener(
          "change",
          this.onOptionSelect.bind(this)
        );
      this.querySelector("form").addEventListener(
        "submit",
        this.onSubmit.bind(this)
      );
      this.renderHistory();
    }
    connectedCallback() {
      this.changePosition();
    }
    changePosition() {
      let searchDrawer = document.querySelector("#search-drawer .search-bar");
      let currentPositon = document.querySelector("header .search-bar");
      if (!document.querySelector("#search-drawer .quick-search")) {
        let clone = this.cloneNode(true);
        searchDrawer.appendChild(clone);
      }
    }

    onSubmit(e) {
      this.addHistory();
    }
    onOptionSelect(e) {
      this.collectionId = e.currentTarget.value;
      this.onChange({ target: { value: this.query } });
    }
    onChange(e) {
      let value = e.target ? e.target.value.trim() : e;
      if (e.target && this.query == value) return;

      this.query = value;
      if (!this.query) {
        this.searchResult.innerHTML = "";
        this.classList.remove("loading", "loaded");
        return;
      }
      if (!this.searchResult) return;
      this.classList.add("loading");
      const base = window.themeUrl ? window.themeUrl("/search") : "/search";
      let url = `${base}?query=${encodeURIComponent(this.query)}&type=product&view=quick-search`;
      if (this.collectionId) {
        url += `&category_id=${encodeURIComponent(this.collectionId)}`;
      }
      fetch(url)
        .then((response) => response.text())
        .then((res) => {
          if (this.searchResult.innerHTML != res) {
            this.searchResult.innerHTML = res.replace("[query]", this.query);
          }
          this.classList.remove("loading");
          this.classList.add("loaded");
        })
        .catch((e) => {
          this.classList.remove("loading");
        });
    }
    getHistory() {
      return JSON.parse(localStorage.getItem(this.storageKey)) || [];
    }
    removeHistory(e) {
      e.stopPropagation();
      e.preventDefault();
      let history = this.getHistory();
      let removeText = e.currentTarget.dataset.remove;
      history = history.filter((key) => key != removeText);
      if (!history.length) localStorage.removeItem(this.storageKey);
      localStorage.setItem(this.storageKey, JSON.stringify(history));
      this.renderHistory();
    }
    renderHistory() {
      let history = this.getHistory();
      let historyList = history.slice(0, 5).map((keyword) => {
        const searchHref = (window.themeUrl || ((p) => p))(
          `/tim-kiem/${encodeURIComponent(keyword)}`
        );
        return `<a class="search-history-item cursor-pointer py-2 flex items-center gap-2 px-2 hover:bg-neutral-50 rounded-sm  " href="${searchHref}" data-text="${keyword}">
			  <i class="icon icon-search-history text-h6 text-neutral-100"></i>
			  <span>${keyword}</span>
			  <button data-remove="${keyword}" class="ml-auto opacity-50 hover:opacity-100" aria-label="Xóa"><i class="icon icon-cross text-h6 text-neutral-200"></i></button>
			</a>`;
      });
      if (!this.querySelector(".search-history-list")) return;
      this.querySelector(".search-history-list").innerHTML =
        historyList.join("");
      this.querySelectorAll("[data-remove]").forEach((el) =>
        el.addEventListener("click", this.removeHistory.bind(this))
      );
    }

    addHistory() {
      if (!this.query) return;
      let history = this.getHistory();
      history = history.filter((key) => key != this.query);
      history.unshift(this.query);
      history = history.slice(0, 5);
      localStorage.setItem(this.storageKey, JSON.stringify(history));
      this.renderHistory();
    }
  }

  defineElement("quick-search", QuickSearch);

  class TabsSection extends TabsComponent {
    constructor() {
      super();
    }
    connectedCallback() {
      this.activeTab = this.querySelector(".tab-btn.active");
      if (this.activeTab) {
        this.onTabClick({ currentTarget: this.activeTab });
      }
    }
    async getContent(contentUrl, tab) {
      if (!contentUrl && !tab) return;
      if (tab.classList.contains("loaded")) return;
      tab.classList.add("loading");
      const response = await fetch(contentUrl);
      const res = await response.text();
      tab.classList.remove("loading");
      tab.classList.add("loaded");
      const html = new DOMParser().parseFromString(res, "text/html");
      const limit = parseInt(tab.dataset.limit);
      const itemSelector = this.dataset.type;
      if (itemSelector && html.querySelector(itemSelector)) {
        const cardProducts = Array.from(
          html.querySelectorAll(itemSelector)
        ).slice(0, limit);
        const tabContent = document.createElement("Div");
        tab.querySelector(".tab-content-inner").innerHTML = "";
		setTimeout(()=>{
        cardProducts.forEach((card) => {
          tab.querySelector(".tab-content-inner").appendChild(card);
        });
			publish("EGA:product-loaded", {
			  data: html,
			});
		},200)
      } else {
        tab.querySelector(".tab-content-inner").innerHTML =
          html.querySelector(".data").innerHTML;
      }
    }
    onTabClick(e) {
      super.onTabClick(e);
      const { url, contentUrl } = this.activeTab.dataset || "";
      this.querySelectorAll(".tab-url").forEach((el) => {
        el.href = url;
      });
      if (contentUrl) {
        this.getContent(contentUrl, this.activeTab);
      }
    }
  }
  defineElement("tabs-section", TabsSection);

  class PromoPopup extends PortalComponent {
    constructor() {
      super();
      this.storageKey = "egaPromoPopuptorage";
    }
    connectedCallback() {
      !sessionStorage.getItem(this.storageKey) && this.show();
      sessionStorage.setItem(this.storageKey, true);
    }
  }

  defineElement("promo-popup", PromoPopup);

  /** Popup mời đăng nhập khi khách mới vào (chưa có phiên đăng nhập). */
  class LoginInvitePopup extends PortalComponent {
    constructor() {
      super();
      this.storageKey = this.dataset.storageKey || "wwLoginInviteSeen";
      this._shown = false;
    }
    connectedCallback() {
      if (this._shown) return;
      this._shown = true;
      try {
        if (sessionStorage.getItem(this.storageKey)) return;
        sessionStorage.setItem(this.storageKey, "1");
      } catch (e) {
        /* private mode: vẫn hiện 1 lần trong lần load này */
      }
      window.setTimeout(() => {
        if (typeof this.show === "function") this.show();
      }, 500);
    }
  }

  defineElement("login-invite-popup", LoginInvitePopup);

  class VideoReview extends HTMLElement {
    constructor() {
      super();
      this.videos = this.querySelectorAll(".review-video__item");
      this.videos.forEach((el) => {
        el.addEventListener("click", () => {
          this.lightbox(el);
        });
      });
    }
    lightbox(el) {
      let index = parseInt(el.dataset.index) + 1;
      if (!Spotlight) return;
      let imageList = Array.from(this.videos).map((el) => {
        if (el.dataset.video) {
          let src = el.dataset.video;
          return {
            media: "video",
            "src-webm": src,
            "src-ogg": src,
            "src-mp4": src,
            poster: el.querySelector("img").src,
            autoplay: true,
            muted: true,
            preload: true,
            controls: true,
            inline: true,
          };
        }
        if (el.dataset.videoUrl) {
          let src = el.dataset.videoUrl;
          let videoId = this.getYouTubeVideoId(src);
          if (videoId) {
            let url = `https://www.youtube.com/embed/${videoId}?enablejsapi=1&autoplay=1`;
            let type =
              src.indexOf("youtube.com/shorts/") !== -1 ? "sort" : "video";
            return {
              media: "node",
              src: (function () {
                const iframe = document.createElement("iframe");
                iframe.allow =
                  "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
                if (type == "sort") {
                  iframe.classList.add(
                    "max-w-[375px]",
                    "w-full",
                    "aspect-[9/16]",
                    "iframe",
                    "container"
                  );
                } else {
                  iframe.classList.add("aspect-video", "iframe", "container");
                }
                iframe.src = url;
                iframe.dataset.src = iframe.src;
                iframe.wdith = 560;
                iframe.height = 315;
                iframe.setAttribute("frameborder", "0");
                iframe.setAttribute("autoplay", "");
                iframe.setAttribute("controls", "");
                iframe.setAttribute("playsinline", "");
                iframe.setAttribute("allowfullscreen", "");
                return iframe;
              })(),
            };
          }
          videoId = this.getTikTokVideoId(src);
          if (videoId) {
            let url = `https://www.tiktok.com/embed/v2/${videoId}`;
            return {
              media: "node",
              src: (function () {
                const iframe = document.createElement("iframe");
                iframe.allow =
                  "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
                iframe.classList.add("max-w-[315px]", "iframe");
                iframe.src = url;
                iframe.dataset.src = iframe.src;
                iframe.wdith = 560;
                iframe.height = 315;
                iframe.setAttribute("frameborder", "0");
                iframe.setAttribute("allowfullscreen", "true");
                iframe.setAttribute("muted", "false");
                iframe.setAttribute(
                  "sandbox",
                  "allow-popups allow-popups-to-escape-sandbox allow-scripts allow-top-navigation allow-same-origin"
                );

                return iframe;
              })(),
            };
          }
        }
      });
      Spotlight.show(imageList.filter(Boolean), {
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
      Spotlight.goto(index);
    }
    connectedCallback() {
      // Add your custom logic here
    }
    getTikTokVideoId(url) {
      const regex = /https?:\/\/(?:www\.)?tiktok\.com\/@[^/]+\/video\/(\d+)/;

      const match = url.match(regex);

      return match ? match[1] : null;
    }

    getYouTubeVideoId(url) {
      let videoId = "";
      // Extract video ID from different YouTube URL formats
      if (url.indexOf("youtube.com/watch?v=") !== -1) {
        // Example: https://www.youtube.com/watch?v=VIDEO_ID
        let match = url.match(/youtube\.com\/watch\?v=([^&]+)/);
        if (match) {
          videoId = match[1];
        }
      } else if (url.indexOf("youtu.be/") !== -1) {
        // Example: https://youtu.be/VIDEO_ID
        let match = url.match(/youtu\.be\/([^&]+)/);
        if (match) {
          videoId = match[1];
        }
      } else if (url.indexOf("youtube.com/embed/") !== -1) {
        // Example: https://www.youtube.com/embed/VIDEO_ID
        let match = url.match(/youtube\.com\/embed\/([^&]+)/);
        if (match) {
          videoId = match[1];
        }
      } else if (url.indexOf("youtube.com/shorts/") !== -1) {
        // Example: https://www.youtube.com/shorts/VIDEO_ID
        let match = url.match(/youtube\.com\/shorts\/([^&]+)/);
        if (match) {
          videoId = match[1];
        }
      }
      if (videoId.indexOf("?") !== -1) {
        videoId = videoId.split("?")[0];
      }

      return videoId;
    }
  }

  defineElement("video-review", VideoReview);
  class SectionAjax extends HTMLElement {
    constructor() {
      super();
	  this.section = this.dataset.section;
      this.loadContent();
    }
    async loadContent() {
	  if(!this.section) return;
      let url = `/search?q=${this.section}&view=section`
      if (!url) return;
      const response = await fetch(url);
      const res = await response.text();
		if(res){
        	this.innerHTML = res
		}
    }
  }
  defineElement("section-ajax", SectionAjax);
});
class ExpandableContent extends HTMLElement {
  constructor() {
    super();
    this.isExpanded = false;
    this.container = this.querySelector(".expandable-content");
    this.maxHeight = parseInt(this.getAttribute("max-height") || 200);
  }

  toggleHeight() {
    this.isExpanded = !this.isExpanded;
    const buttonText = this.isExpanded
      ? 'Thu gọn <i class="icon icon-carret-up"></i>'
      : 'Xem thêm <i class="icon icon-carret-down"></i>';
    this.button.innerHTML = buttonText;

    if (this.isExpanded) {
      this.contentElement.style.maxHeight = "none";
      this.container.classList.add("show-all");
    } else {
      this.contentElement.style.maxHeight = this.maxHeight + "px";
      this.container.classList.remove("show-all");
    }
  }
  init() {
    this.contentElement = this.querySelector(".content");
    const contentHeight = this.contentElement.scrollHeight;
    const button = document.createElement("button");
    this.button = button;
    this.isExpanded = false;
    if (isNaN(this.maxHeight)) {
      this.container.classList.add("show-all");
      return;
    }
    button.innerHTML = 'Xem thêm <i class="icon icon-carret-down"></i>';
    button.addEventListener("click", this.toggleHeight.bind(this));
    button.classList.add("btn", "btn-showmore");
    if (this.querySelector(".btn-showmore")) {
      this.querySelector(".btn-showmore").remove();
    }
    this.appendChild(button);
    if (this.maxHeight < 0 || contentHeight < this.maxHeight) {
      this.button.style.display = "none";
      this.container.classList.add("show-all");
    } else {
      this.container.classList.remove("show-all");
      this.button.style.display = "flex";
      this.contentElement.style.maxHeight = this.maxHeight + "px";
    }
  }
  connectedCallback() {
    this.init();
    subscribe("EGA:tab-update", (e) => {
      if (
        e.activeTab &&
        e.activeTab.querySelector("expandable-content") == this
      ) {
        this.init();
      }
    });
  }
}
defineElement("expandable-content", ExpandableContent);

class ErrorPopup extends PortalComponent {
  constructor() {
    super();
    this.errorList = [];
  }
  connectedCallback() {
    subscribe(window.themeConfigs.error, (e) => {
      console.log(e);
      if (e && e.error) {
        if (e.error.name == "AbortError") return;
        this.errorList.push(e.error);
        this.renderError();
      }
    });
  }
  renderError() {
    let errors = this.errorList.map((e, i) => {
      return `<div class="error-item inline-flex w-auto items-start gap-1 bg-background text-error font-semibold rounded-sm px-3 py-2">
				${e.message || "Đã có lỗi xảy ra"}
						<button class="btn p-0 remove-error-btn" data-index="${i}">  <i class="icon icon-cross"> </i></button>

			</div>`;
    });
    this.querySelector(".error-list").innerHTML = errors.join("");
    this.querySelectorAll(".remove-error-btn").forEach((el) =>
      el.addEventListener("click", this.removeError.bind(this))
    );

    this.show();
    setTimeout(() => this.hide(), 3000);
  }
  removeError(e) {
    let index = e.currentTarget.dataset.index;
    this.errorList.splice(index, 1);
    this.renderError();
    if (this.errorList.length == 0) {
      this.hide();
    }
  }
  hide() {
    super.hide();
    this.errorList = [];
    this.querySelector(".error-list").innerHTML = "";
  }
}

defineElement("error-popup", ErrorPopup);

class EmblaComponent extends HTMLElement {
  constructor() {
    super();
  }
  addDotBtnsAndClickHandlers(emblaApi, dotsNode) {
    if (!dotsNode) return;
    let dotNodes = [];

    const addDotBtnsWithClickHandlers = () => {
      const dots = emblaApi.scrollSnapList();
      if (dots && dots.length > 1) {
        dotsNode.innerHTML = dots
          .map(() => '<div class="embla__dot" ></div>')
          .join("");

        const scrollTo = (index) => {
          emblaApi.scrollTo(index);
        };

        dotNodes = Array.from(dotsNode.querySelectorAll(".embla__dot"));
        dotNodes.forEach((dotNode, index) => {
          dotNode.addEventListener("click", () => scrollTo(index), false);
        });
      }
    };

    const toggleDotBtnsActive = () => {
      const previous = emblaApi.previousScrollSnap();
      const selected = emblaApi.selectedScrollSnap();
      dotNodes[previous]?.classList.remove("embla__dot--selected");
      dotNodes[selected]?.classList.add("embla__dot--selected");
    };

    emblaApi
      .on("init", addDotBtnsWithClickHandlers)
      .on("reInit", addDotBtnsWithClickHandlers)
      .on("init", toggleDotBtnsActive)
      .on("reInit", toggleDotBtnsActive)
      .on("select", toggleDotBtnsActive);

    return () => {
      dotsNode.innerHTML = "";
    };
  }

  addTogglePrevNextBtnsActive(emblaApi, prevBtn, nextBtn) {
    const togglePrevNextBtnsState = () => {
      if (!prevBtn || !nextBtn) return;
      const snaps = typeof emblaApi.scrollSnapList === "function"
        ? emblaApi.scrollSnapList()
        : [];
      const multi = snaps.length > 1;
      const idx =
        typeof emblaApi.selectedScrollSnap === "function"
          ? emblaApi.selectedScrollSnap()
          : 0;

      const wrap = prevBtn.closest(".embla__buttons");
      if (wrap) {
        wrap.style.display = multi ? "" : "none";
        wrap.hidden = !multi;
      }

      if (!multi || idx <= 0) prevBtn.setAttribute("disabled", "disabled");
      else prevBtn.removeAttribute("disabled");

      if (!multi || idx >= snaps.length - 1)
        nextBtn.setAttribute("disabled", "disabled");
      else nextBtn.removeAttribute("disabled");
    };

    emblaApi
      .on("select", togglePrevNextBtnsState)
      .on("init", togglePrevNextBtnsState)
      .on("reInit", togglePrevNextBtnsState)
      .on("settle", togglePrevNextBtnsState);

    // Đồng bộ ngay — tránh prev còn “mở” khi mới vào trang (ảnh đầu)
    togglePrevNextBtnsState();
    requestAnimationFrame(togglePrevNextBtnsState);

    return () => {
      if (typeof emblaApi.off === "function") {
        emblaApi.off("select", togglePrevNextBtnsState);
        emblaApi.off("init", togglePrevNextBtnsState);
        emblaApi.off("reInit", togglePrevNextBtnsState);
        emblaApi.off("settle", togglePrevNextBtnsState);
      }
      prevBtn.removeAttribute("disabled");
      nextBtn.removeAttribute("disabled");
    };
  }

  addPrevNextBtnsClickHandlers(emblaApi, prevBtn, nextBtn) {
    if (!prevBtn || !nextBtn) return;
    const scrollPrev = () => {
      emblaApi.scrollPrev();
    };
    const scrollNext = () => {
      emblaApi.scrollNext();
    };
    prevBtn.addEventListener("click", scrollPrev, false);
    nextBtn.addEventListener("click", scrollNext, false);

    const removeTogglePrevNextBtnsActive = this.addTogglePrevNextBtnsActive(
      emblaApi,
      prevBtn,
      nextBtn
    );

    return () => {
      removeTogglePrevNextBtnsActive();
      prevBtn.removeEventListener("click", scrollPrev, false);
      nextBtn.removeEventListener("click", scrollNext, false);
    };
  }

  addThumbBtnsClickHandlers(emblaApiMain, emblaApiThumb) {
    const slidesThumbs = emblaApiThumb.slideNodes();

    const scrollToIndex = slidesThumbs.map(
      (_, index) => () => emblaApiMain.scrollTo(index)
    );

    slidesThumbs.forEach((slideNode, index) => {
      slideNode.addEventListener("click", scrollToIndex[index], false);
    });

    return () => {
      slidesThumbs.forEach((slideNode, index) => {
        slideNode.removeEventListener("click", scrollToIndex[index], false);
      });
    };
  }
  addToggleThumbBtnsActive(emblaApiMain, emblaApiThumb) {
    const toggleThumbBtnsState = () => {
      const slidesThumbs = emblaApiThumb.slideNodes();
      const selected = emblaApiMain.selectedScrollSnap();
      if (!slidesThumbs.length) return;
      const idx = Math.min(Math.max(0, selected), slidesThumbs.length - 1);
      emblaApiThumb.scrollTo(idx);
      slidesThumbs.forEach((slide, i) => {
        slide.classList.toggle("embla-thumbs__slide--selected", i === idx);
      });
    };

    emblaApiMain.on("select", toggleThumbBtnsState);
    emblaApiThumb.on("init", toggleThumbBtnsState);
    emblaApiThumb.on("reInit", toggleThumbBtnsState);
    toggleThumbBtnsState();

    return () => {
      if (typeof emblaApiMain.off === "function") {
        emblaApiMain.off("select", toggleThumbBtnsState);
        emblaApiThumb.off("init", toggleThumbBtnsState);
        emblaApiThumb.off("reInit", toggleThumbBtnsState);
      }
    };
  }
}

class CarouselSlider extends EmblaComponent {
  constructor() {
    super();
    this.emblaNode = this.querySelector(".embla__viewport");
    this.options = { loop: false, dragFree: true, align: "start" };
    this.dotsNode = this.querySelector(".embla__dots");
    this.prevBtnNode = this.querySelector(".embla__button--prev");
    this.nextBtnNode = this.querySelector(".embla__button--next");
    let options = this.querySelector('[data-type="carousel-options"]');
    this.pluginOptions = [];
    if (options && JSON.parse(options.textContent)) {
      options = JSON.parse(options.textContent) || {};
      this.options = {
        ...options
      }
    }
	  if(this.dataset.disabledDrag){
	  	  this.options.dragFree = false;
	  
	  }
    if (this.dataset.autoplay) {
      this.options.loop = true;
	  this.options.dragFree = false;
      this.pluginOptions.push(
        EmblaCarouselAutoplay({
          delay: parseInt(this.dataset.autoplay) || 5000,
        })
      );
    }
	  if(this.querySelector(".embla__slide")){
	          this.init();
	  }

  }
  connectedCallback() {
    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && this.querySelector(".embla__slide")) {
        observer.disconnect();
      }
    });
    observer.observe(this);
  }
  init() {
    if (
      !this.emblaNode ||
      !this.querySelector(".embla__slide") ||
      this.querySelectorAll(".embla__slide").length === 0
    ) {
      return;
    }
    this.emblaApi = EmblaCarousel(
      this.emblaNode,
      this.options,
      this.pluginOptions
    );

   if(this.querySelectorAll(".embla__slide").length <= 1 && this.prevBtnNode && this.nextBtnNode ){
        this.prevBtnNode.style.display = 'none'
        this.nextBtnNode.style.display = 'none'

  }
    this.addDotBtnsAndClickHandlers(this.emblaApi, this.dotsNode);
    this.addPrevNextBtnsClickHandlers(
      this.emblaApi,
      this.prevBtnNode,
      this.nextBtnNode
    );
	 
  }
}

defineElement("carousel-slider", CarouselSlider);

 class ShareButton extends HTMLElement {
    constructor() {
      super();
      this.addEventListener('click', this.shareContent);
    }
    connectedCallback() {
      if (!navigator.share) {
        this.style.display = 'none';
      }
    }

    async shareContent() {
      if (navigator.share) {
        try {
          await navigator.share({
            title: document.title,
            url: window.location.href
          });
        } catch (error) {
          console.log('Error sharing:', error);
        }
      } else {
        console.log('Web Share API not supported');
      }
    }
  }

  customElements.define('share-button', ShareButton);