<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('members.show', $member->id) }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <span>Edit Data: {{ $member->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
            
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('members.update', $member->id) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- Foto Profil Member --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 space-y-4">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Foto Profil Member</label>
                        
                        <div class="relative w-full aspect-[3/4] bg-dark rounded-xl overflow-hidden border border-gray-700 flex items-center justify-center">
                            <video id="webcam" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                            
                            @if($member->photo_path)
                                <img id="photo-preview" src="{{ Storage::url($member->photo_path) }}" class="absolute inset-0 w-full h-full object-cover" />
                            @else
                                <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden" />
                            @endif
                            
                            <div id="camera-placeholder" class="text-gray-500 flex flex-col items-center {{ $member->photo_path ? 'hidden' : '' }}">
                                <i class="ph ph-camera text-4xl mb-2"></i>
                                <span class="text-sm">Kamera belum aktif</span>
                            </div>
                        </div>

                        <select id="camera-select" class="w-full bg-dark border-gray-700 text-white rounded-lg text-sm hidden"></select>
                        
                        <input type="hidden" name="photo_data" id="photo_data">

                        <div class="flex flex-wrap gap-2 justify-center">
                            <button type="button" id="start-camera" class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-xs flex items-center justify-center space-x-2">
                                <i class="ph ph-video-camera text-base"></i> <span>Mulai Kamera</span>
                            </button>
                            
                            <!-- File Upload Alternative -->
                            <label class="flex-1 bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-xs flex items-center justify-center space-x-2 cursor-pointer">
                                <i class="ph ph-upload-simple text-base"></i> <span>Upload File</span>
                                <input type="file" id="upload-photo" accept="image/*" class="hidden">
                            </label>

                            <button type="button" id="take-photo" class="flex-1 bg-neon hover:bg-[#c4e600] text-darker font-medium py-2 px-3 rounded-lg transition-colors text-xs flex items-center justify-center space-x-2 hidden">
                                <i class="ph ph-camera text-base"></i> <span>Ambil Foto</span>
                            </button>
                            <button type="button" id="retake-photo" class="w-full bg-dark hover:bg-gray-800 border border-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-xs flex items-center justify-center space-x-2 {{ $member->photo_path ? '' : 'hidden' }}">
                                <i class="ph ph-arrows-clockwise text-base"></i> <span>Ubah Foto</span>
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $member->name) }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" value="{{ old('nik', $member->nik) }}" required maxlength="16" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <option value="L" {{ old('gender', $member->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $member->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tempat Lahir</label>
                        <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $member->place_of_birth) }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal Lahir</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('Y-m-d') : '') }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">No. WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Pekerjaan</label>
                    <input type="text" name="job" value="{{ old('job', $member->job) }}" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Alamat Domisili</label>
                    <textarea name="address" rows="3" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">{{ old('address', $member->address) }}</textarea>
                </div>
                
                @role('developer')
                <div class="mt-6 pt-6 border-t border-gray-800" x-data="{
                    packages: {{ Js::from($packages) }},
                    selectedPackageId: '{{ old('locked_package_id', $member->locked_package_id) }}',
                    selectedDiscountId: '',
                    customPrice: '{{ old('locked_price', $member->locked_price) }}',
                    
                    get selectedPackage() {
                        return this.packages.find(p => p.id == this.selectedPackageId);
                    },
                    
                    get finalPrice() {
                        if (!this.selectedPackage) return '';
                        let base = this.selectedPackage.price;
                        if (this.selectedDiscountId) {
                            let discount = this.selectedPackage.discounts.find(d => d.id == this.selectedDiscountId);
                            if (discount) {
                                if (discount.discount_type === 'percentage') {
                                    base = base - (base * discount.amount / 100);
                                } else {
                                    base = base - discount.amount;
                                }
                            }
                        }
                        return Math.max(0, base);
                    },
                    
                    updatePrice() {
                        this.customPrice = this.finalPrice;
                    },

                    init() {
                        this.$watch('selectedPackageId', () => {
                            this.selectedDiscountId = '';
                            this.updatePrice();
                        });
                        this.$watch('selectedDiscountId', () => {
                            this.updatePrice();
                        });
                    }
                }">
                    <h3 class="text-lg font-medium text-neon mb-4"><i class="ph ph-warning-circle mr-2"></i>Area Khusus Developer (Koreksi Data)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Paket Gym Terkunci</label>
                            <select x-model="selectedPackageId" name="locked_package_id" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                                <option value="">-- Tidak Terkunci --</option>
                                <template x-for="pkg in packages" :key="pkg.id">
                                    <option :value="pkg.id" x-text="pkg.name + ' (Rp ' + new Intl.NumberFormat('id-ID').format(pkg.price) + ')'" :selected="pkg.id == selectedPackageId"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Diskon (Opsional)</label>
                            <select x-model="selectedDiscountId" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" :disabled="!selectedPackage || selectedPackage.discounts.length === 0">
                                <option value="">-- Tanpa Diskon --</option>
                                <template x-if="selectedPackage">
                                    <template x-for="disc in selectedPackage.discounts" :key="disc.id">
                                        <option :value="disc.id" x-text="disc.name + ' (' + (disc.discount_type === 'percentage' ? disc.amount + '%' : 'Rp ' + new Intl.NumberFormat('id-ID').format(disc.amount)) + ')'"></option>
                                    </template>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Harga Terkunci Final (Rp)</label>
                            <input type="number" name="locked_price" x-model="customPrice" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" placeholder="Otomatis atau ketik manual...">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500"><i class="ph ph-info mr-1"></i> Pilih paket dan diskon untuk menghitung harga otomatis, atau ketik langsung nominal akhirnya.</p>
                </div>
                @endrole
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-800 mt-6">
                    <a href="{{ route('members.show', $member->id) }}" class="px-5 py-2.5 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                        <i class="ph ph-floppy-disk mr-2 text-lg"></i> Simpan Perubahan
                    </button>
                </div>
                    </div> <!-- End of md:col-span-2 -->
                </div> <!-- End of grid -->
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const video       = document.getElementById('webcam');
        const preview     = document.getElementById('photo-preview');
        const placeholder = document.getElementById('camera-placeholder');
        const photoInput  = document.getElementById('photo_data');
        const startBtn    = document.getElementById('start-camera');
        const takeBtn     = document.getElementById('take-photo');
        const retakeBtn   = document.getElementById('retake-photo');
        const cameraSelect= document.getElementById('camera-select');
        const uploadInput = document.getElementById('upload-photo');
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
                        retakeBtn.querySelector('span').textContent = 'Ubah Foto';
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
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
                preview.classList.add('hidden'); // Hide existing photo if any
                startBtn.classList.add('hidden');
                takeBtn.classList.remove('hidden');
                retakeBtn.classList.add('hidden');
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

                let preferredCamera = videoInputs.find(d => {
                    const lbl = d.label.toLowerCase();
                    return lbl.includes('integrated') || lbl.includes('webcam') || lbl.includes('hd') || lbl.includes('usb') || lbl.includes('facetime');
                });
                
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
            retakeBtn.querySelector('span').textContent = 'Ubah Foto';
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
    });
    </script>
        </div>
    </div>
</x-app-layout>
