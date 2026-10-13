<script>
(function () {
  var categoryUrlMap = @json(storefrontProductCategoryUrlMap());
  var gioQuaPriceUrlMap = @json(storefrontGioQuaPriceUrlMap());
  var toyMenuTitle = 'Đồ chơi trẻ em';
  var PAD = 12;
  var SUB_W = 280;
<<<<<<< HEAD
  var BRIDGE = 20;
  var CLOSE_DELAY = 220;
  var flyoutSeq = 0;
  var hideTimer = null;
  var openLi = null;
=======
>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
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

<<<<<<< HEAD
  function flyoutId(li) {
    if (!li.getAttribute('data-ww-flyout-id')) {
      flyoutSeq += 1;
      li.setAttribute('data-ww-flyout-id', 'ww-flyout-' + flyoutSeq);
    }
    return li.getAttribute('data-ww-flyout-id');
  }

  function getSubmenu(li) {
    if (!li) return null;
    var nested = li.querySelector(':scope > .submenu');
    if (nested) return nested;
    var id = li.getAttribute('data-ww-flyout-id');
    if (!id) return null;
    return document.querySelector('body > .submenu[data-ww-flyout="' + id + '"]');
  }

=======
>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
  function menuItemFromEventTarget(target) {
    if (!target || !target.closest) return null;
    var li = target.closest(SIDEBAR_ITEM);
    if (!li || li.closest('#menu-drawer')) return null;
<<<<<<< HEAD
    if (!getSubmenu(li)) return null;
    return li;
  }

  function ownerFromSubmenu(target) {
    if (!target || !target.closest) return null;
    var sub = target.closest('.submenu[data-ww-flyout]');
    if (!sub || sub.closest('#menu-drawer')) return null;
    var id = sub.getAttribute('data-ww-flyout');
    return document.querySelector(SIDEBAR_ITEM + '[data-ww-flyout-id="' + id + '"]');
  }

  function relatedKeepsOpen(li, related) {
    if (!related || !related.closest) return false;
    if (li.contains(related)) return true;
    var sub = getSubmenu(li);
    return !!(sub && sub.contains(related));
  }

=======
    if (!li.querySelector(':scope > .submenu')) return null;
    return li;
  }

>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
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
<<<<<<< HEAD
    var sub = getSubmenu(li);
    if (!sub) return;

    var id = flyoutId(li);
    sub.setAttribute('data-ww-flyout', id);
    if (sub.parentElement !== document.body) {
      document.body.appendChild(sub);
    }

=======
    var sub = li.querySelector(':scope > .submenu');
    if (!sub) return;

>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
    var rect = li.getBoundingClientRect();
    var vw = window.innerWidth;
    var vh = window.innerHeight;
    var minTop = headerBottom();

<<<<<<< HEAD
    var left = Math.round(rect.right - BRIDGE);
    if (left + SUB_W > vw - PAD) {
      left = Math.max(PAD, Math.round(rect.left - SUB_W + BRIDGE));
    }

    var top = Math.round(rect.top);
    if (top < minTop) {
      top = minTop;
    }
    var maxH = Math.max(160, Math.min(vh - top - PAD, 448));
=======
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
>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236

    sub.style.setProperty('top', top + 'px', 'important');
    sub.style.setProperty('left', left + 'px', 'important');
    sub.style.setProperty('max-height', maxH + 'px', 'important');
<<<<<<< HEAD
    sub.classList.add('is-submenu-open');
    li.classList.add('is-submenu-open');
  }

  function restoreSubmenu(li) {
    if (!li) return;
    var sub = getSubmenu(li);
    li.classList.remove('is-submenu-open');
    if (!sub) return;
    sub.classList.remove('is-submenu-open');
    sub.style.removeProperty('top');
    sub.style.removeProperty('left');
    sub.style.removeProperty('max-height');
    if (sub.parentElement === document.body) {
      li.appendChild(sub);
    }
  }

  function openSubmenu(li) {
    if (!li) return;
    if (hideTimer) {
      clearTimeout(hideTimer);
      hideTimer = null;
    }
    if (openLi && openLi !== li) {
      restoreSubmenu(openLi);
    }
    openLi = li;
    placeSubmenu(li);
  }

  function scheduleClose(li) {
    if (!li) return;
    if (hideTimer) clearTimeout(hideTimer);
    hideTimer = setTimeout(function () {
      hideTimer = null;
      if (openLi !== li) return;
      restoreSubmenu(li);
      openLi = null;
    }, CLOSE_DELAY);
=======
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
>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
  }

  function wireDesktopSubmenus() {
    if (window.__wwDesktopSubmenuWired) return;
    window.__wwDesktopSubmenuWired = true;

    document.addEventListener('mouseover', function (e) {
      if (!isDesktopNav()) return;
<<<<<<< HEAD
      var li = menuItemFromEventTarget(e.target) || ownerFromSubmenu(e.target);
      if (!li) return;
      openSubmenu(li);
=======
      var li = menuItemFromEventTarget(e.target);
      if (!li) return;
      placeSubmenu(li);
>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
    }, true);

    document.addEventListener('mouseout', function (e) {
      if (!isDesktopNav()) return;
<<<<<<< HEAD
      var li = menuItemFromEventTarget(e.target) || ownerFromSubmenu(e.target);
      if (!li || li !== openLi) return;
      if (relatedKeepsOpen(li, e.relatedTarget)) return;
      scheduleClose(li);
    }, true);

    window.addEventListener('scroll', function () {
      if (!isDesktopNav()) {
        if (openLi) {
          restoreSubmenu(openLi);
          openLi = null;
        }
        return;
      }
      if (openLi) placeSubmenu(openLi);
    }, true);

    window.addEventListener('resize', function () {
      if (!isDesktopNav()) {
        if (openLi) {
          restoreSubmenu(openLi);
          openLi = null;
        }
        return;
      }
      if (openLi) placeSubmenu(openLi);
=======
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
>>>>>>> 0e5565fcb9e5bd22bdf9d6302398e7fed4801236
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
