self.addEventListener('push', function (event) {
  var data = {};
  try {
    data = event.data ? event.data.json() : {};
  } catch (error) {
    data = { title: 'Đồ Chơi Win Win', body: event.data ? event.data.text() : '' };
  }

  event.waitUntil(self.registration.showNotification(data.title || 'Đồ Chơi Win Win', {
    body: data.body || '',
    icon: data.icon || undefined,
    data: { url: data.url || '/' },
    tag: data.tag || 'ww-order',
    renotify: true
  }));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  var target = (event.notification.data && event.notification.data.url) || '/';
  event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windows) {
    for (var i = 0; i < windows.length; i++) {
      if (windows[i].url === target && 'focus' in windows[i]) {
        return windows[i].focus();
      }
    }
    if (clients.openWindow) {
      return clients.openWindow(target);
    }
  }));
});
