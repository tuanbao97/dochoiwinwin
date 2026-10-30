<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Đang mở {{ $label }}</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      background: #f8fafc;
      color: #0f172a;
      font-family: system-ui, sans-serif;
    }
    .box { text-align: center; padding: 2rem 1.4rem; max-width: 28rem; }
    p { margin: 0; font-size: 1.15rem; line-height: 1.45; }
    a.btn {
      display: inline-flex;
      margin-top: 1.2rem;
      background: #0ea5e9;
      color: #fff;
      text-decoration: none;
      padding: 0.85rem 1.3rem;
      border-radius: 999px;
      font-weight: 700;
    }
    a.alt { display: block; margin-top: 1rem; color: #64748b; }
  </style>
</head>
<body>
  <div class="box">
    <p>Đang mở ứng dụng {{ $label }}…</p>
    <a class="btn" id="open-app" href="{{ $appUrl }}">Mở ứng dụng {{ $label }}</a>
    <a class="alt" id="open-web" href="{{ $webUrl }}">Tiếp tục trên trình duyệt</a>
  </div>
  <script>
    (function () {
      var appUrl = @json($appUrl);
      var webUrl = @json($webUrl);
      var left = false;
      document.addEventListener('visibilitychange', function () {
        if (document.hidden) left = true;
      });
      window.location.href = appUrl;
      window.setTimeout(function () {
        if (!left && !document.hidden) window.location.replace(webUrl);
      }, 1600);
    })();
  </script>
</body>
</html>
