<style>
#ww-phone-push-guide {
	position: fixed;
	inset: 0;
	z-index: 100000;
	display: flex;
	align-items: flex-end;
	justify-content: center;
	padding: 16px;
	background: rgba(15, 23, 42, 0.48);
}
#ww-phone-push-guide[hidden] { display: none !important; }
#ww-phone-push-guide .panel {
	width: min(100%, 420px);
	margin-bottom: env(safe-area-inset-bottom);
	padding: 18px 16px 16px;
	border-radius: 16px;
	background: #fff;
	color: #0f172a;
	box-shadow: 0 16px 40px rgba(15, 23, 42, 0.2);
}
#ww-phone-push-guide h3 { margin: 0 0 8px; font-size: 18px; font-weight: 700; }
#ww-phone-push-guide p, #ww-phone-push-guide li { font-size: 15px; line-height: 1.45; }
#ww-phone-push-guide ol { margin: 0 0 14px; padding-left: 20px; }
#ww-phone-push-guide button {
	width: 100%;
	min-height: 44px;
	border: 0;
	border-radius: 999px;
	background: #1d4ed8;
	color: #fff;
	font-weight: 700;
}
</style>
<div id="ww-phone-push-guide" hidden>
	<div class="panel" role="dialog" aria-modal="true" aria-labelledby="ww-phone-push-guide-title">
		<h3 id="ww-phone-push-guide-title">Bật thông báo trên iPhone</h3>
		<p id="ww-phone-push-guide-lead"></p>
		<ol id="ww-phone-push-guide-steps"></ol>
		<button type="button" id="ww-phone-push-guide-close">Đã hiểu</button>
	</div>
