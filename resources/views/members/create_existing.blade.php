<x-app-layout>
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
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('members.index') }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <span>{{ __('Registrasi Member Lama (Input Manual ID)') }}</span>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('members.store-existing') }}" id="memberForm" class="space-y-6">
        @csrf

        {{-- ══════════════════════════════════════════════════════════
             SECTION 1: ID MEMBER & STATUS PEMBAYARAN
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-card rounded-2xl border border-neon/30 p-6 md:p-8 shadow-xl relative overflow-hidden space-y-6">
            <div class="absolute top-0 right-0 w-48 h-48 bg-neon/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between border-b border-gray-800/80 pb-4">
                <h3 class="text-white font-semibold text-lg md:text-xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-neon/10 border border-neon/30 text-neon flex items-center justify-center shrink-0">
                        <i class="ph ph-identification-card text-xl"></i>
                    </div>
                    <span>Identitas ID Member & Status Pembayaran</span>
                </h3>
                <span class="text-xs font-semibold text-neon/80 bg-neon/10 border border-neon/20 px-3 py-1 rounded-full uppercase tracking-wider hidden sm:inline-block">
                    Input Manual ID
                </span>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/50 text-red-400 text-sm space-y-1">
                    <div class="font-bold flex items-center gap-2 mb-1">
                        <i class="ph ph-warning-circle text-lg"></i>
                        <span>Mohon periksa kembali inputan Anda:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Field 1: ID Member Lama (Col 5) --}}
                <div class="lg:col-span-5 space-y-2">
                    <label for="member_id" class="block text-sm font-semibold text-neon flex items-center gap-2">
                        <i class="ph ph-barcode text-lg"></i>
                        <span>ID Member Lama / Barcode ID</span>
                        <span class="text-red-400 font-bold">*</span>
                    </label>

                    <div class="relative">
                        <input type="text" id="member_id" name="member_id" autocomplete="off" value="{{ old('member_id') }}" required
                            class="w-full border-2 border-neon/40 rounded-xl bg-dark text-white font-mono text-base md:text-lg focus:ring-neon focus:border-neon tracking-wider pl-11 pr-4 py-3 shadow-inner"
                            placeholder="Contoh: MBR-00123 / 89201923">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neon">
                            <i class="ph ph-qr-code text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 flex items-center gap-1.5 pt-1">
                        <i class="ph ph-info text-neon/70 text-sm"></i>
                        <span>Ketik atau scan barcode ID lama milik member. Harus unik.</span>
                    </p>
                </div>

                {{-- Field 2: Status Pembayaran & Aktivasi (Col 7) --}}
                <div class="lg:col-span-7 space-y-2">
                    <label class="block text-sm font-semibold text-neon flex items-center gap-2">
                        <i class="ph ph-wallet text-lg"></i>
                        <span>Status Pembayaran & Aktivasi</span>
                        <span class="text-red-400 font-bold">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        {{-- Option 1: Sudah Lunas --}}
                        <label class="relative block cursor-pointer group" for="pay_paid">
                            <input type="radio" id="pay_paid" name="payment_status" value="paid" class="peer appearance-none opacity-0 absolute w-0 h-0 pointer-events-none" {{ old('payment_status', 'paid') == 'paid' ? 'checked' : '' }}>
                            
                            <div class="rounded-xl border-2 border-gray-700 bg-dark p-4 shadow-sm transition-all duration-200
                                        peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:shadow-green-500/20 peer-checked:shadow-md
                                        group-hover:border-green-500/50 flex flex-col justify-between h-full space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-500/20 text-green-400 flex items-center justify-center shrink-0">
                                        <i class="ph ph-check-circle text-lg"></i>
                                    </div>
                                    <span class="font-bold text-sm text-green-400">Sudah Lunas (Aktif)</span>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed pl-11">
                                    Member langsung aktif untuk migrasi data lama.
                                </p>
                            </div>

                            <i class="ph-fill ph-check-circle text-green-400 text-xl
                                      opacity-0 peer-checked:opacity-100 transition-all duration-200
                                      absolute top-3.5 right-3.5 drop-shadow-[0_0_6px_rgba(34,197,94,0.8)]"></i>
                        </label>

                        {{-- Option 2: Belum Lunas --}}
                        <label class="relative block cursor-pointer group" for="pay_unpaid">
                            <input type="radio" id="pay_unpaid" name="payment_status" value="unpaid" class="peer appearance-none opacity-0 absolute w-0 h-0 pointer-events-none" {{ old('payment_status') == 'unpaid' ? 'checked' : '' }}>
                            
                            <div class="rounded-xl border-2 border-gray-700 bg-dark p-4 shadow-sm transition-all duration-200
                                        peer-checked:border-yellow-500 peer-checked:bg-yellow-500/10 peer-checked:shadow-yellow-500/20 peer-checked:shadow-md
                                        group-hover:border-yellow-500/50 flex flex-col justify-between h-full space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-500/20 text-yellow-400 flex items-center justify-center shrink-0">
                                        <i class="ph ph-clock text-lg"></i>
                                    </div>
                                    <span class="font-bold text-sm text-yellow-400">Belum Lunas (Kasir)</span>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed pl-11">
                                    Status pending, tagihan diproses di Kasir.
                                </p>
                            </div>

                            <i class="ph-fill ph-check-circle text-yellow-400 text-xl
                                      opacity-0 peer-checked:opacity-100 transition-all duration-200
                                      absolute top-3.5 right-3.5 drop-shadow-[0_0_6px_rgba(234,179,8,0.8)]"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 2: PILIH PAKET MEMBERSHIP & MASA AKTIF
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
            <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                <i class="ph ph-package text-neon"></i> Pilih Paket Membership & Masa Aktif
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($packages as $pkg)
                    <label class="relative block cursor-pointer group" for="pkg_{{ $pkg->id }}">
                        <input type="radio"
                            id="pkg_{{ $pkg->id }}"
                            name="package_id"
                            value="{{ $pkg->id }}"
                            data-name="{{ $pkg->name }}"
                            data-max-members="{{ $pkg->max_members }}"
                            data-category="{{ $pkg->category }}"
                            data-duration="{{ $pkg->duration }}"
                            data-duration-unit="{{ $pkg->duration_unit }}"
                            data-discounts="{{ json_encode($pkg->discounts->map(function($d) { return ['id' => $d->id, 'name' => $d->name, 'percentage' => $d->percentage]; })) }}"
                            class="peer sr-only"
                            required
                            {{ old('package_id') == $pkg->id ? 'checked' : '' }}>

                        <div class="rounded-xl border-2 border-gray-700 bg-dark p-4 shadow-sm transition-all duration-200
                                    peer-checked:border-neon peer-checked:bg-neon/10 peer-checked:shadow-neon/20 peer-checked:shadow-md
                                    group-hover:border-neon/50 h-full flex flex-col">

                            @if($pkg->max_members >= 2)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-pink-500/20 text-pink-400 border border-pink-500/30 rounded-full px-2 py-0.5 mb-2 w-fit">
                                    <i class="ph ph-users"></i> Couple {{ $pkg->max_members }} orang
                                </span>
                            @endif

                            <span class="block text-sm font-semibold text-white">{{ $pkg->name }}</span>
                            <span class="mt-1 flex items-center text-xs text-gray-400 uppercase tracking-wide">
                                {{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }}
                            </span>
                            <span class="mt-auto pt-3 text-base font-bold text-neon">
                                Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            </span>
                        </div>

                        <i class="ph-fill ph-check-circle text-neon text-2xl
                                  opacity-0 peer-checked:opacity-100 transition-all duration-200
                                  absolute top-3 right-3 drop-shadow-[0_0_6px_rgba(212,255,0,0.8)]"></i>
                    </label>
                @endforeach
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 border border-gray-700 bg-dark/50 rounded-xl transition-all" id="discount-container">
                    <label for="discount_id" class="block text-sm font-medium text-neon mb-2">
                        <i class="ph ph-percent mr-2"></i> Pilih Diskon (Opsional)
                    </label>
                    <select id="discount_id" name="discount_id" autocomplete="off" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        <option value="">Tidak Ada Diskon</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2" id="discount-helper">Pilih paket terlebih dahulu untuk melihat daftar diskon.</p>
                </div>
                
                <div class="p-4 border border-gray-700 bg-dark/50 rounded-xl">
                    <label for="activation_date" class="block text-sm font-medium text-neon mb-2">
                        <i class="ph ph-calendar-check mr-2"></i> Tanggal Aktif Member
                    </label>
                    <input type="date" id="activation_date" name="activation_date" autocomplete="off" class="w-full border-gray-600 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" value="{{ old('activation_date', date('Y-m-d')) }}" required>
                    <p class="text-xs text-gray-400 mt-2">Tanggal member mulai aktif latihan.</p>
                </div>

                <div class="p-4 border border-gray-700 bg-dark/50 rounded-xl">
                    <label for="expiry_date" class="block text-sm font-medium text-neon mb-2">
                        <i class="ph ph-calendar-x mr-2"></i> Tanggal Kadaluarsa (Expired)
                    </label>
                    <input type="date" id="expiry_date" name="expiry_date" autocomplete="off" class="w-full border-gray-600 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 month'))) }}" required>
                    <p class="text-xs text-gray-400 mt-2">Bisa disesuaikan manual mengikuti sisa paket dari app lama.</p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 3: DATA DIRI MEMBER PERTAMA
        ══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Webcam Member 1 --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg text-center">
                    <h3 class="text-white font-medium mb-4 text-left border-b border-gray-800 pb-2 flex items-center gap-2">
                        <i class="ph ph-camera text-neon"></i> Foto Member
                        <span id="member1-label" class="text-xs text-gray-500">(Member Pertama)</span>
                    </h3>

                    <label for="camera-select" class="sr-only">Pilih Kamera</label>
                    <select id="camera-select" class="w-full mb-4 bg-dark border-gray-700 text-white rounded-lg text-sm hidden"></select>

                    <div class="relative w-full aspect-[3/4] bg-dark rounded-lg overflow-hidden border border-gray-700 flex items-center justify-center mb-4">
                        <video id="webcam" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                        <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden" />
                        <div id="camera-placeholder" class="text-gray-500 flex flex-col items-center">
                            <i class="ph ph-camera text-4xl mb-2"></i>
                            <span class="text-sm">Kamera belum aktif</span>
                        </div>
                    </div>

                    <input type="hidden" name="photo_data" id="photo_data">

                    <div class="flex flex-wrap gap-2 justify-center">
                        <button type="button" id="start-camera" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2">
                            <i class="ph ph-video-camera"></i> <span>Mulai Kamera</span>
                        </button>
                        
                        <label class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 cursor-pointer">
                            <i class="ph ph-upload-simple"></i> <span>Upload File</span>
                            <input type="file" id="upload-photo" accept="image/*" class="hidden">
                        </label>

                        <button type="button" id="take-photo" class="flex-1 bg-neon hover:bg-[#c4e600] text-darker font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 hidden">
                            <i class="ph ph-camera"></i> <span>Ambil Foto</span>
                        </button>
                        <button type="button" id="retake-photo" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 hidden">
                            <i class="ph ph-arrows-clockwise"></i> <span>Ulangi</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Form Member 1 --}}
            <div class="lg:col-span-2">
                <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                    <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                        <i class="ph ph-user text-neon"></i> Data Diri Member
                        <span id="member1-form-label" class="text-xs text-gray-500">(Pertama)</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" name="name" autocomplete="name" value="{{ old('name') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" placeholder="Sesuai KTP">
                        </div>
                        <div class="md:col-span-2">
                            <label for="nik" class="block text-sm font-medium text-gray-300 mb-1">NIK (16 Digit)</label>
                            <input type="text" id="nik" name="nik" autocomplete="off" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono" placeholder="1234567890123456">
                        </div>
                        <div>
                            <label for="place_of_birth" class="block text-sm font-medium text-gray-300 mb-1">Tempat Lahir</label>
                            <input type="text" id="place_of_birth" name="place_of_birth" autocomplete="address-level2" value="{{ old('place_of_birth') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-300 mb-1">Tanggal Lahir</label>
                            <input type="date" id="dob" name="date_of_birth" autocomplete="bday" value="{{ old('date_of_birth') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <p class="text-xs text-gray-500 mt-1" id="age-helper">Minimal 5 tahun</p>
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-300 mb-1">Jenis Kelamin</label>
                            <select id="gender" name="gender" autocomplete="sex" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="job" class="block text-sm font-medium text-gray-300 mb-1">Pekerjaan</label>
                            <input type="text" id="job" name="job" autocomplete="organization-title" value="{{ old('job') }}"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">No. WhatsApp</label>
                            <input type="text" id="phone" name="phone" autocomplete="tel" value="{{ old('phone') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                            <input type="email" id="email" name="email" autocomplete="email" value="{{ old('email') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-300 mb-1">Alamat Domisili</label>
                            <textarea id="address" name="address" autocomplete="street-address" rows="3" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 4: MEMBER KEDUA (couple) — hidden by default
        ══════════════════════════════════════════════════════════ --}}
        <div id="couple-section" class="hidden transition-all duration-500" style="overflow: hidden; max-height: 0;">
            <div class="relative">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-pink-500/50 to-transparent"></div>
                    <span class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-pink-500/10 border border-pink-500/30 text-pink-400 text-sm font-semibold">
                        <i class="ph ph-users"></i> Data Member Ke-2 (Couple)
                    </span>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-pink-500/50 to-transparent"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Webcam Member 2 --}}
                    <div class="lg:col-span-1">
                        <div class="bg-card rounded-xl border border-pink-500/20 p-6 shadow-lg text-center">
                            <h3 class="text-white font-medium mb-4 text-left border-b border-pink-500/20 pb-2 flex items-center gap-2">
                                <i class="ph ph-camera text-pink-400"></i> Foto Member Ke-2
                            </h3>

                            <label for="camera-select-2" class="sr-only">Pilih Kamera Member 2</label>
                            <select id="camera-select-2" class="w-full mb-4 bg-dark border-gray-700 text-white rounded-lg text-sm hidden"></select>

                            <div class="relative w-full aspect-[3/4] bg-dark rounded-lg overflow-hidden border border-pink-500/30 flex items-center justify-center mb-4">
                                <video id="webcam-2" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                                <img id="photo-preview-2" class="absolute inset-0 w-full h-full object-cover hidden" />
                                <div id="camera-placeholder-2" class="text-gray-500 flex flex-col items-center">
                                    <i class="ph ph-camera text-4xl mb-2"></i>
                                    <span class="text-sm">Kamera belum aktif</span>
                                </div>
                            </div>

                            <input type="hidden" name="member2_photo_data" id="photo_data_2">

                            <div class="flex flex-wrap gap-2 justify-center">
                                <button type="button" id="start-camera-2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2">
                                    <i class="ph ph-video-camera"></i> <span>Mulai Kamera</span>
                                </button>
                                
                                <label class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 cursor-pointer">
                                    <i class="ph ph-upload-simple"></i> <span>Upload File</span>
                                    <input type="file" id="upload-photo-2" accept="image/*" class="hidden">
                                </label>

                                <button type="button" id="take-photo-2" class="flex-1 bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 hidden">
                                    <i class="ph ph-camera"></i> <span>Ambil Foto</span>
                                </button>
                                <button type="button" id="retake-photo-2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 hidden">
                                    <i class="ph ph-arrows-clockwise"></i> <span>Ulangi</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Form Member 2 --}}
                    <div class="lg:col-span-2">
                        <div class="bg-card rounded-xl border border-pink-500/20 p-6 shadow-lg">
                            <h3 class="text-white font-medium mb-4 border-b border-pink-500/20 pb-2 flex items-center gap-2">
                                <i class="ph ph-user text-pink-400"></i> Data Diri Member Ke-2
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="member2_member_id" class="block text-sm font-semibold text-pink-400 mb-1">
                                        <i class="ph ph-barcode mr-1"></i> ID Member Lama Ke-2 / Barcode ID <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" id="member2_member_id" name="member2_member_id" autocomplete="off" value="{{ old('member2_member_id') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm font-mono tracking-wider" placeholder="Contoh: MBR-00124">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="member2_name" class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                                    <input type="text" id="member2_name" name="member2_name" autocomplete="name" value="{{ old('member2_name') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm" placeholder="Sesuai KTP">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="member2_nik" class="block text-sm font-medium text-gray-300 mb-1">NIK (16 Digit)</label>
                                    <input type="text" id="member2_nik" name="member2_nik" autocomplete="off" value="{{ old('member2_nik') }}" minlength="16" maxlength="16" pattern="\d{16}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm font-mono" placeholder="1234567890123456">
                                </div>
                                <div>
                                    <label for="member2_place_of_birth" class="block text-sm font-medium text-gray-300 mb-1">Tempat Lahir</label>
                                    <input type="text" id="member2_place_of_birth" name="member2_place_of_birth" autocomplete="address-level2" value="{{ old('member2_place_of_birth') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                                </div>
                                <div>
                                    <label for="member2_dob" class="block text-sm font-medium text-gray-300 mb-1">Tanggal Lahir</label>
                                    <input type="date" id="member2_dob" name="member2_date_of_birth" autocomplete="bday" value="{{ old('member2_date_of_birth') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    <p class="text-xs text-gray-500 mt-1" id="member2-age-helper">Minimal 5 tahun</p>
                                </div>
                                <div>
                                    <label for="member2_gender" class="block text-sm font-medium text-gray-300 mb-1">Jenis Kelamin</label>
                                    <select id="member2_gender" name="member2_gender" autocomplete="sex" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                                        <option value="">Pilih...</option>
                                        <option value="L" {{ old('member2_gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('member2_gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="member2_job" class="block text-sm font-medium text-gray-300 mb-1">Pekerjaan</label>
                                    <input type="text" id="member2_job" name="member2_job" autocomplete="organization-title" value="{{ old('member2_job') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                                </div>
                                <div>
                                    <label for="member2_phone" class="block text-sm font-medium text-gray-300 mb-1">No. WhatsApp</label>
                                    <input type="text" id="member2_phone" name="member2_phone" autocomplete="tel" value="{{ old('member2_phone') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm font-mono">
                                </div>
                                <div>
                                    <label for="member2_email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                                    <input type="email" id="member2_email" name="member2_email" autocomplete="email" value="{{ old('member2_email') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="member2_address" class="block text-sm font-medium text-gray-300 mb-1">Alamat Domisili</label>
                                    <textarea id="member2_address" name="member2_address" autocomplete="street-address" rows="3"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">{{ old('member2_address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-4">
            <a href="{{ route('members.index') }}" class="bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-neon/20 flex items-center space-x-2">
                <i class="ph ph-check-circle text-xl"></i>
                <span>Simpan Member Lama</span>
            </button>
        </div>
    </form>

    <canvas id="canvas" class="hidden"></canvas>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ── Auto Calculate Expiry Date based on Package Selection & Activation Date ──
            const activationInput = document.getElementById('activation_date');
            const expiryInput = document.getElementById('expiry_date');
            const packageRadios = document.querySelectorAll('input[name="package_id"]');

            function updateExpiryFromPackage() {
                const selectedPkg = document.querySelector('input[name="package_id"]:checked');
                if (!selectedPkg || !activationInput.value) return;

                const duration = parseInt(selectedPkg.dataset.duration || 1);
                const durationUnit = selectedPkg.dataset.durationUnit || 'bulan';
                
                const actDate = new Date(activationInput.value);
                if (isNaN(actDate.getTime())) return;

                if (durationUnit === 'hari') {
                    actDate.setDate(actDate.getDate() + duration);
                } else if (durationUnit === 'bulan') {
                    actDate.setMonth(actDate.getMonth() + duration);
                } else if (durationUnit === 'tahun') {
                    actDate.setFullYear(actDate.getFullYear() + duration);
                }

                const yyyy = actDate.getFullYear();
                const mm = String(actDate.getMonth() + 1).padStart(2, '0');
                const dd = String(actDate.getDate()).padStart(2, '0');
                expiryInput.value = `${yyyy}-${mm}-${dd}`;
            }

            const discountSelect = document.getElementById('discount_id');
            const discountHelper = document.getElementById('discount-helper');

            function updateDiscounts(discountsJson) {
                discountSelect.innerHTML = '<option value="">Tidak Ada Diskon</option>';
                
                if (!discountsJson || discountsJson === "[]") {
                    discountSelect.disabled = true;
                    discountHelper.textContent = 'Tidak ada promo/diskon yang tersedia untuk paket ini.';
                    return;
                }

                try {
                    const discounts = JSON.parse(discountsJson);
                    if (discounts.length > 0) {
                        discountSelect.disabled = false;
                        discounts.forEach(d => {
                            const option = document.createElement('option');
                            option.value = d.id;
                            option.textContent = `${d.name} (${d.percentage}%)`;
                            discountSelect.appendChild(option);
                        });
                        
                        const oldVal = "{{ old('discount_id') }}";
                        if (oldVal) {
                            discountSelect.value = oldVal;
                        }
                        
                        discountHelper.textContent = 'Pilih diskon yang sesuai dengan kriteria pendaftar.';
                    } else {
                        discountSelect.disabled = true;
                        discountHelper.textContent = 'Tidak ada promo/diskon yang tersedia untuk paket ini.';
                    }
                } catch (e) {
                    discountSelect.disabled = true;
                    discountHelper.textContent = 'Tidak ada promo/diskon yang tersedia untuk paket ini.';
                }
            }

            packageRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateExpiryFromPackage();
                    updateDiscounts(this.dataset.discounts || "[]");
                });
            });
            activationInput.addEventListener('change', updateExpiryFromPackage);

            // ── Couple Section Toggle Logic ──
            const coupleSection = document.getElementById('couple-section');
            const member1Label = document.getElementById('member1-label');
            const member1FormLabel = document.getElementById('member1-form-label');
            const member2MemberId = document.getElementById('member2_member_id');
            const member2Name = document.getElementById('member2_name');
            const member2Nik = document.getElementById('member2_nik');
            const member2Pob = document.getElementById('member2_place_of_birth');
            const member2Dob = document.getElementById('member2_dob');
            const member2Gender = document.getElementById('member2_gender');
            const member2Phone = document.getElementById('member2_phone');
            const member2Email = document.getElementById('member2_email');
            const member2Address = document.getElementById('member2_address');

            function toggleCouple(show) {
                if (show) {
                    coupleSection.classList.remove('hidden');
                    requestAnimationFrame(() => {
                        coupleSection.style.maxHeight = '2000px';
                    });
                    member1Label.textContent = '(Member Pertama)';
                    member1FormLabel.textContent = '(Pertama)';
                    member2MemberId.required = true;
                    member2Name.required = true;
                    member2Nik.required = true;
                    member2Pob.required = true;
                    member2Dob.required = true;
                    member2Gender.required = true;
                    member2Phone.required = true;
                    member2Email.required = true;
                    member2Address.required = true;
                } else {
                    coupleSection.style.maxHeight = '0';
                    setTimeout(() => coupleSection.classList.add('hidden'), 500);
                    member1Label.textContent = '';
                    member1FormLabel.textContent = '';
                    member2MemberId.required = false;
                    member2Name.required = false;
                    member2Nik.required = false;
                    member2Pob.required = false;
                    member2Dob.required = false;
                    member2Gender.required = false;
                    member2Phone.required = false;
                    member2Email.required = false;
                    member2Address.required = false;
                }
            }

            packageRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const maxMembers = parseInt(this.dataset.maxMembers || 1);
                    toggleCouple(maxMembers >= 2);
                });
            });

            const initialChecked = document.querySelector('input[name="package_id"]:checked');
            if (initialChecked) {
                toggleCouple(parseInt(initialChecked.dataset.maxMembers || 1) >= 2);
                updateDiscounts(initialChecked.dataset.discounts || "[]");
            } else {
                updateDiscounts("[]");
            }

            // ── Webcam & Upload Photo Helper ──
            function initCameraSystem(config) {
                const video = document.getElementById(config.videoId);
                const preview = document.getElementById(config.previewId);
                const placeholder = document.getElementById(config.placeholderId);
                const startBtn = document.getElementById(config.startBtnId);
                const takeBtn = document.getElementById(config.takeBtnId);
                const retakeBtn = document.getElementById(config.retakeBtnId);
                const inputData = document.getElementById(config.inputDataId);
                const uploadInput = document.getElementById(config.uploadInputId);
                const cameraSelect = document.getElementById(config.cameraSelectId);
                const canvas = document.getElementById('canvas');

                let stream = null;

                async function getCameras() {
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const videoDevices = devices.filter(d => d.kind === 'videoinput');
                        if (videoDevices.length > 1) {
                            cameraSelect.innerHTML = '';
                            videoDevices.forEach((device, index) => {
                                const option = document.createElement('option');
                                option.value = device.deviceId;
                                option.text = device.label || `Kamera ${index + 1}`;
                                cameraSelect.appendChild(option);
                            });
                            cameraSelect.classList.remove('hidden');
                        }
                    } catch (e) {}
                }

                async function startCamera(deviceId = null) {
                    try {
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                        }
                        const constraints = {
                            video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'user' }
                        };
                        stream = await navigator.mediaDevices.getUserMedia(constraints);
                        video.srcObject = stream;
                        video.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                        preview.classList.add('hidden');
                        startBtn.classList.add('hidden');
                        takeBtn.classList.remove('hidden');
                        retakeBtn.classList.add('hidden');
                        await getCameras();
                    } catch (err) {
                        alert('Tidak dapat mengakses kamera: ' + err.message);
                    }
                }

                startBtn.addEventListener('click', () => startCamera());
                if (cameraSelect) {
                    cameraSelect.addEventListener('change', () => startCamera(cameraSelect.value));
                }

                takeBtn.addEventListener('click', function () {
                    canvas.width = video.videoWidth || 480;
                    canvas.height = video.videoHeight || 640;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    inputData.value = dataUrl;
                    preview.src = dataUrl;
                    preview.classList.remove('hidden');
                    video.classList.add('hidden');
                    takeBtn.classList.add('hidden');
                    retakeBtn.classList.remove('hidden');

                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                });

                retakeBtn.addEventListener('click', () => startCamera());

                if (uploadInput) {
                    uploadInput.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (event) {
                                inputData.value = event.target.result;
                                preview.src = event.target.result;
                                preview.classList.remove('hidden');
                                video.classList.add('hidden');
                                placeholder.classList.add('hidden');
                                startBtn.classList.add('hidden');
                                takeBtn.classList.add('hidden');
                                retakeBtn.classList.remove('hidden');
                                if (stream) {
                                    stream.getTracks().forEach(track => track.stop());
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }

            initCameraSystem({
                videoId: 'webcam',
                previewId: 'photo-preview',
                placeholderId: 'camera-placeholder',
                startBtnId: 'start-camera',
                takeBtnId: 'take-photo',
                retakeBtnId: 'retake-photo',
                inputDataId: 'photo_data',
                uploadInputId: 'upload-photo',
                cameraSelectId: 'camera-select'
            });

            initCameraSystem({
                videoId: 'webcam-2',
                previewId: 'photo-preview-2',
                placeholderId: 'camera-placeholder-2',
                startBtnId: 'start-camera-2',
                takeBtnId: 'take-photo-2',
                retakeBtnId: 'retake-photo-2',
                inputDataId: 'photo_data_2',
                uploadInputId: 'upload-photo-2',
                cameraSelectId: 'camera-select-2'
            });
        });
    </script>
</x-app-layout>
