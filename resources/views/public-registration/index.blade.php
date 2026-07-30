<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Form Pendaftaran Member Baru - BisaGym</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        input[type="date"], select, textarea {
            color-scheme: dark;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.85) sepia(1) saturate(5) hue-rotate(35deg);
            cursor: pointer;
            opacity: 0.9;
            padding: 2px;
            border-radius: 4px;
            transition: all 0.2s ease-in-out;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            filter: invert(1) brightness(1.3);
            opacity: 1;
            transform: scale(1.15);
            background-color: rgba(212, 255, 0, 0.2);
        }
    </style>
</head>
<body class="font-sans antialiased bg-darker text-gray-100 min-h-screen selection:bg-neon selection:text-darker">
    
    <!-- Top Branding Header -->
    <header class="bg-card/80 backdrop-blur border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-neon flex items-center justify-center text-darker font-black text-xl shadow-lg shadow-neon/20">
                    <i class="ph ph-barbell"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black text-white leading-none tracking-tight">BISA<span class="text-neon">GYM</span></h1>
                    <p class="text-[10px] text-gray-400 font-medium">Self-Service Member Registration</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-mono text-gray-400 bg-dark px-3 py-1.5 rounded-lg border border-gray-800">
                <i class="ph ph-calendar text-neon"></i> {{ date('d M Y') }}
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Welcome Banner -->
        <div class="mb-8 bg-gradient-to-r from-dark to-gray-900 rounded-2xl border border-gray-800 p-6 shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-xl sm:text-2xl font-black text-white mb-1">Selamat Datang Calon Member! 👋</h2>
                <p class="text-xs sm:text-sm text-gray-400">Silakan isi formulir pendaftaran di bawah ini untuk menjadi member resmi BisaGym.</p>
            </div>
            <div class="absolute -right-6 -bottom-6 opacity-10 text-neon pointer-events-none">
                <i class="ph ph-user-plus text-9xl"></i>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-5 rounded-2xl bg-green-500/10 border border-green-500/40 text-green-400 flex items-start justify-between shadow-xl shadow-green-500/5">
                <div class="flex items-start gap-3">
                    <i class="ph ph-check-circle text-3xl text-green-400 mt-0.5"></i> 
                    <div>
                        <h4 class="font-bold text-base text-white mb-1">Pendaftaran Sukses!</h4>
                        <p class="text-sm text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-400/60 hover:text-green-400 p-1"><i class="ph ph-x text-lg"></i></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 p-5 rounded-2xl bg-red-500/10 border border-red-500/40 text-red-400 text-sm shadow-xl shadow-red-500/5">
                <div class="flex items-center gap-2 font-bold mb-2 text-base">
                    <i class="ph ph-warning-circle text-xl"></i>
                    <span>Terdapat kesalahan pada isian formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('public.registration.store') }}" id="memberForm" class="space-y-8">
            @csrf

            {{-- SECTION 1: PILIH PAKET --}}
            <div class="bg-card rounded-2xl border border-gray-800 p-6 md:p-8 shadow-xl">
                <h3 class="text-white font-bold text-base mb-4 border-b border-gray-800 pb-3 flex items-center gap-2">
                    <i class="ph ph-package text-neon text-xl"></i> 1. Pilih Paket Membership
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    @foreach($packages as $pkg)
                        <label class="relative block cursor-pointer group" for="pkg_{{ $pkg->id }}">
                            <input type="radio"
                                id="pkg_{{ $pkg->id }}"
                                name="package_id"
                                value="{{ $pkg->id }}"
                                data-max-members="{{ $pkg->max_members }}"
                                data-category="{{ $pkg->category }}"
                                class="peer sr-only"
                                required
                                {{ old('package_id') == $pkg->id ? 'checked' : '' }}>

                            <div class="rounded-2xl border-2 border-gray-800 bg-dark p-5 shadow-sm transition-all duration-200
                                        peer-checked:border-neon peer-checked:bg-neon/10 peer-checked:shadow-neon/20 peer-checked:shadow-lg
                                        group-hover:border-neon/50 h-full flex flex-col justify-between">

                                @if($pkg->max_members >= 2)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-pink-500/20 text-pink-400 border border-pink-500/30 rounded-full px-2.5 py-0.5 mb-3 w-fit">
                                        <i class="ph ph-users"></i> Couple {{ $pkg->max_members }} orang
                                    </span>
                                @endif

                                <div>
                                    <span class="block text-base font-bold text-white mb-1">{{ $pkg->name }}</span>
                                    <span class="flex items-center text-xs text-gray-400 uppercase tracking-wide">
                                        Durasi: {{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }}
                                    </span>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-800/60 flex items-center justify-between">
                                    <span class="text-xs text-gray-400">Harga</span>
                                    <span class="text-lg font-black text-neon font-mono">
                                        Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <i class="ph-fill ph-check-circle text-neon text-2xl
                                      opacity-0 peer-checked:opacity-100 transition-all duration-200
                                      absolute top-4 right-4 drop-shadow-[0_0_6px_rgba(212,255,0,0.8)]"></i>
                        </label>
                    @endforeach
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 border border-gray-800 bg-dark/60 rounded-xl">
                        <label for="discount_category" class="block text-xs font-semibold text-neon mb-2">
                            <i class="ph ph-percent mr-1"></i> Diskon Profesi (Opsional)
                        </label>
                        <select id="discount_category" name="discount_category" autocomplete="off" class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">
                            <option value="0">Tidak Ada Diskon (0%)</option>
                            <option value="10">Pelajar, PNS, TNI, dan POLRI (10%)</option>
                            <option value="15">Pelaku Budaya, Dukun, dan Ulama (15%)</option>
                            <option value="20">Guru / Tenaga Pendidik (20%)</option>
                        </select>
                    </div>
                    
                    <div class="p-4 border border-gray-800 bg-dark/60 rounded-xl">
                        <label for="activation_date" class="block text-xs font-semibold text-neon mb-2">
                            <i class="ph ph-calendar-check mr-1"></i> Tanggal Mulai Aktif
                        </label>
                        <input type="date" id="activation_date" name="activation_date" autocomplete="off" class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            {{-- SECTION 2: MEMBER PERTAMA --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Webcam Member 1 --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-card rounded-2xl border border-gray-800 p-6 shadow-xl text-center">
                        <h3 class="text-white font-bold text-sm mb-4 text-left border-b border-gray-800 pb-2 flex items-center gap-2">
                            <i class="ph ph-camera text-neon"></i> Foto Profil Member
                        </h3>

                        <label for="camera-select" class="sr-only">Pilih Kamera Member 1</label>
                        <select id="camera-select" class="w-full mb-4 bg-dark border-gray-700 text-white rounded-xl text-xs hidden"></select>

                        <div class="relative w-full aspect-[3/4] bg-dark rounded-xl overflow-hidden border border-gray-800 flex items-center justify-center mb-4 shadow-inner">
                            <video id="webcam" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                            <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden" alt="Preview Foto Member 1" />
                            <div id="camera-placeholder" class="text-gray-500 flex flex-col items-center p-4">
                                <i class="ph ph-camera text-5xl mb-2 text-gray-600"></i>
                                <span class="text-xs">Foto belum diambil</span>
                            </div>
                        </div>

                        <input type="hidden" name="photo_data" id="photo_data">

                        <div class="flex flex-wrap gap-2 justify-center">
                            <button type="button" id="start-camera" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5">
                                <i class="ph ph-video-camera text-base"></i> <span>Kamera</span>
                            </button>
                            
                            <label for="upload-photo" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5 cursor-pointer">
                                <i class="ph ph-upload-simple text-base"></i> <span>Upload</span>
                                <input type="file" id="upload-photo" accept="image/*" class="hidden">
                            </label>

                            <button type="button" id="take-photo" class="flex-1 bg-neon hover:bg-[#c4e600] text-darker font-bold py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5 hidden">
                                <i class="ph ph-camera text-base"></i> <span>Ambil Foto</span>
                            </button>
                            <button type="button" id="retake-photo" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5 hidden">
                                <i class="ph ph-arrows-clockwise text-base"></i> <span>Ulangi</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Member 1 --}}
                <div class="lg:col-span-2">
                    <div class="bg-card rounded-2xl border border-gray-800 p-6 md:p-8 shadow-xl">
                        <h3 class="text-white font-bold text-sm mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                            <i class="ph ph-user text-neon"></i> Data Diri Calon Member
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-xs font-semibold text-gray-300 mb-1">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" autocomplete="name" value="{{ old('name') }}" required
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs" placeholder="Sesuai KTP">
                            </div>
                            <div class="md:col-span-2">
                                <label for="nik" class="block text-xs font-semibold text-gray-300 mb-1">NIK (16 Digit KTP) *</label>
                                <input type="text" id="nik" name="nik" autocomplete="off" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs font-mono" placeholder="351501...">
                            </div>
                            <div>
                                <label for="place_of_birth" class="block text-xs font-semibold text-gray-300 mb-1">Tempat Lahir *</label>
                                <input type="text" id="place_of_birth" name="place_of_birth" autocomplete="address-level2" value="{{ old('place_of_birth') }}" required
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">
                            </div>
                            <div>
                                <label for="dob" class="block text-xs font-semibold text-gray-300 mb-1">Tanggal Lahir *</label>
                                <input type="date" id="dob" name="date_of_birth" autocomplete="bday" value="{{ old('date_of_birth') }}" required
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">
                                <p class="text-[10px] text-gray-500 mt-1" id="age-helper">Minimal umur 5 tahun</p>
                            </div>
                            <div>
                                <label for="gender" class="block text-xs font-semibold text-gray-300 mb-1">Jenis Kelamin *</label>
                                <select id="gender" name="gender" autocomplete="sex" required class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">
                                    <option value="">Pilih...</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label for="job" class="block text-xs font-semibold text-gray-300 mb-1">Pekerjaan</label>
                                <input type="text" id="job" name="job" autocomplete="organization-title" value="{{ old('job') }}" placeholder="Swasta / Pelajar / dll"
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-semibold text-gray-300 mb-1">No. WhatsApp / HP *</label>
                                <input type="text" id="phone" name="phone" autocomplete="tel" value="{{ old('phone') }}" required placeholder="081234567890"
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs font-mono">
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-300 mb-1">Alamat Email *</label>
                                <input type="email" id="email" name="email" autocomplete="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-xs font-semibold text-gray-300 mb-1">Alamat Domisili *</label>
                                <textarea id="address" name="address" autocomplete="street-address" rows="3" required placeholder="Alamat tempat tinggal saat ini"
                                    class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-neon focus:border-neon text-xs">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: MEMBER KEDUA (Couple) --}}
            <div id="couple-section" class="hidden transition-all duration-500 overflow-hidden">
                <div class="relative pt-4">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-1 h-px bg-gradient-to-r from-transparent via-pink-500/50 to-transparent"></div>
                        <span class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-pink-500/10 border border-pink-500/30 text-pink-400 text-xs font-bold">
                            <i class="ph ph-users"></i> Data Member Ke-2 (Pasangan Couple)
                        </span>
                        <div class="flex-1 h-px bg-gradient-to-r from-transparent via-pink-500/50 to-transparent"></div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1">
                            <div class="bg-card rounded-2xl border border-pink-500/20 p-6 shadow-xl text-center">
                                <h3 class="text-white font-bold text-sm mb-4 text-left border-b border-pink-500/20 pb-2 flex items-center gap-2">
                                    <i class="ph ph-camera text-pink-400"></i> Foto Member Ke-2
                                </h3>

                                <label for="camera-select2" class="sr-only">Pilih Kamera Member 2</label>
                                <select id="camera-select2" class="w-full mb-4 bg-dark border-gray-700 text-white rounded-xl text-xs hidden"></select>

                                <div class="relative w-full aspect-[3/4] bg-dark rounded-xl overflow-hidden border border-gray-800 flex items-center justify-center mb-4">
                                    <video id="webcam2" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                                    <img id="photo-preview2" class="absolute inset-0 w-full h-full object-cover hidden" alt="Preview Foto Member 2" />
                                    <div id="camera-placeholder2" class="text-gray-500 flex flex-col items-center">
                                        <i class="ph ph-camera text-5xl mb-2 text-gray-600"></i>
                                        <span class="text-xs">Foto belum diambil</span>
                                    </div>
                                </div>

                                <input type="hidden" name="member2_photo_data" id="photo_data2">

                                <div class="flex flex-wrap gap-2 justify-center">
                                    <button type="button" id="start-camera2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5">
                                        <i class="ph ph-video-camera text-base"></i> <span>Kamera</span>
                                    </button>
                                    
                                    <label for="upload-photo2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5 cursor-pointer">
                                        <i class="ph ph-upload-simple text-base"></i> <span>Upload</span>
                                        <input type="file" id="upload-photo2" accept="image/*" class="hidden">
                                    </label>

                                    <button type="button" id="take-photo2" class="flex-1 bg-pink-500 hover:bg-pink-400 text-white font-bold py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5 hidden">
                                        <i class="ph ph-camera text-base"></i> <span>Ambil Foto</span>
                                    </button>
                                    <button type="button" id="retake-photo2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-xl transition-colors text-xs flex items-center justify-center space-x-1.5 hidden">
                                        <i class="ph ph-arrows-clockwise text-base"></i> <span>Ulangi</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-card rounded-2xl border border-pink-500/20 p-6 md:p-8 shadow-xl">
                                <h3 class="text-white font-bold text-sm mb-4 border-b border-pink-500/20 pb-2 flex items-center gap-2">
                                    <i class="ph ph-user text-pink-400"></i> Data Diri Member Ke-2
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="member2_name" class="block text-xs font-semibold text-gray-300 mb-1">Nama Lengkap *</label>
                                        <input type="text" id="member2_name" name="member2_name" autocomplete="name" value="{{ old('member2_name') }}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs" placeholder="Sesuai KTP">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="member2_nik" class="block text-xs font-semibold text-gray-300 mb-1">NIK (16 Digit KTP) *</label>
                                        <input type="text" id="member2_nik" name="member2_nik" autocomplete="off" value="{{ old('member2_nik') }}" minlength="16" maxlength="16" pattern="\d{16}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs font-mono" placeholder="351501...">
                                    </div>
                                    <div>
                                        <label for="member2_place_of_birth" class="block text-xs font-semibold text-gray-300 mb-1">Tempat Lahir *</label>
                                        <input type="text" id="member2_place_of_birth" name="member2_place_of_birth" autocomplete="address-level2" value="{{ old('member2_place_of_birth') }}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs">
                                    </div>
                                    <div>
                                        <label for="dob2" class="block text-xs font-semibold text-gray-300 mb-1">Tanggal Lahir *</label>
                                        <input type="date" id="dob2" name="member2_date_of_birth" autocomplete="bday" value="{{ old('member2_date_of_birth') }}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs">
                                        <p class="text-[10px] text-gray-500 mt-1" id="age-helper2">Minimal umur 5 tahun</p>
                                    </div>
                                    <div>
                                        <label for="member2_gender" class="block text-xs font-semibold text-gray-300 mb-1">Jenis Kelamin *</label>
                                        <select id="member2_gender" name="member2_gender" autocomplete="sex" class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs">
                                            <option value="">Pilih...</option>
                                            <option value="L" {{ old('member2_gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('member2_gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="member2_job" class="block text-xs font-semibold text-gray-300 mb-1">Pekerjaan</label>
                                        <input type="text" id="member2_job" name="member2_job" autocomplete="organization-title" value="{{ old('member2_job') }}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs">
                                    </div>
                                    <div>
                                        <label for="member2_phone" class="block text-xs font-semibold text-gray-300 mb-1">No. WhatsApp / HP *</label>
                                        <input type="text" id="member2_phone" name="member2_phone" autocomplete="tel" value="{{ old('member2_phone') }}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs font-mono">
                                    </div>
                                    <div>
                                        <label for="member2_email" class="block text-xs font-semibold text-gray-300 mb-1">Alamat Email *</label>
                                        <input type="email" id="member2_email" name="member2_email" autocomplete="email" value="{{ old('member2_email') }}"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="member2_address" class="block text-xs font-semibold text-gray-300 mb-1">Alamat Domisili *</label>
                                        <textarea id="member2_address" name="member2_address" autocomplete="street-address" rows="3"
                                            class="w-full border-gray-700 rounded-xl bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-xs">{{ old('member2_address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" id="submit-btn" class="w-full sm:w-auto px-8 py-4 bg-neon hover:bg-[#c4e600] text-darker font-extrabold rounded-2xl transition-all text-base shadow-xl shadow-neon/20 flex items-center justify-center space-x-2 active:scale-98">
                    <i class="ph ph-paper-plane-right text-xl"></i>
                    <span id="submit-label">Kirim Pendaftaran Member</span>
                </button>
            </div>
        </form>
    </main>

    <footer class="text-center py-6 text-xs text-gray-600 border-t border-gray-900 mt-12">
        <p>&copy; {{ date('Y') }} BisaGym Management System. All Rights Reserved.</p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const coupleSection  = document.getElementById('couple-section');
        const submitLabel    = document.getElementById('submit-label');
        const radioInputs    = document.querySelectorAll('input[name="package_id"]');

        function toggleCoupleSection(maxMembers) {
            const isCouple = parseInt(maxMembers) >= 2;

            if (isCouple) {
                coupleSection.classList.remove('hidden');
                setMember2Required(true);
                submitLabel.textContent = 'Kirim Pendaftaran (Paket Couple 2 Member)';
            } else {
                coupleSection.classList.add('hidden');
                setMember2Required(false);
                submitLabel.textContent = 'Kirim Pendaftaran Member';
            }
        }

        function setMember2Required(required) {
            const fields = ['member2_name','member2_place_of_birth','member2_date_of_birth',
                            'member2_gender','member2_nik','member2_address','member2_phone','member2_email'];
            fields.forEach(name => {
                const el = document.querySelector(`[name="${name}"]`);
                if (el) el.required = required;
            });
        }

        radioInputs.forEach(radio => {
            radio.addEventListener('change', function () {
                toggleCoupleSection(this.dataset.maxMembers || 1);
            });
        });

        const checkedRadio = document.querySelector('input[name="package_id"]:checked');
        if (checkedRadio) {
            toggleCoupleSection(checkedRadio.dataset.maxMembers || 1);
        }

        // Setup Webcam
        setupWebcam({
            videoId: 'webcam', previewId: 'photo-preview', placeholderId: 'camera-placeholder',
            inputId: 'photo_data', startBtnId: 'start-camera', takeBtnId: 'take-photo',
            retakeBtnId: 'retake-photo', selectId: 'camera-select', uploadInputId: 'upload-photo'
        });

        setupWebcam({
            videoId: 'webcam2', previewId: 'photo-preview2', placeholderId: 'camera-placeholder2',
            inputId: 'photo_data2', startBtnId: 'start-camera2', takeBtnId: 'take-photo2',
            retakeBtnId: 'retake-photo2', selectId: 'camera-select2', uploadInputId: 'upload-photo2'
        });

        function setupWebcam({ videoId, previewId, placeholderId, inputId, startBtnId, takeBtnId, retakeBtnId, selectId, uploadInputId }) {
            const video       = document.getElementById(videoId);
            const preview     = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            const photoInput  = document.getElementById(inputId);
            const startBtn    = document.getElementById(startBtnId);
            const takeBtn     = document.getElementById(takeBtnId);
            const retakeBtn   = document.getElementById(retakeBtnId);
            const cameraSelect= document.getElementById(selectId);
            const uploadInput = uploadInputId ? document.getElementById(uploadInputId) : null;
            let stream = null;

            if (uploadInput) {
                uploadInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = new Image();
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            const maxW = 600;
                            const maxH = 800;

                            if (width > maxW) {
                                height = Math.round(height * (maxW / width));
                                width = maxW;
                            }
                            if (height > maxH) {
                                width = Math.round(width * (maxH / height));
                                height = maxH;
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                            preview.src = compressedDataUrl;
                            photoInput.value = compressedDataUrl;
                            
                            video.classList.add('hidden');
                            placeholder.classList.add('hidden');
                            preview.classList.remove('hidden');
                            
                            if (stream) stream.getTracks().forEach(t => t.stop());
                            startBtn.classList.add('hidden');
                            takeBtn.classList.add('hidden');
                            if (cameraSelect) cameraSelect.classList.add('hidden');
                            retakeBtn.classList.remove('hidden');
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (!video || !startBtn) return;

            async function startCamera(deviceId = null) {
                if (stream) stream.getTracks().forEach(t => t.stop());
                
                const constraints = {
                    video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'user' },
                    audio: false
                };

                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = stream;
                    video.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    startBtn.classList.add('hidden');
                    takeBtn.classList.remove('hidden');
                } catch (err) {
                    alert('Kamera tidak dapat diakses: ' + err.message);
                }
            }

            startBtn.addEventListener('click', async function () {
                try {
                    let devices = await navigator.mediaDevices.enumerateDevices();
                    let videoInputs = devices.filter(d => d.kind === 'videoinput');
                    let hasLabels = videoInputs.some(d => d.label !== '');

                    if (!hasLabels) {
                        const initStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        initStream.getTracks().forEach(t => t.stop());
                        devices = await navigator.mediaDevices.enumerateDevices();
                        videoInputs = devices.filter(d => d.kind === 'videoinput');
                    }

                    if (cameraSelect && videoInputs.length > 0) {
                        cameraSelect.innerHTML = '';
                        videoInputs.forEach((device, index) => {
                            const option = document.createElement('option');
                            option.value = device.deviceId;
                            option.text = device.label || `Kamera ${index + 1}`;
                            cameraSelect.appendChild(option);
                        });
                        cameraSelect.classList.remove('hidden');
                        startCamera(videoInputs[0].deviceId);
                    } else {
                        startCamera();
                    }
                } catch (err) {
                    alert('Izin kamera ditolak: ' + err.message);
                }
            });

            if (cameraSelect) {
                cameraSelect.addEventListener('change', function() {
                    startCamera(this.value);
                });
            }

            takeBtn.addEventListener('click', function () {
                const canvas = document.createElement('canvas');
                let width = video.videoWidth || 480;
                let height = video.videoHeight || 640;
                const maxW = 600;
                const maxH = 800;

                if (width > maxW) {
                    height = Math.round(height * (maxW / width));
                    width = maxW;
                }
                if (height > maxH) {
                    width = Math.round(width * (maxH / height));
                    height = maxH;
                }

                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(video, 0, 0, width, height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                preview.src = dataUrl;
                photoInput.value = dataUrl;
                video.classList.add('hidden');
                preview.classList.remove('hidden');
                takeBtn.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
                if (cameraSelect) cameraSelect.classList.add('hidden');
                if (stream) stream.getTracks().forEach(t => t.stop());
            });

            retakeBtn.addEventListener('click', async function () {
                preview.classList.add('hidden');
                photoInput.value = '';
                retakeBtn.classList.add('hidden');
                takeBtn.classList.remove('hidden');
                if (cameraSelect && cameraSelect.options.length > 0) {
                    cameraSelect.classList.remove('hidden');
                    startCamera(cameraSelect.value);
                } else {
                    startCamera();
                }
            });
        }

        // Age Helper
        function setupAgeHelper(dobId, helperId) {
            const dobInput  = document.getElementById(dobId);
            const ageHelper = document.getElementById(helperId);
            if (!dobInput) return;
            dobInput.addEventListener('change', function () {
                if (!this.value) return;
                const dob   = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;

                if (age < 5) {
                    ageHelper.textContent = `Umur: ${age} tahun ⚠ Minimal 5 tahun!`;
                    ageHelper.className = 'text-[10px] text-red-400 mt-1';
                } else {
                    ageHelper.textContent = `Umur: ${age} tahun ✓ Memenuhi syarat`;
                    ageHelper.className = 'text-[10px] text-green-400 mt-1';
                }
            });
        }

        setupAgeHelper('dob', 'age-helper');
        setupAgeHelper('dob2', 'age-helper2');
    });
    </script>
</body>
</html>
