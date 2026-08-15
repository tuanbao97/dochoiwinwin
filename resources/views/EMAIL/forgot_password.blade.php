<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-header h2 {
            color: #3b86d1;
            font-size: 24px;
            margin: 0;
        }
        .email-body {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: center; /* Căn giữa cho toàn bộ phần body */
        }
        .email-body p {
            margin-bottom: 10px;
        }
        .button-container {
            text-align: center; /* Đảm bảo nút sẽ được căn giữa */
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #3b86d1;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color:rgb(5, 126, 247);
        }
        .footer {
            font-size: 15px;
            text-align: center;
            color: #777;
            margin-top: 20px;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Xin chào {{ $name }}!</h2>
        </div>
        <div class="email-body">
            <p>Chúng tôi đã nhận được yêu cầu reset mật khẩu cho tài khoản của bạn.</p>
            <p>Để thiết lập lại mật khẩu, vui lòng nhấp vào liên kết dưới đây:</p>
        </div>
        <div class="button-container">
            <a href="{{ $resetLink }}" class="button" target="_blank" style="color: white;">Thiết lập lại mật khẩu</a>
        </div>
        <div class="email-body">
            <p>Liên kết này sẽ hết hiệu lực sau 5 phút kể từ thời điểm yêu cầu.</p>
        </div>
        <div class="footer">
            <p>Thân ái,<br>Đồ Chơi Win Win</p>
        </div>
    </div>
</body>
</html>
