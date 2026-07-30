<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Card Member - {{ $member->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@700;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #070c14;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(224,255,0,0.06) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(224,255,0,0.03) 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 24px;
            font-family: 'Inter', sans-serif;
            padding: 24px;
        }

        /* ─── CARD ─── */
        .card {
            width: 400px;
            background: linear-gradient(145deg, #141b2d 0%, #0d1117 60%, #101820 100%);
            border: 1px solid rgba(224,255,0,0.15);
            border-radius: 24px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 20px 60px rgba(0,0,0,0.6),
                0 0 80px rgba(224,255,0,0.04);
        }

        /* Decorative glows */
        .card::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(224,255,0,0.07) 0%, transparent 65%);
            border-radius: 50%;
            pointer-events: none;
        }
        .card::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(59,130,246,0.05) 0%, transparent 65%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* ─── HEADER ─── */
        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            position: relative;
            z-index: 1;
        }
        .logo {
            font-family: 'Orbitron', monospace;
            font-size: 20px;
            font-weight: 900;
            color: #e0ff00;
            letter-spacing: 1px;
            text-shadow: 0 0 20px rgba(224,255,0,0.4);
        }
        .logo-sub {
            font-size: 9px;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 3px;
            font-family: 'Inter', sans-serif;
        }
        .header-right {
            text-align: right;
        }
        .reg-label {
            font-size: 8px;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .reg-date {
            font-size: 11px;
            color: #6b7280;
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px;
        }

        /* ─── PHOTO + INFO ─── */
        .member-section {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .photo-wrap {
            position: relative;
            flex-shrink: 0;
        }
        .photo {
            width: 76px;
            height: 76px;
            border-radius: 16px;
            border: 2px solid rgba(224,255,0,0.4);
            object-fit: cover;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 32px;
            box-shadow: 0 0 20px rgba(224,255,0,0.1);
        }
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .photo-corner {
            position: absolute;
            width: 12px; height: 12px;
            border-color: #e0ff00;
            border-style: solid;
        }
        .photo-corner.tl { top: -4px; left: -4px; border-width: 2px 0 0 2px; border-radius: 3px 0 0 0; }
        .photo-corner.br { bottom: -4px; right: -4px; border-width: 0 2px 2px 0; border-radius: 0 0 3px 0; }

        .member-name { font-size: 20px; font-weight: 700; color: #f9fafb; line-height: 1.2; }
        .member-vip {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: #e0ff00;
            margin-top: 5px;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-active  { background: rgba(34,197,94,0.12);  color: #22c55e; border: 1px solid rgba(34,197,94,0.25); }
        .status-active .dot { background: #22c55e; box-shadow: 0 0 6px #22c55e; animation: pulse 2s infinite; }
        .status-pending { background: rgba(234,179,8,0.12);  color: #eab308; border: 1px solid rgba(234,179,8,0.25); }
        .status-expired { background: rgba(239,68,68,0.12);  color: #ef4444; border: 1px solid rgba(239,68,68,0.25); }

        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.4; }
        }

        /* ─── DIVIDER ─── */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(224,255,0,0.15), transparent);
            margin: 18px 0;
            position: relative;
            z-index: 1;
        }

        /* ─── MAIN BODY: INFO + QR ─── */
        .card-body {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }

        .info-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .info-item label {
            font-size: 8px;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: block;
            font-weight: 600;
        }
        .info-item span {
            font-size: 12px;
            color: #e2e8f0;
            margin-top: 3px;
            display: block;
            font-weight: 500;
        }
        .info-item span.highlight {
            color: #e0ff00;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }

        /* ─── QR CODE ─── */
        .qr-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .qr-box {
            background: #ffffff;
            padding: 10px;
            border-radius: 12px;
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(224,255,0,0.1);
            border: 1px solid rgba(224,255,0,0.2);
        }
        .qr-box img {
            width: 90px;
            height: 90px;
            display: block;
            image-rendering: pixelated;
        }
        .qr-hint {
            font-size: 8px;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-align: center;
            font-weight: 600;
        }

        /* ─── FOOTER ─── */
        .card-footer {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .footer-left {
            font-size: 8px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .chip {
            display: flex;
            gap: 3px;
        }
        .chip span {
            width: 8px; height: 8px;
            border-radius: 2px;
            background: rgba(224,255,0,0.15);
            border: 1px solid rgba(224,255,0,0.2);
        }
        .chip span:nth-child(2) { background: rgba(224,255,0,0.25); }
        .chip span:nth-child(3) { background: rgba(224,255,0,0.1); }

        /* ─── PRINT BUTTON ─── */
        .actions {
            display: flex;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }
        .btn-print {
            background: #e0ff00;
            color: #0d1117;
        }
        .btn-print:hover { background: #c4e600; transform: translateY(-1px); }
        .btn-download-qr {
            background: #2563eb;
            color: white;
        }
        .btn-download-qr:hover { background: #1d4ed8; transform: translateY(-1px); }
        .btn-download-photo {
            background: #8b5cf6;
            color: white;
        }
        .btn-download-photo:hover { background: #7c3aed; transform: translateY(-1px); }
        .btn-download-id {
            background: #10b981;
            color: white;
        }
        .btn-download-id:hover { background: #059669; transform: translateY(-1px); }
        .btn-back {
            background: rgba(255,255,255,0.05);
            color: #9ca3af;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .btn-back:hover { background: rgba(255,255,255,0.08); color: #f3f4f6; }

        @media print {
            body { background: white; }
            .actions { display: none; }
            .card { box-shadow: none; border-color: #ddd; }
            .logo { text-shadow: none; }
            .status-active .dot { box-shadow: none; animation: none; }
        }
    </style>
</head>
<body>
    <div class="card" id="ecard-container">
        {{-- Header --}}
        <div class="card-header">
            <div>
                <div class="logo">BisaGym</div>
                <div class="logo-sub">Member E-Card</div>
            </div>
            <div class="header-right">
                <div class="reg-label">Terdaftar</div>
                <div class="reg-date">{{ \Carbon\Carbon::parse($member->registration_date)->format('d M Y') }}</div>
            </div>
        </div>

        {{-- Member Info --}}
        <div class="member-section">
            <div class="photo-wrap">
                <div class="photo">
                    @if($member->photo_path)
                        <img src="{{ Storage::url($member->photo_path) }}" alt="{{ $member->name }}">
                    @else
                        👤
                    @endif
                </div>
                <div class="photo-corner tl"></div>
                <div class="photo-corner br"></div>
            </div>
            <div>
                <div class="member-name">{{ $member->name }}</div>
                <div class="member-vip">{{ $member->member_id }}</div>
                <div>
                    @if($member->status === 'active')
                        <span class="status-badge status-active"><span class="dot"></span>Aktif</span>
                    @elseif($member->status === 'pending')
                        <span class="status-badge status-pending"><span class="dot"></span>Pending</span>
                    @else
                        <span class="status-badge status-expired"><span class="dot"></span>Expired</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="divider"></div>

        {{-- Body: Info + QR --}}
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Tipe Paket</label>
                    <span>{{ ucfirst($member->member_type) }}</span>
                </div>
                <div class="info-item">
                    <label>Jenis Kelamin</label>
                    <span>{{ $member->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="info-item">
                    <label>Berlaku Hingga</label>
                    <span class="highlight">{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') }}</span>
                </div>
                <div class="info-item">
                    <label>No. WhatsApp</label>
                    <span>{{ $member->phone }}</span>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <label>Alamat</label>
                    <span style="font-size:11px; color:#6b7280; line-height:1.4;">{{ Str::limit($member->address, 60) }}</span>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="qr-wrapper">
                @php
                    $qrStoragePath = storage_path('app/public/qrcodes/' . $member->member_id . '.svg');
                    $qrPublicUrl   = asset('storage/qrcodes/' . $member->member_id . '.svg');

                    // Auto-generate jika file hilang
                    if (!file_exists($qrStoragePath)) {
                        try {
                            if (!is_dir(dirname($qrStoragePath))) {
                                mkdir(dirname($qrStoragePath), 0755, true);
                            }
                            \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($member->member_id, $qrStoragePath);
                        } catch (\Exception $e) {
                            // silent fail
                        }
                    }

                    $qrExists = file_exists($qrStoragePath);
                @endphp

                <div class="qr-box">
                    @if($qrExists)
                        {{-- Gunakan <img> dengan URL publik agar ukuran terkontrol --}}
                        <img src="{{ $qrPublicUrl }}?v={{ filemtime($qrStoragePath) }}"
                             alt="QR Code {{ $member->member_id }}"
                             width="90" height="90">
                    @else
                        <div style="width:90px;height:90px;display:flex;align-items:center;justify-content:center;font-size:9px;color:#999;text-align:center;background:#f5f5f5;">
                            QR<br>Error
                        </div>
                    @endif
                </div>
                <span class="qr-hint">Scan Absensi</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer">
            <div class="footer-left">bisagym.id &bull; {{ date('Y') }}</div>
            <div class="chip">
                <span></span><span></span><span></span><span></span>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="actions">
        <a href="{{ route('members.index') }}" class="btn btn-back">
            ← Kembali
        </a>
        <a href="{{ $qrPublicUrl }}?v={{ filemtime($qrStoragePath) }}" download="QR_{{ $member->member_id }}.svg" class="btn btn-download-qr">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> 
            ↓ QR
        </a>
        @if($member->photo_path)
            <a href="{{ Storage::url($member->photo_path) }}" download="Foto_{{ $member->member_id }}.jpg" class="btn btn-download-photo" title="Download Foto Profil Member">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> 
                ↓ Foto Profil
            </a>
        @endif
        <button class="btn btn-download-id" id="btn-download-id">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            ↓ ID Card
        </button>
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Cetak
        </button>
    </div>

    <script>
        document.getElementById('btn-download-id').addEventListener('click', function() {
            const cardElement = document.getElementById('ecard-container');
            const origTransform = cardElement.style.transform;
            
            html2canvas(cardElement, {
                scale: 3, // High res
                backgroundColor: '#070c14',
                logging: false,
                allowTaint: true,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `IDCard_{{ $member->member_id }}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });
    </script>
</body>
</html>
