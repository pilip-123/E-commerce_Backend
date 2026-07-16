<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            padding: 40px 40px 32px;
            text-align: center;
        }
        .header img {
            max-width: 80px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 16px 0 0;
            font-weight: 700;
        }
        .body {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .text {
            font-size: 15px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 16px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.35);
            transition: all 0.2s ease;
        }
        .btn:hover {
            box-shadow: 0 6px 24px rgba(34, 197, 94, 0.45);
            transform: translateY(-1px);
        }
        .note {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .footer {
            text-align: center;
            padding: 24px 40px;
            background: #f8fafc;
            font-size: 12px;
            color: #94a3b8;
        }
        .footer a {
            color: #22c55e;
            text-decoration: none;
        }
        @media only screen and (max-width: 480px) {
            .body { padding: 24px; }
            .header { padding: 32px 24px 24px; }
            .btn { display: block; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="{{ config('app.name') }}">
                <h1>Reset Your Password</h1>
            </div>
            <div class="body">
                <div class="greeting">Hello, {{ $user->name }}!</div>
                <div class="text">
                    You are receiving this email because we received a password reset request for your account.
                    Click the button below to reset your password.
                </div>

                <div class="btn-wrapper">
                    <a href="{{ $resetUrl }}" class="btn" target="_blank">Reset Password</a>
                </div>

                <div class="text" style="text-align:center;font-size:13px;color:#94a3b8;">
                    This link will expire in {{ \App\Services\PasswordResetService::EXPIRY_MINUTES }} minutes.
                </div>

                <div class="note">
                    If you did not request a password reset, no further action is required. Your account is safe.
                    If you have any questions, please contact our support team.
                </div>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </div>
        </div>
    </div>
</body>
</html>
