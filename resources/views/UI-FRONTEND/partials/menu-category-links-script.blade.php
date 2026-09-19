<script>
(function () {
  var categoryUrlMap = @json(storefrontProductCategoryUrlMap());
  var gioQuaPriceUrlMap = @json(storefrontGioQuaPriceUrlMap());
  var toyMenuTitle = 'Đồ chơi trẻ em';
  var PAD = 12;
  var SUB_W = 280;
  /** Mọi menu trái desktop (trang chủ / tất cả SP / tìm kiếm / danh mục). */
  var SIDEBAR_ITEM = '.ww-home-sidebar .navigation-vertical .menu-item.group';

  function wireMenuCategoryLinks() {
    document.querySelectorAll('.navigation-vertical a[title]').forEach(function (anchor) {
      var title = (anchor.getAttribute('title') || '').replace(/\s+/g, ' ').trim();
      if (!title) return;

      if (title === toyMenuTitle) {
        anchor.setAttribute('href', 'https://dochoiwinwin.com');
        anchor.setAttribute('target', '_blank');
        anchor.setAttribute('rel', 'noopener noreferrer');
        anchor.removeAttribute('data-prefetch');
        return;
      }

      var targetUrl = gioQuaPriceUrlMap[title] || categoryUrlMap[title];
      if (!targetUrl) return;

      anchor.setAttribute('href', targetUrl);
      anchor.removeAttribute('target');
      anchor.removeAttribute('rel');

      try {
        var parsed = new URL(targetUrl, window.location.origin);
        anchor.setAttribute('data-prefetch', parsed.pathname + parsed.search);
      } catch (e) {
        anchor.removeAttribute('data-prefetch');
      }
    });
  }

  function isDesktopNav() {
    return window.matchMedia('(min-width: 1280px)').matches;
  }

  function menuItemFromEventTarget(target) {
    if (!target || !target.closest) return null;
    var li = target.closest(SIDEBAR_ITEM);
    if (!li || li.closest('#menu-drawer')) return null;
    if (!li.querySelector(':scope > .submenu')) return null;
    return li;
  }

  function headerBottom() {
    var bottom = PAD;
    var header = document.querySelector('.header');
    if (header) {
      var hr = header.getBoundingClientRect();
      if (hr.height > 0) {
        if (hr.bottom <= 0 && (header.classList.contains('active') || getComputedStyle(header).position === 'fixed')) {
          bottom = Math.max(bottom, header.offsetHeight || hr.height);
        } else if (hr.bottom > 0) {
          bottom = Math.max(bottom, hr.bottom);
        }
      }
    }
    var subHeader = document.querySelector('.navigation-wrapper');
    if (subHeader) {
      var nr = subHeader.getBoundingClientRect();
      if (nr.bottom > 0) {
        bottom = Math.max(bottom, nr.bottom);
      }
    }
    return Math.round(bottom) + 4;
  }

  function placeSubmenu(li) {
    if (!li || !isDesktopNav()) return;
    var sub = li.querySelector(':scope > .submenu');
    if (!sub) return;

    var rect = li.getBoundingClientRect();
    var vw = window.innerWidth;
    var vh = window.innerHeight;
    var minTop = headerBottom();

    var left = Math.round(rect.right - 2);
    if (left + SUB_W > vw - PAD) {
      left = Math.max(PAD, Math.round(rect.left - SUB_W + 2));
    }

    var maxH = Math.max(160, Math.min(vh - minTop - PAD, 448));

    // Đo chiều cao thật (submenu đã hiện khi hover); fallback ước lượng nếu chưa render
    sub.style.setProperty('max-height', maxH + 'px', 'important');
    var itemCount = sub.querySelectorAll('.submenu__item').length || 4;
    var contentH = sub.scrollHeight || (24 + itemCount * 40);
    var panelH = Math.min(contentH, maxH);

    var top = Math.round(rect.top);
    if (top + panelH > vh - PAD) {
      top = Math.round(vh - PAD - panelH);
    }
    if (top < minTop) {
      top = minTop;
    }
    maxH = Math.max(160, Math.min(maxH, vh - top - PAD));

    sub.style.setProperty('top', top + 'px', 'important');
    sub.style.setProperty('left', left + 'px', 'important');
    sub.style.setProperty('max-height', maxH + 'px', 'important');
  }

  function clearSubmenu(li) {
    if (!li) return;
    var sub = li.querySelector(':scope > .submenu');
    if (!sub) return;
    sub.style.removeProperty('top');
    sub.style.removeProperty('left');
    sub.style.removeProperty('max-height');
  }

  function hoverSidebarItems() {
    return document.querySelectorAll(SIDEBAR_ITEM + ':hover');
  }

  function wireDesktopSubmenus() {
    if (window.__wwDesktopSubmenuWired) return;
    window.__wwDesktopSubmenuWired = true;

    document.addEventListener('mouseover', function (e) {
      if (!isDesktopNav()) return;
      var li = menuItemFromEventTarget(e.target);
      if (!li) return;
      placeSubmenu(li);
    }, true);

    document.addEventListener('mouseout', function (e) {
      if (!isDesktopNav()) return;
      var li = menuItemFromEventTarget(e.target);
      if (!li) return;
      var to = e.relatedTarget;
      if (to && li.contains(to)) return;
      clearSubmenu(li);
    }, true);

    window.addEventListener('scroll', function () {
      if (!isDesktopNav()) return;
      hoverSidebarItems().forEach(placeSubmenu);
    }, true);

    window.addEventListener('resize', function () {
      if (!isDesktopNav()) return;
      hoverSidebarItems().forEach(placeSubmenu);
    });
  }

  function boot() {
    wireMenuCategoryLinks();
    wireDesktopSubmenus();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
