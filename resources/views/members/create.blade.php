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
            <span>{{ __('Registrasi Member Baru') }}</span>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('members.store') }}" id="memberForm" class="space-y-6">
        @csrf

        {{-- ══════════════════════════════════════════════════════════
             SECTION: PILIH PAKET (pindah ke atas agar JS bisa deteksi)
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
            <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                <i class="ph ph-package text-neon"></i> Pilih Paket Membership
            </h3>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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

                        {{-- Card --}}
                        <div class="rounded-xl border-2 border-gray-700 bg-dark p-4 shadow-sm transition-all duration-200
                                    peer-checked:border-neon peer-checked:bg-neon/10 peer-checked:shadow-neon/20 peer-checked:shadow-md
                                    group-hover:border-neon/50 h-full flex flex-col">

                            {{-- Badge couple --}}
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

                        {{-- Check icon — ditempatkan di LUAR div card tapi masih di dalam label,
                             sehingga peer-checked bisa menjangkaunya --}}
                        <i class="ph-fill ph-check-circle text-neon text-2xl
                                  opacity-0 peer-checked:opacity-100 transition-all duration-200
                                  absolute top-3 right-3 drop-shadow-[0_0_6px_rgba(212,255,0,0.8)]"></i>
                    </label>
                @endforeach
            </div>
            
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 border border-gray-700 bg-dark/50 rounded-xl">
                    <label for="discount_category" class="block text-sm font-medium text-neon mb-2">
                        <i class="ph ph-percent mr-2"></i> Diskon Profesi
                    </label>
                    <select id="discount_category" name="discount_category" autocomplete="off" class="w-full border-gray-600 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        <option value="0">Tidak Ada Diskon (0%)</option>
                        <option value="10">Pelajar, PNS, TNI, dan POLRI (10%)</option>
                        <option value="15">Pelaku Budaya, Dukun, dan Ulama (15%)</option>
                        <option value="20">Guru / Tenaga Pendidik (20%)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Diskon hanya untuk pendaftaran baru (tanpa admin).</p>
                </div>
                
                <div class="p-4 border border-gray-700 bg-dark/50 rounded-xl">
                    <label for="activation_date" class="block text-sm font-medium text-neon mb-2">
                        <i class="ph ph-calendar-check mr-2"></i> Tanggal Aktif Member
                    </label>
                    <input type="date" id="activation_date" name="activation_date" autocomplete="off" class="w-full border-gray-600 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                    <p class="text-xs text-gray-500 mt-2">Tentukan kapan member mulai aktif latihan (bisa pilih hari ini atau tanggal kedepannya).</p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION: MEMBER PERTAMA
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
                        
                        <!-- File Upload Alternative -->
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
                        <i class="ph ph-user text-neon"></i>
                        Data Diri Member
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
             SECTION: MEMBER KEDUA (couple) — hidden by default
        ══════════════════════════════════════════════════════════ --}}
        <div id="couple-section"
             class="hidden transition-all duration-500"
             style="overflow: hidden; max-height: 0;">
            <div class="relative">
                {{-- Divider --}}
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

                    <label for="camera-select2" class="sr-only">Pilih Kamera Member 2</label>
                    <select id="camera-select2" class="w-full mb-4 bg-dark border-gray-700 text-white rounded-lg text-sm hidden"></select>

                            <div class="relative w-full aspect-[3/4] bg-dark rounded-lg overflow-hidden border border-gray-700 flex items-center justify-center mb-4">
                                <video id="webcam2" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                                <img id="photo-preview2" class="absolute inset-0 w-full h-full object-cover hidden" />
                                <div id="camera-placeholder2" class="text-gray-500 flex flex-col items-center">
                                    <i class="ph ph-camera text-4xl mb-2"></i>
                                    <span class="text-sm">Kamera belum aktif</span>
                                </div>
                            </div>

                            <input type="hidden" name="member2_photo_data" id="photo_data2">

                            <div class="flex flex-wrap gap-2 justify-center">
                                <button type="button" id="start-camera2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2">
                                    <i class="ph ph-video-camera"></i> <span>Mulai Kamera</span>
                                </button>
                                
                                <!-- File Upload Alternative -->
                                <label class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 cursor-pointer">
                                    <i class="ph ph-upload-simple"></i> <span>Upload File</span>
                                    <input type="file" id="upload-photo2" accept="image/*" class="hidden">
                                </label>

                                <button type="button" id="take-photo2" class="flex-1 bg-pink-500 hover:bg-pink-400 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 hidden">
                                    <i class="ph ph-camera"></i> <span>Ambil Foto</span>
                                </button>
                                <button type="button" id="retake-photo2" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center space-x-2 hidden">
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
                                    <label for="member2_date_of_birth" class="block text-sm font-medium text-gray-300 mb-1">Tanggal Lahir</label>
                                    <input type="date" id="dob2" name="member2_date_of_birth" autocomplete="bday" value="{{ old('member2_date_of_birth') }}"
                                        class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    <p class="text-xs text-gray-500 mt-1" id="age-helper2">Minimal 5 tahun</p>
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
        <div class="flex justify-end space-x-3">
            <a href="{{ route('members.index') }}" class="px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
            <button type="submit" id="submit-btn" class="px-6 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                <i class="ph ph-check-circle mr-2 text-lg"></i>
                <span id="submit-label">Simpan & Generate VIP ID</span>
            </button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ══════════════════════════════
        // 1. PAKET SELECTION LOGIC
        // ══════════════════════════════
        const coupleSection  = document.getElementById('couple-section');
        const submitLabel    = document.getElementById('submit-label');
        const radioInputs    = document.querySelectorAll('input[name="package_id"]');

        function toggleCoupleSection(maxMembers) {
            const isCouple = parseInt(maxMembers) >= 2;

            if (isCouple) {
                coupleSection.classList.remove('hidden');
                // Animate open
                coupleSection.style.maxHeight = coupleSection.scrollHeight + 5000 + 'px';
                // Tandai field member2 sebagai required
                setMember2Required(true);
                submitLabel.textContent = 'Simpan & Generate 2 VIP ID (Couple)';
            } else {
                coupleSection.style.maxHeight = '0';
                setTimeout(() => coupleSection.classList.add('hidden'), 400);
                setMember2Required(false);
                submitLabel.textContent = 'Simpan & Generate VIP ID';
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

        // Attach event to all radio inputs
        radioInputs.forEach(radio => {
            radio.addEventListener('change', function () {
                toggleCoupleSection(this.dataset.maxMembers || 1);
            });
        });

        // Restore state jika old() ada (setelah validation error)
        const checkedRadio = document.querySelector('input[name="package_id"]:checked');
        if (checkedRadio) {
            toggleCoupleSection(checkedRadio.dataset.maxMembers || 1);
        }

        // ══════════════════════════════
        // 2. WEBCAM — MEMBER 1
        // ══════════════════════════════
        setupWebcam({
            videoId: 'webcam',
            previewId: 'photo-preview',
            placeholderId: 'camera-placeholder',
            inputId: 'photo_data',
            startBtnId: 'start-camera',
            takeBtnId: 'take-photo',
            retakeBtnId: 'retake-photo',
            selectId: 'camera-select',
            uploadInputId: 'upload-photo'
        });

        // ══════════════════════════════
        // 3. WEBCAM — MEMBER 2
        // ══════════════════════════════
        setupWebcam({
            videoId: 'webcam2',
            previewId: 'photo-preview2',
            placeholderId: 'camera-placeholder2',
            inputId: 'photo_data2',
            startBtnId: 'start-camera2',
            takeBtnId: 'take-photo2',
            retakeBtnId: 'retake-photo2',
            selectId: 'camera-select2',
            uploadInputId: 'upload-photo2'
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

            async function getCameras() {
                try {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    
                    if (videoDevices.length > 0 && cameraSelect) {
                        cameraSelect.innerHTML = '';
                        videoDevices.forEach((device, index) => {
                            const option = document.createElement('option');
                            option.value = device.deviceId;
                            option.text = device.label || `Camera ${index + 1}`;
                            cameraSelect.appendChild(option);
                        });
                        cameraSelect.classList.remove('hidden');
                        return true;
                    }
                } catch (e) {
                    console.error('Error enumerating devices', e);
                }
                return false;
            }

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
                    // Cek apakah label kamera sudah tersedia (izin sudah diberikan sebelumnya)
                    let devices = await navigator.mediaDevices.enumerateDevices();
                    let videoInputs = devices.filter(d => d.kind === 'videoinput');
                    let hasLabels = videoInputs.some(d => d.label !== '');

                    if (!hasLabels) {
                        // Jika belum ada label, kita harus minta izin dulu (ini mungkin memicu popup Link to Windows sesaat di awal saja)
                        const initStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        initStream.getTracks().forEach(t => t.stop());
                        devices = await navigator.mediaDevices.enumerateDevices();
                        videoInputs = devices.filter(d => d.kind === 'videoinput');
                    }

                    // Cari kamera laptop bawaan (bukan virtual camera/HP)
                    let preferredCamera = videoInputs.find(d => {
                        const lbl = d.label.toLowerCase();
                        return lbl.includes('integrated') || lbl.includes('webcam') || lbl.includes('hd') || lbl.includes('usb') || lbl.includes('facetime');
                    });
                    
                    // Hindari virtual camera HP (seperti Redmi / Windows Virtual Camera) jika ada pilihan lain
                    if (!preferredCamera && videoInputs.length > 1) {
                        preferredCamera = videoInputs.find(d => {
                            const lbl = d.label.toLowerCase();
                            return !lbl.includes('windows') && !lbl.includes('redmi') && !lbl.includes('phone') && !lbl.includes('obs') && !lbl.includes('virtual');
                        });
                    }

                    if (cameraSelect && videoInputs.length > 0) {
                        cameraSelect.innerHTML = '';
                        videoInputs.forEach((device, index) => {
                            const option = document.createElement('option');
                            option.value = device.deviceId;
                            option.text = device.label || `Camera ${index + 1}`;
                            cameraSelect.appendChild(option);
                        });
                        cameraSelect.classList.remove('hidden');
                        
                        if (preferredCamera) {
                            cameraSelect.value = preferredCamera.deviceId;
                            startCamera(preferredCamera.deviceId);
                        } else {
                            startCamera(videoInputs[0].deviceId);
                        }
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

        // ══════════════════════════════
        // 4. AGE VALIDATION HELPERS
        // ══════════════════════════════
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
                    ageHelper.textContent = `Umur: ${age} tahun ⚠ Tidak memenuhi syarat minimal 5 tahun!`;
                    ageHelper.className = 'text-xs text-red-400 mt-1';
                } else {
                    ageHelper.textContent = `Umur: ${age} tahun ✓ Memenuhi syarat`;
                    ageHelper.className = 'text-xs text-green-400 mt-1';
                }
            });
        }

        setupAgeHelper('dob', 'age-helper');
        setupAgeHelper('dob2', 'age-helper2');
    });
    </script>
</x-app-layout>