</div>
<script>
window.WW_PHONE_PUSH = {
  publicKey: @json((string) config('services.webpush.public_key')),
  swUrl: @json(url('/admin-phone-push-sw.js')),
  saveUrl: @json(url('/api/admin/phone-push')),
  testUrl: @json(url('/api/admin/phone-push/test'))
};
</script>
<script>
(function () {
  var cfg = window.WW_PHONE_PUSH || {};
  var button = document.getElementById('ww-phone-push-btn');
  var testButton = document.getElementById('ww-phone-push-test');
  var label = button ? button.querySelector('span') : null;
  if (!button) return;

  function setLabel(text) {
    if (label) label.textContent = text;
    button.title = text;
  }

  function toast(ok, message) {
    if (ok && typeof showToastSuccess === 'function') {
      showToastSuccess('top-right', message);
      return;
    }
    if (!ok && typeof showToastFailure === 'function') {
      showToastFailure('top-right', message);
      return;
    }
    window.alert(message);
  }

  function authHeaders() {
    var token = localStorage.getItem('ACCESS_TOKEN') || '';
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': token ? 'Bearer ' + token : ''
    };
  }

  function keyBytes(value) {
    var padding = '='.repeat((4 - value.length % 4) % 4);
    var base64 = (value + padding).replace(/-/g, '+').replace(/_/g, '/');
    var raw = window.atob(base64);
    var output = new Uint8Array(raw.length);
    for (var i = 0; i < raw.length; i++) output[i] = raw.charCodeAt(i);
    return output;
  }

  var ua = navigator.userAgent || '';
  var ios = /iPad|iPhone|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
  var chromeOnIos = ios && /CriOS/.test(ua);
  var standalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
  var guide = document.getElementById('ww-phone-push-guide');
  var guideLead = document.getElementById('ww-phone-push-guide-lead');
  var guideSteps = document.getElementById('ww-phone-push-guide-steps');

  function showGuide() {
    if (!guide || !guideLead || !guideSteps) {
      toast(false, 'iPhone chỉ nhận thông báo khi mở từ màn hình chính.');
      return;
    }
    if (chromeOnIos) {
      guideLead.textContent = 'Chrome trên iPhone không nhận được thông báo. Hãy mở trang này bằng Safari.';
      guideSteps.innerHTML = '<li>Sao chép địa chỉ trang quản trị.</li><li>Mở Safari, dán địa chỉ và đăng nhập.</li><li>Bấm nút Chia sẻ, chọn Thêm vào Màn hình chính.</li><li>Mở icon Win Win vừa thêm, đăng nhập lại, rồi bấm chuông.</li>';
    } else {
      guideLead.textContent = 'Safari chỉ gửi thông báo khi trang quản trị được mở từ icon trên màn hình chính.';
      guideSteps.innerHTML = '<li>Bấm nút Chia sẻ ở thanh dưới Safari.</li><li>Chọn Thêm vào Màn hình chính, rồi Thêm.</li><li>Mở icon Win Win vừa thêm, đăng nhập lại.</li><li>Bấm chuông một lần nữa và chọn Cho phép.</li>';
    }
    guide.hidden = false;
  }

  if (guide) {
    guide.addEventListener('click', function (event) {
      if (event.target === guide || event.target.id === 'ww-phone-push-guide-close') {
        guide.hidden = true;
      }
    });
  }

  var ready = ('serviceWorker' in navigator && window.isSecureContext)
    ? navigator.serviceWorker.register(cfg.swUrl).then(function () { return navigator.serviceWorker.ready; })
    : Promise.reject();

  function paint(subscription) {
    var on = !!(subscription && window.Notification && Notification.permission === 'granted');
    setLabel(on ? 'Đã bật điện thoại' : 'Thông báo điện thoại');
    if (testButton) testButton.hidden = !on;
  }

  ready.then(function (registration) {
    if (!registration.pushManager) return null;
    return registration.pushManager.getSubscription();
  }).then(paint).catch(function () {});

  button.addEventListener('click', function () {
    if (ios && !standalone) {
      showGuide();
      return;
    }
    if (!cfg.publicKey || !window.isSecureContext || !('serviceWorker' in navigator) || typeof Notification === 'undefined') {
      toast(false, 'Trình duyệt này chưa nhận được thông báo.');
      return;
    }
    if (Notification.permission === 'denied') {
      toast(false, ios
        ? 'Hãy vào Cài đặt iPhone → Thông báo → Win Win và bật Cho phép thông báo.'
        : 'Điện thoại đang chặn thông báo. Hãy bật lại trong cài đặt trình duyệt.');
      return;
    }
    var ask = Notification.permission === 'granted'
      ? Promise.resolve('granted')
      : Notification.requestPermission();
    ask.then(function (permission) {
      if (permission !== 'granted') throw new Error('Bạn chưa cho phép thông báo.');
      return ready;
    }).then(function (registration) {
      return registration.pushManager.getSubscription().then(function (existing) {
        if (existing) return existing;
        return registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: keyBytes(cfg.publicKey)
        });
      });
    }).then(function (subscription) {
      var json = subscription.toJSON();
      return fetch(cfg.saveUrl, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({
          ENDPOINT: subscription.endpoint,
          PUBLIC_KEY: json.keys && json.keys.p256dh,
          AUTH_TOKEN: json.keys && json.keys.auth,
          CONTENT_ENCODING: 'aes128gcm'
        })
      }).then(function (response) {
        if (!response.ok) throw new Error('Không lưu được điện thoại nhận thông báo.');
        paint(subscription);
        toast(true, 'Điện thoại này sẽ nhận thông báo khi có đơn mới.');
      });
    }).catch(function (error) {
      toast(false, error && error.message ? error.message : 'Không bật được thông báo.');
    });
  });

  if (testButton) {
    testButton.addEventListener('click', function () {
      fetch(cfg.testUrl, { method: 'POST', headers: authHeaders(), body: '{}' })
        .then(function (response) { return response.json(); })
        .then(function (data) {
          toast(true, (data && data.STATUS_DETAIL) || 'Đã gửi thông báo thử.');
        })
        .catch(function () {
          toast(false, 'Không gửi được thông báo thử.');
        });
    });
  }
})();
</script>
