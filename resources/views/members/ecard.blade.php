<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Card Member - {{ $member->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@700;900&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #05070a;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(234,179,8,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(6,182,212,0.08) 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            gap: 24px;
            font-family: 'Inter', sans-serif;
            padding: 28px 16px;
            color: #f8fafc;
        }

        /* ─── ACTION BUTTONS BAR (TOP) ─── */
        .actions-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
            max-width: 750px;
            width: 100%;
            z-index: 100;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 14px rgba(0,0,0,0.5);
            user-select: none;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.7); }

        .btn-back { background: #1e293b; color: #cbd5e1; border: 1px solid #334155; }
        .btn-back:hover { background: #334155; color: #fff; }

        .btn-dl-card { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .btn-dl-card:hover { background: linear-gradient(135deg, #059669, #047857); }

        .btn-dl-photo { background: linear-gradient(135deg, #a855f7, #9333ea); color: #fff; }
        .btn-dl-photo:hover { background: linear-gradient(135deg, #9333ea, #7e22ce); }

        .btn-dl-qr { background: #06b6d4; color: #fff; }
        .btn-dl-qr:hover { background: #0891b2; }

        .btn-print { background: #eab308; color: #0f172a; }
        .btn-print:hover { background: #ca8a04; color: #fff; }

        /* ─── SINGLE E-CARD CONTAINER ─── */
        .single-card-wrapper {
            position: relative;
            width: 580px;
            max-width: 100%;
            border-radius: 24px;
            overflow: hidden;
            background: #090d14;
            color: #ffffff;
            box-shadow:
                0 0 0 1.5px rgba(234,179,8,0.4),
                0 25px 60px rgba(0,0,0,0.85),
                0 0 40px rgba(234,179,8,0.15);
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
        }

        /* Gold Curved Swoosh Accents */
        .swoosh-top-gold {
            position: absolute;
            top: 0; right: 0; left: 0;
            height: 90px;
            background: linear-gradient(90deg, #ca8a04 0%, #eab308 40%, #fef08a 80%, #ca8a04 100%);
            clip-path: polygon(0 0, 100% 0, 100% 65%, 0% 95%);
            z-index: 0;
            opacity: 0.85;
        }
        .swoosh-top-dark {
            position: absolute;
            top: 0; right: 0; left: 0;
            height: 84px;
            background: linear-gradient(135deg, rgba(15,23,42,0.95) 0%, rgba(9,13,20,0.98) 100%);
            clip-path: polygon(0 0, 100% 0, 100% 60%, 0% 90%);
            z-index: 0;
        }
        .swoosh-bottom-gold {
            position: absolute;
            bottom: 0; right: 0; left: 0;
            height: 48px;
            background: linear-gradient(90deg, #ca8a04 0%, #eab308 50%, #fef08a 100%);
            clip-path: polygon(0 35%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
            opacity: 0.85;
        }
        .swoosh-bottom-dark {
            position: absolute;
            bottom: 0; right: 0; left: 0;
            height: 42px;
            background: linear-gradient(135deg, #090d14 0%, #0f172a 100%);
            clip-path: polygon(0 45%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
        }

        /* Header Row */
        .card-header-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-b border-gray-800/80 pb-3;
        }
        .brand-logo-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .gym-logo-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fef08a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.6);
            background: #000;
        }
        .gym-brand-titles {
            display: flex;
            flex-direction: column;
        }
        .gym-main-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.5px;
            background: linear-gradient(180deg, #ffffff 0%, #fef08a 50%, #eab308 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
            line-height: 1;
            text-transform: uppercase;
        }
        .gym-sub-tagline {
            font-size: 8.5px;
            font-weight: 800;
            color: #e2e8f0;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .card-vip-tag {
            text-align: right;
        }
        .vip-card-title {
            font-size: 9px;
            font-weight: 800;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .vip-code-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 900;
            color: #fef08a;
            letter-spacing: 0.5px;
        }

        /* Card Main Body Grid */
        .card-body-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 20px;
            align-items: center;
        }

        /* Left Side: Photo + Badge + QR Code */
        .left-column-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .photo-ring-frame {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            padding: 3.5px;
            background: linear-gradient(135deg, #ca8a04 0%, #eab308 50%, #fef08a 100%);
            box-shadow: 0 0 22px rgba(234,179,8,0.35), 0 6px 16px rgba(0,0,0,0.6);
            position: relative;
        }
        .photo-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            overflow: hidden;
        }
        .photo-avatar-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Tier Pill Badge */
        .tier-badge-pill {
            background: #000000;
            border: 1.5px solid #eab308;
            padding: 3px 12px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
            color: #fef08a;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            text-align: center;
            width: fit-content;
        }
        .badge-silver { border-color: #cbd5e1; color: #f8fafc; }
        .badge-gold { border-color: #eab308; color: #fef08a; }
        .badge-platinum { border-color: #38bdf8; color: #e0f2fe; }
        .badge-platinum-plus { border-color: #c084fc; color: #f472b6; }

        /* QR Code Box */
        .qr-code-box {
            background: #ffffff;
            padding: 5px;
            border-radius: 10px;
            width: 82px;
            height: 82px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.6);
            border: 1.5px solid #eab308;
            margin-top: 2px;
        }
        .qr-code-box img {
            width: 70px;
            height: 70px;
            display: block;
            image-rendering: pixelated;
        }

        /* Right Side: Detailed Member Info Table */
        .right-column-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .member-main-name {
            font-size: 18px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: -0.2px;
        }

        .details-table {
            display: flex;
            flex-direction: column;
            gap: 3.5px;
            font-size: 10.5px;
            margin-top: 2px;
        }
        .detail-row {
            display: flex;
            align-items: flex-start;
            line-height: 1.3;
        }
        .detail-label {
            width: 105px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            shrink: 0;
        }
        .detail-colon {
            width: 12px;
            color: #eab308;
            font-weight: 800;
            shrink: 0;
        }
        .detail-value {
            color: #f1f5f9;
            font-weight: 600;
            flex: 1;
        }
        .detail-value-highlight {
            color: #fef08a;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Discount Tag Badge */
        .discount-banner-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, rgba(234,179,8,0.15) 0%, rgba(234,179,8,0.05) 100%);
            border: 1px dashed rgba(234,179,8,0.5);
            border-radius: 8px;
            padding: 5px 10px;
            margin-top: 4px;
        }
        .discount-banner-title {
            font-size: 9px;
            font-weight: 800;
            color: #eab308;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .discount-banner-val {
            background: linear-gradient(90deg, #eab308, #fef08a);
            color: #0f172a;
            font-size: 10px;
            font-weight: 900;
            padding: 1.5px 8px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Footer Row */
        .card-footer-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-t border-gray-800/80 pt-2.5;
            font-size: 8px;
            color: #64748b;
        }
        .official-card-tag {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
        }
        .partner-logo-text {
            text-align: right;
            font-family: 'Orbitron', sans-serif;
            font-size: 9px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .partner-logo-text span { color: #fef08a; }

        @media print {
            body { background: white; padding: 0; }
            .actions-bar { display: none !important; }
            .single-card-wrapper { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>

    @php
        // ── Determinasi Tier Badge, Jenis Member & Diskon ──
        $latestTransaction = $member->transactions()->with('package')->latest()->first();
        $packageName = $latestTransaction?->package?->name ?? $member->member_type ?? 'Basic Plan';
        $packageNameLower = strtolower(trim($packageName));

        if (str_contains($packageNameLower, 'pro plan') || (str_contains($packageNameLower, 'pro') && !str_contains($packageNameLower, 'couple'))) {
            $tierBadge = 'GOLD MEMBER';
            $tierDiscount = 5;
            $tierBadgeStyle = 'badge-gold';
        } elseif (str_contains($packageNameLower, 'elite plan') || str_contains($packageNameLower, 'elite')) {
            $tierBadge = 'PLATINUM MEMBER';
            $tierDiscount = 10;
            $tierBadgeStyle = 'badge-platinum';
        } elseif (str_contains($packageNameLower, 'vvip') || str_contains($packageNameLower, 'vvip member')) {
            $tierBadge = 'PLATINUM+ VVIP';
            $tierDiscount = 15;
            $tierBadgeStyle = 'badge-platinum-plus';
        } else {
            $tierBadge = 'SILVER MEMBER';
            $tierDiscount = 5; // Default benefit discount
            $tierBadgeStyle = 'badge-silver';
        }

        $joinDate = $member->registration_date ? \Carbon\Carbon::parse($member->registration_date)->format('d M Y') : date('d M Y');
        $dobFormatted = $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y') : '-';
        $ttlText = ($member->place_of_birth ? $member->place_of_birth . ', ' : '') . $dobFormatted;
        
        $logoGymUrl = asset('asset/logo_gym.jpg');

        // QR Code Path Check
        $qrStoragePath = storage_path('app/public/qrcodes/' . $member->member_id . '.svg');
        $qrPublicUrl   = asset('storage/qrcodes/' . $member->member_id . '.svg');

        if (!file_exists($qrStoragePath)) {
            try {
                if (!is_dir(dirname($qrStoragePath))) {
                    mkdir(dirname($qrStoragePath), 0755, true);
                }
                \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($member->member_id, $qrStoragePath);
            } catch (\Exception $e) {}
        }
        $qrExists = file_exists($qrStoragePath);
    @endphp

    {{-- Action Buttons Bar (Top) --}}
    <div class="actions-bar">
        <a href="{{ route('members.index') }}" class="btn btn-back">
            ← Kembali
        </a>

        <button type="button" class="btn btn-dl-card" id="btn-dl-card">
            ↓ Download E-Card (PNG)
        </button>

        <button type="button" class="btn btn-dl-photo" id="btn-dl-photo">
            📷 Download Foto Profile
        </button>

        <button type="button" class="btn btn-dl-qr" id="btn-dl-qr">
            ↓ Download QR Code (PNG)
        </button>

        <button type="button" class="btn btn-print" onclick="window.print()">
            🖨️ Cetak Kartu
        </button>
    </div>

    {{-- SINGLE 1-PAGE E-CARD --}}
    <div class="single-card-wrapper" id="single-card-element">
        <div class="swoosh-top-gold"></div>
        <div class="swoosh-top-dark"></div>
        <div class="swoosh-bottom-gold"></div>
        <div class="swoosh-bottom-dark"></div>

        {{-- Card Header --}}
        <div class="card-header-row">
            <div class="brand-logo-group">
                <img src="{{ $logoGymUrl }}" alt="Logo Gym" class="gym-logo-circle">
                <div class="gym-brand-titles">
                    <div class="gym-main-title">BISA GYM</div>
                    <div class="gym-sub-tagline">24 HOURS &bull; SEHAT ITU MUDAH</div>
                </div>
            </div>
            <div class="card-vip-tag">
                <div class="vip-card-title">MEMBER CARD ID</div>
                <div class="vip-code-text">{{ $member->member_id }}</div>
            </div>
        </div>

        {{-- Card Body Grid --}}
        <div class="card-body-grid">
            
            {{-- Left Column: Photo Profile, Tier Badge & QR Code --}}
            <div class="left-column-profile">
                <div class="photo-ring-frame">
                    <div class="photo-avatar-img">
                        @if($member->photo_path)
                            <img src="{{ Storage::url($member->photo_path) }}" alt="{{ $member->name }}">
                        @else
                            👤
                        @endif
                    </div>
                </div>

                <div class="tier-badge-pill {{ $tierBadgeStyle }}">
                    {{ $tierBadge }}
                </div>

                <div class="qr-code-box">
                    @if($qrExists)
                        <img src="{{ $qrPublicUrl }}?v={{ filemtime($qrStoragePath) }}"
                             alt="QR Code {{ $member->member_id }}"
                             width="70" height="70">
                    @else
                        <div style="width:70px;height:70px;display:flex;align-items:center;justify-content:center;font-size:8px;color:#999;text-align:center;">
                            QR CODE
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Complete Member Information --}}
            <div class="right-column-details">
                <div class="member-main-name">{{ $member->name }}</div>

                <div class="details-table">
                    <div class="detail-row">
                        <span class="detail-label">Jenis Kelamin</span>
                        <span class="detail-colon">:</span>
                        <span class="detail-value">{{ $member->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Tempat, Tgl Lahir</span>
                        <span class="detail-colon">:</span>
                        <span class="detail-value">{{ $ttlText }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">No. Telepon / HP</span>
                        <span class="detail-colon">:</span>
                        <span class="detail-value-highlight">{{ $member->phone }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Alamat Lengkap</span>
                        <span class="detail-colon">:</span>
                        <span class="detail-value">{{ Str::limit($member->address, 36) }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Tanggal Join</span>
                        <span class="detail-colon">:</span>
                        <span class="detail-value-highlight">{{ $joinDate }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Jenis Paket Member</span>
                        <span class="detail-colon">:</span>
                        <span class="detail-value" style="color: #38bdf8; font-weight: 800;">{{ strtoupper($packageName) }}</span>
                    </div>
                </div>


            </div>

        </div>

        {{-- Card Footer --}}
        <div class="card-footer-row">
            <div class="official-card-tag">OFFICIAL MEMBER CARD &bull; BISA GYM CENTER</div>
            <div class="partner-logo-text">
                BRILLIANT <span>INDONESIA GROUP</span>
            </div>
        </div>
    </div>

    <script>
        // ── Download Single E-Card PNG ──
        document.getElementById('btn-dl-card').addEventListener('click', function(e) {
            e.stopPropagation();
            const cardEl = document.getElementById('single-card-element');
            html2canvas(cardEl, {
                scale: 3,
                backgroundColor: '#090d14',
                logging: false,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `E-Card_${memberVipId()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        // ── Download Profile Photo PNG ──
        document.getElementById('btn-dl-photo').addEventListener('click', function(e) {
            e.stopPropagation();
            const photoEl = document.querySelector('.photo-avatar-img');
            const imgTag = photoEl ? photoEl.querySelector('img') : null;
            
            if (imgTag && imgTag.src) {
                fetch(imgTag.src)
                    .then(response => response.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        const extension = imgTag.src.split('.').pop().split('?')[0] || 'jpg';
                        link.href = url;
                        link.download = `Foto_Profile_${memberVipId()}.${extension}`;
                        link.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(e => {
                        const link = document.createElement('a');
                        link.href = imgTag.src;
                        link.target = '_blank';
                        link.download = `Foto_Profile_${memberVipId()}`;
                        link.click();
                    });
            } else {
                alert('Member ini belum memiliki foto profil.');
            }
        });

        // ── Download QR Code PNG ──
        document.getElementById('btn-dl-qr').addEventListener('click', function(e) {
            e.stopPropagation();
            const qrBox = document.querySelector('.qr-code-box');
            html2canvas(qrBox, {
                scale: 4,
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `QR_${memberVipId()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        function memberVipId() {
            return "{{ $member->member_id }}";
        }
    </script>
</body>
</html>
