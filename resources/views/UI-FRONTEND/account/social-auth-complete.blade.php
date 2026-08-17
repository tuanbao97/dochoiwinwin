<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Đăng nhập thành công — Đồ Chơi Win Win</title>
</head>
<body>
    <p>Đăng nhập thành công. Đang chuyển về trang chủ…</p>
    <script>
        (function () {
            localStorage.setItem('ACCESS_TOKEN', {{ Illuminate\Support\Js::from($accessToken) }});
            localStorage.removeItem('REFRESH_TOKEN');
            localStorage.setItem('AUTH_SCOPE', 'storefront');
            window.location.replace({{ Illuminate\Support\Js::from($redirectUrl) }});
        })();
    </script>
    <noscript>
        Vui lòng bật JavaScript và <a href="{{ $redirectUrl }}">quay về trang chủ</a>.
    </noscript>
</body>
</html>
