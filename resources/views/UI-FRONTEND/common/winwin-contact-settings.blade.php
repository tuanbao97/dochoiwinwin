{{-- Bootstrap từ DB + refresh qua API setting web → window.__wwContact --}}
@php
  $wwBoot = wwWebContact();
  $wwBootJs = array_merge($wwBoot, [
    'ready' => true,
    'loading' => false,
    'aboutHtml' => '',
    'commitmentHtml' => '',
  ]);
@endphp
<script>
(function () {
  var boot = @json($wwBootJs);

  if (window.__wwContact && window.__wwContact.ready && !window.__wwContact.fromBoot) {
    applyWwContact(window.__wwContact);
    return;
  }
  if (window.__wwContact && window.__wwContact.loading) {
    document.addEventListener('ww:contact-ready', function once() {
      document.removeEventListener('ww:contact-ready', once);
      applyWwContact(window.__wwContact);
    });
    return;
  }

  boot.fromBoot = true;
  window.__wwContact = boot;

  function formatPhoneNumber(phone) {
    var digits = String(phone || '').replace(/\D/g, '');
    if (digits.length <= 7) return digits;
    return digits.slice(0, 4) + ' ' + digits.slice(4, 7) + ' ' + digits.slice(7);
  }

  function telHref(digits) {
    return 'tel:' + String(digits || '').replace(/\D/g, '');
  }

  function filterHotline(settings) {
    return (settings || [])
      .filter(function (item) {
        return item.CODE && String(item.CODE).indexOf('SETTING_HOTLINE') === 0 && item.VALUE;
      })
      .sort(function (a, b) {
        var oa = a.ORDER == null ? 9999 : Number(a.ORDER);
        var ob = b.ORDER == null ? 9999 : Number(b.ORDER);
        return oa - ob;
      });
  }

  function parseHotlineValue(value) {
    var parts = String(value || '').split('|');
    if (parts.length < 2) return null;
    var raw = parts[1];
    return {
      display: formatPhoneNumber(raw),
      tel: telHref(raw),
      raw: String(raw).replace(/\D/g, ''),
      owner: parts[2] || '',
      type: parts[0] || '',
    };
  }

  function settingValue(settings, code) {
    var item = (settings || []).find(function (s) {
      return String(s.CODE || '').toUpperCase() === code;
    });
    return item && item.VALUE != null ? String(item.VALUE).trim() : '';
  }

  function setTextSlots(slot, text) {
    if (!text) return;
    document.querySelectorAll('[data-ww-contact-slot="' + slot + '"]').forEach(function (el) {
      el.textContent = text;
    });
  }

  function setHtmlSlots(slot, html) {
    if (!html) return;
    document.querySelectorAll('[data-ww-contact-slot="' + slot + '"]').forEach(function (el) {
      el.innerHTML = html;
    });
  }

  function setLinkGroup(attr, url, opts) {
    opts = opts || {};
    document.querySelectorAll('[data-ww-contact="' + attr + '"]').forEach(function (el) {
      var wrap = el.closest('[data-ww-social]') || (el.hasAttribute('data-ww-social') ? el : null);
      if (!url) {
        if (wrap) wrap.hidden = true;
        return;
      }
      if (wrap) wrap.hidden = false;
      if (el.tagName === 'IFRAME') {
        el.setAttribute('src', url);
        el.removeAttribute('data-src');
        el.removeAttribute('data-map-loaded');
        return;
      }
      el.href = url;
      if (opts.external !== false && /^https?:/i.test(url)) {
        el.target = '_blank';
        el.rel = 'noopener noreferrer';
      }
      if (opts.title) el.title = opts.title;
      if (opts.fillText && el.hasAttribute('data-ww-contact-fill-text')) {
        el.textContent = opts.fillText;
      }
    });
  }

  function buildContact(settings) {
    var hotlines = filterHotline(settings)
      .map(function (item) {
        return parseHotlineValue(item.VALUE);
      })
      .filter(Boolean);
    return {
      ready: true,
      loading: false,
      hotline: hotlines[0] || null,
      hotlines: hotlines,
      storeName: settingValue(settings, 'SETTING_TEN_CUA_HANG'),
      email: settingValue(settings, 'SETTING_EMAIL'),
      taxCode: settingValue(settings, 'SETTING_MA_SO_THUE'),
      workingHours: settingValue(settings, 'SETTING_THOI_GIAN_LAM_VIEC'),
      description: settingValue(settings, 'SETTING_MO_TA_CUA_HANG'),
      address: settingValue(settings, 'SETTING_DIA_CHI_CUA_HANG'),
      mapUrl: settingValue(settings, 'SETTING_DUONG_DAN_GG_MAP_CUA_HANG'),
      mapShareUrl: settingValue(settings, 'SETTING_DUONG_DAN_CHIA_SE_GG_MAP_CUA_HANG'),
      zaloPageUrl: settingValue(settings, 'SETTING_DUONG_DAN_PAGE_ZALO_CUA_HANG'),
      zaloUrl: settingValue(settings, 'SETTING_DUONG_DAN_SO_ZALO_CUA_HANG'),
      messengerUrl: settingValue(settings, 'SETTING_DUONG_DAN_FACEBOOK_MESSENGER_CUA_HANG'),
      facebookUrl: settingValue(settings, 'SETTING_DUONG_DAN_PAGE_FACEBOOK_CUA_HANG'),
      websiteUrl: settingValue(settings, 'SETTING_DUONG_DAN_TRANG_WEBSITE_CUA_HANG'),
      tiktokUrl: settingValue(settings, 'SETTING_DUONG_DAN_TIKTOK_CUA_HANG'),
      youtubeUrl: settingValue(settings, 'SETTING_DUONG_DAN_YOUTUBE_CUA_HANG'),
      aboutHtml: settingValue(settings, 'SETTING_GIOI_THIEU_CUA_HANG'),
      commitmentHtml: settingValue(settings, 'SETTING_CAM_KET_BAN_HANG'),
      commitmentText: settingValue(settings, 'SETTING_CAM_KET_BAN_HANG_ONLY_TEXT'),
    };
  }

  window.applyWwContact = function (contact) {
    if (!contact || !contact.ready) return;

    setTextSlots('store-name', contact.storeName);
    setTextSlots('store-description', contact.description);
    setTextSlots('tax-code', contact.taxCode);
    setTextSlots('address', contact.address);
    setTextSlots('working-hours', contact.workingHours);
    setHtmlSlots('about-html', contact.aboutHtml);
    setHtmlSlots('commitment-html', contact.commitmentHtml);
    setTextSlots('commitment-text', contact.commitmentText || contact.storeName);

    var c = contact.hotline;
    if (c) {
      document.querySelectorAll('[data-ww-contact="hotline"]').forEach(function (el) {
        el.href = c.tel;
        el.title = c.display;
      });
      document.querySelectorAll('[data-ww-contact-slot="hotline-number"]').forEach(function (el) {
        el.textContent = c.display;
      });
      document.querySelectorAll('[data-ww-contact-slot="hotline-label"]').forEach(function (el) {
        el.textContent = c.display;
      });
    }

    var listEl = document.querySelectorAll('[data-ww-contact-slot="hotline-list"]');
    if (listEl.length && contact.hotlines && contact.hotlines.length) {
      var html = contact.hotlines
        .map(function (h, i) {
          var sep = i > 0 ? ' <span>·</span> ' : '';
          return (
            sep +
            '<a class="link text-primary font-semibold" href="' +
            h.tel +
            '" title="' +
            h.display +
            '">' +
            h.display +
            '</a>'
          );
        })
        .join('');
      listEl.forEach(function (el) {
        el.innerHTML = html;
      });
    }

    if (contact.email) {
      document.querySelectorAll('[data-ww-contact="email"]').forEach(function (el) {
        el.href = 'mailto:' + contact.email;
        el.title = contact.email;
      });
      setTextSlots('email', contact.email);
      document.querySelectorAll('[data-ww-contact="email"][data-ww-contact-fill-text]').forEach(function (el) {
        el.textContent = contact.email;
      });
    }

    setLinkGroup('zalo', contact.zaloUrl || contact.zaloPageUrl);
    setLinkGroup('messenger', contact.messengerUrl || contact.facebookUrl);
    setLinkGroup('facebook', contact.facebookUrl);
    setLinkGroup('tiktok', contact.tiktokUrl);
    setLinkGroup('youtube', contact.youtubeUrl);
    setLinkGroup('website', contact.websiteUrl, {
      title: contact.websiteUrl,
      fillText: (contact.websiteUrl || '').replace(/^https?:\/\//i, '').replace(/\/$/, ''),
    });
    setLinkGroup('map', contact.mapUrl);
    setLinkGroup('map-link', contact.mapShareUrl
      || (contact.mapUrl && contact.mapUrl.indexOf('embed') !== -1
        ? 'https://maps.google.com/?q=' + encodeURIComponent(contact.address || contact.storeName || 'Win Win')
        : contact.mapUrl));

    if (contact.workingHours) {
      document.querySelectorAll('[data-ww-contact-slot="hotline-hours"]').forEach(function (el) {
        el.textContent = contact.workingHours;
      });
    }
  };

  applyWwContact(window.__wwContact);
  document.dispatchEvent(new CustomEvent('ww:contact-ready', { detail: window.__wwContact }));

  var apiUrl = @json(url('/api/public/setting/list')) + '?IS_GET_ALL_ELEMENTS=true&TYPE=SETTING_WEB';

  fetch(apiUrl, {
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
  })
    .then(function (r) {
      if (!r.ok) throw new Error('settings');
      return r.json();
    })
    .then(function (data) {
      var list = data && data.DATAS && data.DATAS.SETTING && data.DATAS.SETTING.DATA;
      window.__wwContact = buildContact(Array.isArray(list) ? list : []);
      applyWwContact(window.__wwContact);
      document.dispatchEvent(new CustomEvent('ww:contact-ready', { detail: window.__wwContact }));
    })
    .catch(function () {
      /* giữ bootstrap từ server */
    });
})();
</script>
