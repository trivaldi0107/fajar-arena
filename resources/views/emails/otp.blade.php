<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi - Fajar Arena</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 560px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .body-content {
            padding: 32px 24px;
            text-align: center;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 12px;
            color: #334155;
        }
        .message {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .otp-box {
            display: inline-block;
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 16px 36px;
            margin: 10px 0 24px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #2563eb;
            font-family: 'Courier New', Courier, monospace;
        }
        .warning-text {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 16px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>FAJAR ARENA</h1>
    </div>

    <div class="body-content">
        <div class="greeting">Halo, <strong>{{ $user->name }}</strong></div>
        <div class="message">
            Terima kasih telah mendaftar di <strong>Fajar Arena</strong>. Gunakan kode OTP berikut untuk memverifikasi pendaftaran akun Anda:
        </div>

        <div class="otp-box">
            <div class="otp-code">{{ $otpCode }}</div>
        </div>

        <div class="message">
            Kode OTP ini berlaku selama <strong>10 menit</strong>. Jangan berikan kode ini kepada siapapun demi keamanan akun Anda.
        </div>

        <div class="warning-text">
            Jika Anda tidak merasa melakukan pendaftaran di Fajar Arena, silakan abaikan email ini.
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Fajar Arena. All rights reserved.
    </div>
</div>

</body>
</html>
