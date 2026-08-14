<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - Fajar Arena</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table role="presentation" width="100%" max-width="600" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- Header (Clean Without Icons) -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 26px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">Fajar Arena</h1>
                            <p style="color: #93c5fd; font-size: 12px; margin: 6px 0 0 0; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Sistem Reservasi Arena Olahraga</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 32px; background-color: #ffffff;">
                            
                            <h2 style="color: #0f172a; font-size: 20px; font-weight: 700; margin-top: 0; margin-bottom: 16px;">Halo, {{ $userName }}</h2>
                            
                            <p style="color: #475569; font-size: 15px; line-height: 1.6; margin-bottom: 24px;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun <strong>Fajar Arena</strong> Anda. Silakan klik tombol di bawah ini untuk membuat kata sandi baru:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 32px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 14px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);">
                                            Atur Ulang Kata Sandi Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice Box -->
                            <div style="background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 0 12px 12px 0; margin-bottom: 24px;">
                                <p style="color: #334155; font-size: 13px; line-height: 1.5; margin: 0;">
                                    <strong>Catatan Keamanan:</strong> Tautan pengaturan ulang kata sandi ini hanya berlaku selama <strong>60 menit</strong>. Jika Anda tidak pernah meminta perubahan kata sandi, tidak ada tindakan yang perlu dilakukan dan akun Anda tetap aman.
                                </p>
                            </div>

                            <!-- Fallback Link -->
                            <p style="color: #94a3b8; font-size: 12px; line-height: 1.5; margin-bottom: 0; border-t: 1px solid #f1f5f9; padding-top: 20px;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:<br>
                                <a href="{{ $url }}" style="color: #2563eb; word-break: break-all; text-decoration: underline;">{{ $url }}</a>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #64748b; font-size: 13px; margin: 0 0 6px 0; font-weight: 600;">Salam hangat,</p>
                            <p style="color: #0f172a; font-size: 14px; margin: 0 0 16px 0; font-weight: 700;">Tim Fajar Arena</p>
                            <p style="color: #94a3b8; font-size: 11px; margin: 0;">&copy; {{ date('Y') }} Fajar Arena. All rights reserved.</p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
