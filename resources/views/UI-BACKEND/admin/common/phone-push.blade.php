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

  if (!cfg.publicKey || !('serviceWorker' in navigator) || !('PushManager' in window) || !window.isSecureContext) {
    setLabel('Điện thoại chưa hỗ trợ');
    button.disabled = true;
    return;
  }

  var ready = navigator.serviceWorker.register(cfg.swUrl).then(function () {
    return navigator.serviceWorker.ready;
  });

  function paint(subscription) {
    var on = !!(subscription && Notification.permission === 'granted');
    setLabel(on ? 'Đã bật điện thoại' : 'Thông báo điện thoại');
    if (testButton) testButton.hidden = !on;
  }

  ready.then(function (registration) {
    return registration.pushManager.getSubscription();
  }).then(paint).catch(function () {
    setLabel('Chưa bật được');
  });

  button.addEventListener('click', function () {
    if (Notification.permission === 'denied') {
      toast(false, 'Điện thoại đang chặn thông báo. Hãy bật lại trong cài đặt trình duyệt.');
      return;
    }
    ready.then(function (registration) {
      return registration.pushManager.getSubscription().then(function (existing) {
        if (existing) return existing;
        return Notification.requestPermission().then(function (permission) {
          if (permission !== 'granted') throw new Error('Bạn chưa cho phép thông báo.');
          return registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: keyBytes(cfg.publicKey)
          });
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
