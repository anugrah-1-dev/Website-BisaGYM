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
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(234,179,8,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(168,85,247,0.05) 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            gap: 28px;
            font-family: 'Inter', sans-serif;
            padding: 32px 24px;
            color: #f8fafc;
        }

        /* ─── ACTION BUTTONS BAR (TOP) ─── */
        .actions-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
            max-width: 1000px;
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

        .btn-flip { background: linear-gradient(135deg, #f59e0b, #d97706); color: #0f172a; }
        .btn-flip:hover { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; }

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


        /* ─── 3D CONTAINER & FLIP ─── */
        .card-container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 100%;
            margin-top: 10px;
        }

        .card-wrapper {
            perspective: 1200px;
            width: 500px;
            height: 315px;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            -webkit-transform-style: preserve-3d;
        }

        .card-wrapper.flipped .card-inner {
            transform: rotateY(180deg);
            -webkit-transform: rotateY(180deg);
        }

        /* ─── SIDE BY SIDE MODE ─── */
        .side-by-side-mode .card-wrapper {
            width: 100% !important;
            max-width: 1050px;
            height: auto !important;
            perspective: none !important;
            cursor: default;
        }
        .side-by-side-mode .card-inner {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 28px !important;
            transform: none !important;
            -webkit-transform: none !important;
            transition: none !important;
            perspective: none !important;
        }
        .side-by-side-mode .card-face {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            width: 500px !important;
            height: 315px !important;
            transform: none !important;
            -webkit-transform: none !important;
            backface-visibility: visible !important;
            -webkit-backface-visibility: visible !important;
            opacity: 1 !important;
        }

        /* ─── CARD FACE COMMON ─── */
        .card-face {
            position: absolute;
            top: 0;
            left: 0;
            width: 500px;
            height: 315px;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: 22px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(234,179,8,0.3),
                0 25px 50px rgba(0,0,0,0.8),
                0 0 40px rgba(234,179,8,0.12);
        }

        /* ══════════════════════════════════════════════════════════
           TAMPAK DEPAN (FRONT FACE - LUXURY BLACK & GOLD SWOOSH)
        ══════════════════════════════════════════════════════════ */
        .card-front {
            background: #090d14;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 18px 22px;
            transform: rotateY(0deg);
            -webkit-transform: rotateY(0deg);
            position: relative;
        }

        /* Metallic Gold Curved Waves (Top & Bottom Swoosh) */
        .card-front .swoosh-top {
            position: absolute;
            top: 0; right: 0; left: 0;
            height: 95px;
            background: linear-gradient(135deg, rgba(15,23,42,0.9) 0%, rgba(9,13,20,0.95) 60%),
                        linear-gradient(90deg, #ca8a04 0%, #eab308 50%, #fef08a 100%);
            background-blend-mode: overlay;
            clip-path: polygon(0 0, 100% 0, 100% 70%, 0% 100%);
            z-index: 0;
        }
        .card-front .swoosh-top-gold {
            position: absolute;
            top: 0; right: 0; left: 0;
            height: 102px;
            background: linear-gradient(90deg, #ca8a04 0%, #eab308 40%, #fef08a 80%, #ca8a04 100%);
            clip-path: polygon(0 0, 100% 0, 100% 76%, 0% 105%);
            z-index: 0;
            opacity: 0.9;
        }
        .card-front .swoosh-bottom-gold {
            position: absolute;
            bottom: 0; right: 0; left: 0;
            height: 52px;
            background: linear-gradient(90deg, #ca8a04 0%, #eab308 50%, #fef08a 100%);
            clip-path: polygon(0 35%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
            opacity: 0.9;
        }
        .card-front .swoosh-bottom {
            position: absolute;
            bottom: 0; right: 0; left: 0;
            height: 46px;
            background: linear-gradient(135deg, #090d14 0%, #0f172a 100%);
            clip-path: polygon(0 45%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
        }

        /* Header Right Alignment */
        .front-header-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .gym-header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: right;
        }
        .gym-logo-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fef08a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            background: #000;
        }
        .gym-title-text {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .gym-main-name {
            font-family: 'Orbitron', sans-serif;
            font-size: 17px;
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
            font-size: 8px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Front Body: Photo Left, Details Right */
        .front-main-body {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 18px;
            align-items: center;
            margin-top: 6px;
        }

        /* Left Side: Photo Circle & Purple-Gold Ring Frame */
        .photo-left-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            shrink: 0;
        }
        .photo-circle-ring {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #a855f7 0%, #eab308 50%, #fef08a 100%);
            box-shadow: 0 0 20px rgba(168,85,247,0.35), 0 6px 16px rgba(0,0,0,0.6);
            position: relative;
        }
        .photo-inner-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            overflow: hidden;
        }
        .photo-inner-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Solid Black Category Label Pill Below Photo */
        .category-solid-box {
            background: #000000;
            border: 1.5px solid #eab308;
            padding: 3px 12px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            text-align: center;
        }

        /* Specific Badges styling */
        .badge-silver-style { border-color: #cbd5e1; color: #f8fafc; }
        .badge-gold-style { border-color: #eab308; color: #fef08a; }
        .badge-platinum-style { border-color: #38bdf8; color: #e0f2fe; }
        .badge-platinum-plus-style { border-color: #c084fc; color: #f472b6; }

        /* Right Side Info Data */
        .info-right-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .member-header-line {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }
        .member-name-large {
            font-size: 17px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: -0.2px;
        }
        .gender-badge-tag {
            font-size: 9px;
            font-weight: 800;
            color: #fef08a;
            background: rgba(234,179,8,0.15);
            border: 1px solid rgba(234,179,8,0.4);
            padding: 2px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            shrink: 0;
        }
        .sub-member-label {
            font-size: 9px;
            font-weight: 700;
            color: #eab308;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        /* Data Rows Table Layout */
        .data-rows-table {
            display: flex;
            flex-direction: column;
            gap: 2.5px;
            font-size: 10px;
        }
        .data-row {
            display: flex;
            align-items: center;
        }
        .data-row .col-label {
            width: 58px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
        }
        .data-row .col-colon {
            width: 12px;
            color: #eab308;
            font-weight: 800;
        }
        .data-row .col-value {
            color: #f1f5f9;
            font-weight: 600;
            flex: 1;
        }

        /* Highlighted Join Date Bar */
        .join-highlight-box {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(90deg, #eab308 0%, #ca8a04 100%);
            color: #0f172a;
            font-size: 9.5px;
            font-weight: 900;
            padding: 3px 10px;
            border-radius: 5px;
            width: fit-content;
            margin-top: 5px;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 2px 8px rgba(234,179,8,0.4);
            letter-spacing: 0.5px;
        }

        /* Front Footer Partner Logo */
        .front-footer-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 2px;
        }
        .official-card-label {
            font-size: 7.5px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .partner-group-logo {
            text-align: right;
        }
        .partner-group-title {
            font-size: 9.5px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }
        .partner-group-title span { color: #fef08a; }
        .partner-group-sub {
            font-size: 7px;
            color: #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }


        /* ══════════════════════════════════════════════════════════
           TAMPAK BELAKANG (BACK FACE - LUXURY BLACK GOLD)
        ══════════════════════════════════════════════════════════ */
        .card-back {
            background: #070a10;
            color: #f8fafc;
            padding: 18px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid rgba(234,179,8,0.3);
            transform: rotateY(180deg);
            -webkit-transform: rotateY(180deg);
            position: relative;
        }

        .card-back .gold-line-wave {
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background:
                radial-gradient(circle at 90% 10%, rgba(234,179,8,0.1) 0%, transparent 40%),
                radial-gradient(circle at 10% 90%, rgba(168,85,247,0.05) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        .back-header {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-b border-gray-800/90 pb-2;
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
            font-weight: 800;
            color: #fef08a;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }
        .back-logo-img {
            height: 30px;
            width: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #eab308;
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
            background: linear-gradient(135deg, rgba(30,41,59,0.8) 0%, rgba(15,23,42,0.95) 100%);
            border: 1px dashed rgba(234,179,8,0.45);
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.5);
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
            background: linear-gradient(90deg, #eab308, #fef08a);
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
            color: #fef08a;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
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
            width: 88px;
            height: 88px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            border: 1.5px solid #eab308;
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
        .back-footer-web { color: #eab308; font-weight: 800; font-family: 'JetBrains Mono', monospace; }

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
            $tierBadgeStyle = 'badge-gold-style';
        } elseif (str_contains($packageNameLower, 'elite plan') || str_contains($packageNameLower, 'elite')) {
            $tierBadge = 'PLATINUM';
            $tierDiscount = 10;
            $tierBadgeStyle = 'badge-platinum-style';
        } elseif (str_contains($packageNameLower, 'vvip') || str_contains($packageNameLower, 'vvip member')) {
            $tierBadge = 'PLATINUM+';
            $tierDiscount = 15;
            $tierBadgeStyle = 'badge-platinum-plus-style';
        } else {
            // Basic Plan, Reguler, Power Couple, Non Member
            $tierBadge = 'SILVER';
            $tierDiscount = 0;
            $tierBadgeStyle = 'badge-silver-style';
        }

        $joinDate = $member->registration_date ? \Carbon\Carbon::parse($member->registration_date)->format('d-m-Y') : date('d-m-Y');
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

    {{-- Main Container Card --}}
    <div class="card-container-wrapper" id="main-card-container">
        <div class="card-wrapper" id="cardWrapper" onclick="toggleCardFlip(event)">
            <div class="card-inner">

                {{-- ══════════════════════════════════════════════════════════
                     1. TAMPAK DEPAN (FRONT FACE - LUXURY BLACK & GOLD SWOOSH)
                ══════════════════════════════════════════════════════════ --}}
                <div class="card-face card-front" id="card-front-element">
                    <div class="swoosh-top-gold"></div>
                    <div class="swoosh-top"></div>
                    <div class="swoosh-bottom-gold"></div>
                    <div class="swoosh-bottom"></div>

                    {{-- Front Header (Kanan Atas) --}}
                    <div class="front-header-row">
                        <div class="gym-header-brand">
                            <div class="gym-title-text">
                                <div class="gym-main-name">BISA GYM</div>
                                <div class="gym-sub-tagline">25 HOURS &bull; SEHAT ITU MUDAH</div>
                            </div>
                            <img src="{{ $logoGymUrl }}" alt="Logo Gym" class="gym-logo-circle">
                        </div>
                    </div>

                    {{-- Front Body (Foto Kiri, Data Kanan) --}}
                    <div class="front-main-body">
                        {{-- Sisi Kiri: Foto Circle & Ring Frame + Solid Black Category Box --}}
                        <div class="photo-left-container">
                            <div class="photo-circle-ring">
                                <div class="photo-inner-img">
                                    @if($member->photo_path)
                                        <img src="{{ Storage::url($member->photo_path) }}" alt="{{ $member->name }}">
                                    @else
                                        👤
                                    @endif
                                </div>
                            </div>
                            <div class="category-solid-box {{ $tierBadgeStyle }}">
                                {{ $tierBadge }}
                            </div>
                        </div>

                        {{-- Sisi Kanan: Info Data Member --}}
                        <div class="info-right-container">
                            <div class="member-header-line">
                                <div class="member-name-large">{{ Str::limit($member->name, 20) }}</div>
                                <div class="gender-badge-tag">{{ $member->gender === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</div>
                            </div>

                            <div class="sub-member-label">Member BISA GYM</div>

                            <div class="data-rows-table">
                                <div class="data-row">
                                    <span class="col-label">TTL</span>
                                    <span class="col-colon">:</span>
                                    <span class="col-value">{{ Str::limit($member->place_of_birth ?? '-', 10) }}, {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '-' }}</span>
                                </div>

                                <div class="data-row">
                                    <span class="col-label">NO. HP</span>
                                    <span class="col-colon">:</span>
                                    <span class="col-value" style="font-family: 'JetBrains Mono', monospace;">{{ $member->phone }}</span>
                                </div>

                                <div class="data-row">
                                    <span class="col-label">ALAMAT</span>
                                    <span class="col-colon">:</span>
                                    <span class="col-value">{{ Str::limit($member->address, 26) }}</span>
                                </div>
                            </div>

                            <div class="join-highlight-box">
                                JOIN [ {{ $joinDate }} ]
                            </div>
                        </div>
                    </div>

                    {{-- Front Footer --}}
                    <div class="front-footer-row">
                        <div class="official-card-label">OFFICIAL MEMBER CARD</div>
                        <div class="partner-group-logo">
                            <div class="partner-group-title">BRILLIANT <span>INDONESIA GROUP</span></div>
                            <div class="partner-group-sub">Gym &bull; Course &bull; Coffee</div>
                        </div>
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
                        <img src="{{ $logoGymUrl }}" alt="Logo Gym Emas" class="back-logo-img">
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
                backgroundColor: '#090d14',
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
                backgroundColor: '#070a10',
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
