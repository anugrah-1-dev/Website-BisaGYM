<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <span>{{ __('Tambah Karyawan Baru') }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
            @csrf

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Data Profil --}}
                <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                    <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                        <i class="ph ph-user text-neon"></i> Data Profil Karyawan
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">No. WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Posisi / Jabatan</label>
                            <input type="text" name="position" value="{{ old('position', 'Penjaga / Kasir') }}" required
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Gaji Pokok (Rp)</label>
                                <input type="number" name="base_salary" value="{{ old('base_salary', 0) }}" required min="0" step="1000"
                                    class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Tgl Masuk (Opsional)</label>
                                <input type="date" name="join_date" value="{{ old('join_date') }}"
                                    class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Akun Login --}}
                <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                    <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                        <i class="ph ph-key text-blue-400"></i> Pembuatan Akun Akses
                    </h3>
                    
                    <p class="text-xs text-gray-400 mb-4">
                        Jika dicentang, sistem akan membuatkan akun untuk karyawan ini agar dapat login sebagai <strong>Kasir</strong>.
                    </p>

                    <label class="flex items-center space-x-3 mb-6 cursor-pointer group">
                        <input type="checkbox" name="create_login" id="create_login_cb" value="1" {{ old('create_login') ? 'checked' : '' }}
                            class="rounded border-gray-700 bg-dark text-neon focus:ring-neon w-5 h-5 transition-colors">
                        <span class="text-sm font-medium text-gray-300 group-hover:text-white transition-colors">Buatkan akun login aplikasi</span>
                    </label>

                    <div id="login_fields" class="space-y-4 {{ old('create_login') ? '' : 'hidden' }} p-4 border border-blue-500/30 bg-blue-500/5 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Email Karyawan</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Gunakan format email bebas (contoh: agus@gym.id)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                            <input type="text" name="password" id="password" value="{{ old('password') }}"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                    <i class="ph ph-floppy-disk mr-2"></i> Simpan Data Karyawan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cb = document.getElementById('create_login_cb');
            const fields = document.getElementById('login_fields');
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');

            cb.addEventListener('change', function() {
                if(this.checked) {
                    fields.classList.remove('hidden');
                    emailInput.required = true;
                    passInput.required = true;
                } else {
                    fields.classList.add('hidden');
                    emailInput.required = false;
                    passInput.required = false;
                }
            });
        });
    </script>
</x-app-layout>
