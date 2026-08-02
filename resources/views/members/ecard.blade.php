<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Card Member - {{ $member->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@700;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #070c14;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(212,255,0,0.06) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(59,130,246,0.04) 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 24px;
            font-family: 'Inter', sans-serif;
            padding: 24px;
            color: #f8fafc;
        }

        /* ─── 3D CONTAINER & FLIP ─── */
        .card-container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .card-wrapper {
            perspective: 1200px;
            width: 480px;
            height: 300px;
            cursor: pointer;
            user-select: none;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }

        .card-wrapper.flipped .card-inner {
            transform: rotateY(180deg);
        }

        /* ─── SIDE BY SIDE MODE ─── */
        .side-by-side-mode .card-wrapper {
            width: auto;
            height: auto;
            perspective: none;
            cursor: default;
        }
        .side-by-side-mode .card-inner {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            transform: none !important;
            transition: none;
            perspective: none;
        }
        .side-by-side-mode .card-face {
            position: relative !important;
            width: 480px !important;
            height: 300px !important;
            transform: none !important;
            backface-visibility: visible !important;
            opacity: 1 !important;
        }

        /* ─── CARD FACE COMMON ─── */
        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.08),
                0 20px 45px rgba(0,0,0,0.6),
                0 0 50px rgba(212,255,0,0.05);
        }

        /* ══════════════════════════════════════════════════════════
           TAMPAK DEPAN (FRONT FACE - WHITE LUXURY GOLD)
        ══════════════════════════════════════════════════════════ */
        .card-front {
            background: #ffffff;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 22px;
            position: relative;
        }

        /* Background Waves & Watermark Silhouette */
        .card-front .bg-wave-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 90px;
            background: linear-gradient(135deg, #0d1117 0%, #1e293b 70%, #d4ff00 100%);
            clip-path: polygon(0 0, 100% 0, 100% 55%, 0% 100%);
            z-index: 0;
        }
        .card-front .bg-wave-top-gold {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 96px;
            background: linear-gradient(90deg, #eab308, #ca8a04, #d4ff00);
            clip-path: polygon(0 0, 100% 0, 100% 62%, 0% 105%);
            z-index: 0;
            opacity: 0.85;
        }
        .card-front .bg-wave-bottom {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 45px;
            background: linear-gradient(135deg, #0d1117 0%, #1e293b 100%);
            clip-path: polygon(0 40%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
        }
        .card-front .bg-wave-bottom-gold {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 50px;
            background: linear-gradient(90deg, #eab308 0%, #ca8a04 50%, #d4ff00 100%);
            clip-path: polygon(0 30%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
            opacity: 0.85;
        }
        .card-front .watermark-silhouette {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 170px;
            height: 170px;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23000000'%3E%3Cpath d='M20.5 6c-2.61.7-5.67 1-8.5 1s-5.89-.3-8.5-1L3 8c1.86.5 4 .83 6 1v13h2v-6h2v6h2V9c2-.17 4.14-.5 6-1l-0.5-2z'/%3E%3Cpath d='M12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z'/%3E%3C/svg%3E") center/contain no-repeat;
        }

        /* Front Content Layout */
        .front-header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #eab308;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            background: #000;
        }
        .brand-text {
            color: #ffffff;
        }
        .brand-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            line-height: 1;
        }
        .brand-title span { color: #d4ff00; }
        .brand-tagline {
            font-size: 8px;
            font-weight: 700;
            color: #e2e8f0;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .front-body {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 16px;
            align-items: center;
            margin-top: 10px;
        }

        /* Photo & Tier Badge */
        .photo-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            shrink: 0;
        }
        .photo-frame {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 50%, #d4ff00 100%);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            position: relative;
        }
        .photo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            overflow: hidden;
        }
        .photo-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Tier Badges styling */
        .tier-badge {
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.4);
            font-family: 'JetBrains Mono', monospace;
        }
        .badge-silver {
            background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 50%, #94a3b8 100%);
            color: #0f172a;
        }
        .badge-gold {
            background: linear-gradient(135deg, #fef08a 0%, #eab308 50%, #ca8a04 100%);
            color: #0f172a;
        }
        .badge-platinum {
            background: linear-gradient(135deg, #e0f2fe 0%, #38bdf8 50%, #0284c7 100%);
            color: #0f172a;
        }
        .badge-platinum-plus {
            background: linear-gradient(135deg, #f472b6 0%, #c084fc 50%, #6366f1 100%);
            color: #ffffff;
            text-shadow: 0 1px 2px rgba(0,0,0,0.4);
        }

        /* Member Info Details */
        .info-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .member-fullname {
            font-size: 17px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: -0.2px;
        }
        .info-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10.5px;
            color: #334155;
            font-weight: 500;
        }
        .info-row i { color: #ca8a04; font-size: 12px; }
        .info-row span.label { color: #64748b; font-weight: 600; font-size: 9.5px; text-transform: uppercase; width: 55px; }

        .join-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            padding: 2.5px 10px;
            border-radius: 6px;
            width: fit-content;
            margin-top: 4px;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 2px 6px rgba(217,119,6,0.3);
            letter-spacing: 0.5px;
        }

        .front-footer {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 4px;
        }
        .group-logo-text {
            font-size: 9px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }
        .group-logo-text span { color: #eab308; }


        /* ══════════════════════════════════════════════════════════
           TAMPAK BELAKANG (BACK FACE - LUXURY BLACK GOLD)
        ══════════════════════════════════════════════════════════ */
        .card-back {
            background: linear-gradient(145deg, #090d16 0%, #0f172a 60%, #05080f 100%);
            color: #f8fafc;
            padding: 18px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border: 1px solid rgba(234,179,8,0.25);
        }

        .card-back .gold-line-wave {
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background:
                radial-gradient(circle at 90% 10%, rgba(234,179,8,0.08) 0%, transparent 40%),
                radial-gradient(circle at 10% 90%, rgba(212,255,0,0.05) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        .back-header {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-b border-gray-800/80 pb-2;
        }
        .back-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 13px;
            font-weight: 900;
            color: #eab308;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .back-vip {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            color: #d4ff00;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }
        .back-logo-img {
            height: 28px;
            border-radius: 6px;
            border: 1px solid rgba(234,179,8,0.4);
        }

        .back-main {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 16px;
            align-items: center;
            margin: 6px 0;
        }

        /* Promo Voucher Box (Left Side of Back) */
        .promo-voucher-box {
            flex: 1;
            background: linear-gradient(135deg, rgba(30,41,59,0.7) 0%, rgba(15,23,42,0.9) 100%);
            border: 1px dashed rgba(234,179,8,0.4);
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.4);
        }
        .promo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-b border-gray-700/60 pb-4;
        }
        .promo-title {
            font-size: 9px;
            font-weight: 800;
            color: #eab308;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .promo-discount-tag {
            background: linear-gradient(90deg, #eab308, #d4ff00);
            color: #0f172a;
            font-size: 10px;
            font-weight: 900;
            padding: 1px 7px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
        }
        .promo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 9.5px;
            color: #e2e8f0;
            font-weight: 600;
        }
        .promo-item-name {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .promo-item-disc {
            color: #d4ff00;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
        }
        .promo-footer-hint {
            font-size: 7.5px;
            color: #94a3b8;
            font-style: italic;
            text-align: center;
            margin-top: 2px;
        }

        /* Non-Promo Standard Box */
        .standard-member-box {
            flex: 1;
            background: rgba(30,41,59,0.5);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
        }
        .standard-member-title {
            font-size: 10px;
            font-weight: 800;
            color: #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .standard-member-text {
            font-size: 8.5px;
            color: #94a3b8;
            line-height: 1.4;
        }

        /* QR Code Box (Right Side of Back) */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            shrink: 0;
        }
        .qr-section-title {
            font-size: 7.5px;
            font-weight: 800;
            color: #eab308;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .qr-white-box {
            background: #ffffff;
            padding: 6px;
            border-radius: 10px;
            width: 86px;
            height: 86px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            border: 1px solid #eab308;
        }
        .qr-white-box img {
            width: 74px;
            height: 74px;
            display: block;
            image-rendering: pixelated;
        }

        .back-footer {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            color: #64748b;
            border-t border-gray-800/80 pt-2;
        }
        .back-footer-info { display: flex; gap: 10px; }
        .back-footer-web { color: #eab308; font-weight: 700; font-family: 'JetBrains Mono', monospace; }

        /* ─── ACTION BUTTONS BAR ─── */
        .actions-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            max-width: 900px;
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .btn:hover { transform: translateY(-2px); }

        .btn-back { background: #1e293b; color: #cbd5e1; border: 1px solid #334155; }
        .btn-back:hover { background: #334155; color: #fff; }

        .btn-flip { background: #eab308; color: #0f172a; }
        .btn-flip:hover { background: #ca8a04; }

        .btn-toggle { background: #3b82f6; color: #fff; }
        .btn-toggle:hover { background: #2563eb; }

        .btn-dl-front { background: #10b981; color: #fff; }
        .btn-dl-front:hover { background: #059669; }

        .btn-dl-back { background: #8b5cf6; color: #fff; }
        .btn-dl-back:hover { background: #7c3aed; }

        .btn-dl-qr { background: #06b6d4; color: #fff; }
        .btn-dl-qr:hover { background: #0891b2; }

        .btn-print { background: #d4ff00; color: #0f172a; }
        .btn-print:hover { background: #bada00; }

        @media print {
            body { background: white; padding: 0; }
            .actions-bar { display: none !important; }
            .card-wrapper { perspective: none; width: 100%; height: auto; }
            .card-inner { display: flex; gap: 20px; transform: none !important; }
            .card-face { position: relative !important; transform: none !important; backface-visibility: visible !important; }
        }
    </style>
</head>
<body>

    @php
        // ── Determinasi Tier Badge & Diskon Promo Mitra ──
        $latestTransaction = $member->transactions()->with('package')->latest()->first();
        $packageName = $latestTransaction?->package?->name ?? $member->member_type ?? 'Basic Plan';
        $packageNameLower = strtolower(trim($packageName));

        if (str_contains($packageNameLower, 'pro plan') || (str_contains($packageNameLower, 'pro') && !str_contains($packageNameLower, 'couple'))) {
            $tierBadge = 'GOLD';
            $tierDiscount = 5;
            $tierBadgeClass = 'badge-gold';
        } elseif (str_contains($packageNameLower, 'elite plan') || str_contains($packageNameLower, 'elite')) {
            $tierBadge = 'PLATINUM';
            $tierDiscount = 10;
            $tierBadgeClass = 'badge-platinum';
        } elseif (str_contains($packageNameLower, 'vvip') || str_contains($packageNameLower, 'vvip member')) {
            $tierBadge = 'PLATINUM+';
            $tierDiscount = 15;
            $tierBadgeClass = 'badge-platinum-plus';
        } else {
            // Basic Plan, Reguler, Power Couple, Non Member
            $tierBadge = 'SILVER';
            $tierDiscount = 0;
            $tierBadgeClass = 'badge-silver';
        }

        $joinDate = $member->registration_date ? \Carbon\Carbon::parse($member->registration_date)->format('d-m-Y') : date('d-m-Y');
        $logoUrl = asset('asset/logo_kta1.jpeg');

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

    {{-- Main Container Card --}}
    <div class="card-container-wrapper" id="main-card-container">
        <div class="card-wrapper" id="cardWrapper" onclick="toggleCardFlip(event)">
            <div class="card-inner">

                {{-- ══════════════════════════════════════════════════════════
                     1. TAMPAK DEPAN (FRONT FACE - WHITE LUXURY GOLD)
                ══════════════════════════════════════════════════════════ --}}
                <div class="card-face card-front" id="card-front-element">
                    <div class="bg-wave-top-gold"></div>
                    <div class="bg-wave-top"></div>
                    <div class="bg-wave-bottom-gold"></div>
                    <div class="bg-wave-bottom"></div>
                    <div class="watermark-silhouette"></div>

                    {{-- Front Header --}}
                    <div class="front-header">
                        <div class="brand-box">
                            <img src="{{ $logoUrl }}" alt="Logo BisaGym" class="brand-logo">
                            <div class="brand-text">
                                <div class="brand-title">BISA <span>GYM</span></div>
                                <div class="brand-tagline">24/25 HOURS &bull; SEHAT ITU MUDAH</div>
                            </div>
                        </div>
                    </div>

                    {{-- Front Body --}}
                    <div class="front-body">
                        <div class="photo-column">
                            <div class="photo-frame">
                                <div class="photo-img">
                                    @if($member->photo_path)
                                        <img src="{{ Storage::url($member->photo_path) }}" alt="{{ $member->name }}">
                                    @else
                                        👤
                                    @endif
                                </div>
                            </div>
                            <div class="tier-badge {{ $tierBadgeClass }}">{{ $tierBadge }}</div>
                        </div>

                        <div class="info-column">
                            <div class="member-fullname">{{ Str::limit($member->name, 22) }}</div>
                            
                            <div class="info-row">
                                <span class="label">Gender</span>: 
                                <strong>{{ $member->gender === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</strong>
                            </div>

                            <div class="info-row">
                                <span class="label">TTL</span>: 
                                <span>{{ Str::limit($member->place_of_birth ?? '-', 10) }}, {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '-' }}</span>
                            </div>

                            <div class="info-row">
                                <span class="label">No. WA</span>: 
                                <span style="font-family: 'JetBrains Mono', monospace; font-weight:700;">{{ $member->phone }}</span>
                            </div>

                            <div class="info-row">
                                <span class="label">Alamat</span>: 
                                <span>{{ Str::limit($member->address, 28) }}</span>
                            </div>

                            <div class="join-badge">
                                ⚡ JOIN [ {{ $joinDate }} ]
                            </div>
                        </div>
                    </div>

                    {{-- Front Footer --}}
                    <div class="front-footer">
                        <div style="font-size:7.5px; color:#64748b; font-weight:600; text-transform:uppercase;">OFFICIAL MEMBER CARD</div>
                        <div class="group-logo-text">BRILLIANT <span>INDONESIA GROUP</span></div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════
                     2. TAMPAK BELAKANG (BACK FACE - LUXURY BLACK GOLD)
                ══════════════════════════════════════════════════════════ --}}
                <div class="card-face card-back" id="card-back-element">
                    <div class="gold-line-wave"></div>

                    {{-- Back Header --}}
                    <div class="back-header">
                        <div>
                            <div class="back-title">MEMBER CARD</div>
                            <div class="back-vip">{{ $member->member_id }}</div>
                        </div>
                        <img src="{{ $logoUrl }}" alt="Logo Emas" class="back-logo-img">
                    </div>

                    {{-- Back Main Body --}}
                    <div class="back-main">
                        @if($tierDiscount > 0)
                            {{-- Tampilkan Promo Voucher untuk Tier Gold (5%), Platinum (10%), Platinum+ (15%) --}}
                            <div class="promo-voucher-box">
                                <div class="promo-header">
                                    <div class="promo-title">
                                        🎁 VOUCHER PROMO MITRA
                                    </div>
                                    <div class="promo-discount-tag">DISCOUNT {{ $tierDiscount }}%</div>
                                </div>

                                <div class="promo-item">
                                    <div class="promo-item-name">🎓 Brilliant English Course</div>
                                    <div class="promo-item-disc">Diskon {{ $tierDiscount }}%</div>
                                </div>

                                <div class="promo-item">
                                    <div class="promo-item-name">☕ Bicopis Coffee</div>
                                    <div class="promo-item-disc">Diskon {{ $tierDiscount }}%</div>
                                </div>

                                <div class="promo-footer-hint">
                                    *Tunjukkan E-Card ini saat melakukan transaksi di mitra kami.
                                </div>
                            </div>
                        @else
                            {{-- Tier Silver / Basic Plan / Without Promo --}}
                            <div class="standard-member-box">
                                <div class="standard-member-title">BISA GYM MEMBERSHIP</div>
                                <div class="standard-member-text">
                                    "Sehat Itu Mudah. Tunjukkan E-Card ini kepada petugas receptionist saat melakukan absensi harian."
                                </div>
                                <div style="font-size:8px; color:#eab308; font-weight:700; margin-top:2px;">
                                    Paket: {{ strtoupper($packageName) }}
                                </div>
                            </div>
                        @endif

                        {{-- QR Code Section --}}
                        <div class="qr-section">
                            <div class="qr-section-title">SCAN YOUR CODE</div>
                            <div class="qr-white-box">
                                @if($qrExists)
                                    <img src="{{ $qrPublicUrl }}?v={{ filemtime($qrStoragePath) }}"
                                         alt="QR Code {{ $member->member_id }}"
                                         width="74" height="74">
                                @else
                                    <div style="width:74px;height:74px;display:flex;align-items:center;justify-content:center;font-size:9px;color:#999;text-align:center;">
                                        QR Code
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Back Footer --}}
                    <div class="back-footer">
                        <div class="back-footer-info">
                            <span>📍 Gym & Fitness Center</span>
                            <span>📞 {{ $member->phone }}</span>
                        </div>
                        <div class="back-footer-web">www.bisagym.com</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Action Buttons Bar --}}
    <div class="actions-bar">
        <a href="{{ route('members.index') }}" class="btn btn-back">
            ← Kembali
        </a>

        <button type="button" class="btn btn-flip" id="btn-flip">
            🔄 Balik Kartu (Flip 3D)
        </button>

        <button type="button" class="btn btn-toggle" id="btn-toggle-side">
            👁️ Tampilan Berdampingan
        </button>

        <button type="button" class="btn btn-dl-front" id="btn-dl-front">
            ↓ Depan (PNG)
        </button>

        <button type="button" class="btn btn-dl-back" id="btn-dl-back">
            ↓ Belakang (PNG)
        </button>

        <button type="button" class="btn btn-dl-qr" id="btn-dl-qr">
            ↓ QR Code (PNG)
        </button>

        <button type="button" class="btn btn-print" onclick="window.print()">
            🖨️ Cetak E-Card
        </button>
    </div>

    <script>
        const cardWrapper = document.getElementById('cardWrapper');
        const mainContainer = document.getElementById('main-card-container');
        const btnFlip = document.getElementById('btn-flip');
        const btnToggleSide = document.getElementById('btn-toggle-side');

        let isSideBySide = false;

        function toggleCardFlip(e) {
            if (isSideBySide) return;
            cardWrapper.classList.toggle('flipped');
        }

        btnFlip.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isSideBySide) {
                isSideBySide = false;
                mainContainer.classList.remove('side-by-side-mode');
                btnToggleSide.textContent = '👁️ Tampilan Berdampingan';
            }
            cardWrapper.classList.toggle('flipped');
        });

        btnToggleSide.addEventListener('click', function() {
            isSideBySide = !isSideBySide;
            if (isSideBySide) {
                mainContainer.classList.add('side-by-side-mode');
                btnToggleSide.textContent = '🔄 Tampilan Mode 3D';
            } else {
                mainContainer.classList.remove('side-by-side-mode');
                btnToggleSide.textContent = '👁️ Tampilan Berdampingan';
            }
        });

        // ── Download Front Side PNG ──
        document.getElementById('btn-dl-front').addEventListener('click', function(e) {
            e.stopPropagation();
            const frontEl = document.getElementById('card-front-element');
            html2canvas(frontEl, {
                scale: 3,
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `E-Card_Depan_${memberVipId()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        // ── Download Back Side PNG ──
        document.getElementById('btn-dl-back').addEventListener('click', function(e) {
            e.stopPropagation();
            const backEl = document.getElementById('card-back-element');
            html2canvas(backEl, {
                scale: 3,
                backgroundColor: '#090d16',
                logging: false,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `E-Card_Belakang_${memberVipId()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        // ── Download QR Code PNG ──
        document.getElementById('btn-dl-qr').addEventListener('click', function(e) {
            e.stopPropagation();
            const qrBox = document.querySelector('.qr-white-box');
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
