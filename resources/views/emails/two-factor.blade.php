<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi BisaGym</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #333333; margin-top: 0; text-align: center; }
        p { color: #555555; line-height: 1.6; font-size: 16px; text-align: center; }
        .code-box { background-color: #f8f9fa; border: 2px dashed #00bfff; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #333333; }
        .footer { margin-top: 30px; border-top: 1px solid #eeeeee; padding-top: 20px; text-align: center; color: #888888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verifikasi Keamanan BisaGym</h2>
        <p>Halo <strong>{{ $user->name }}</strong>,</p>
        <p>Seseorang telah mencoba login ke akun Anda. Untuk melanjutkan, silakan masukkan kode verifikasi (OTP) berikut:</p>
        
        <div class="code-box">
            {{ $code }}
        </div>
        
        <p>Kode ini akan <strong>kedaluwarsa dalam 15 menit</strong>. Mohon jangan berikan kode ini kepada siapa pun.</p>
        <p>Jika ini bukan Anda, mohon abaikan email ini.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} Manajemen BisaGym. All rights reserved.
        </div>
    </div>
</body>
</html>
