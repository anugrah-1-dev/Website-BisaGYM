<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <i class="ph ph-pencil-simple text-neon"></i>
            Edit Promo Diskon
        </div>
    </x-slot>

    <!-- Page Header (Main Content) -->
    <div class="max-w-4xl mx-auto mb-6 mt-2">
        <div class="flex items-center space-x-4 relative z-10">
            <a href="{{ route('discounts.index') }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-dark border border-gray-800 text-gray-400 hover:text-neon hover:border-neon/50 hover:bg-neon/10 transition-all duration-300 group shadow-sm">
                <i class="ph ph-arrow-left text-xl group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-2">
                    Edit Promo Diskon
                </h2>
                <p class="text-sm text-gray-400 font-medium">Ubah informasi dan cakupan paket untuk promo ini</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto relative">
        <!-- Ambient background glow -->
        <div class="absolute -inset-4 bg-neon/10 blur-3xl -z-10 rounded-[3rem]"></div>
        
        <div class="bg-card/90 backdrop-blur-xl rounded-3xl border border-gray-800/80 p-6 md:p-10 shadow-2xl relative z-10">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('discounts.update', $discount) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2 relative group">
                        <label for="name" class="block text-sm font-bold text-gray-300 mb-2 group-focus-within:text-neon transition-colors">Nama Promo Diskon <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ph ph-tag text-gray-500 group-focus-within:text-neon transition-colors text-lg"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name', $discount->name) }}" required placeholder="Contoh: Promo Pelajar / Mahasiswa" class="w-full pl-11 py-3 border-gray-700/80 rounded-xl bg-dark/50 text-white focus:ring-neon focus:border-neon focus:bg-dark transition-all duration-300 shadow-inner">
                        </div>
                    </div>

                    <div class="relative group">
                        <label for="percentage" class="block text-sm font-bold text-gray-300 mb-2 group-focus-within:text-neon transition-colors">Persentase Diskon (%) <span class="text-red-400">*</span></label>
                        <div class="relative flex items-center">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ph ph-trend-down text-gray-500 group-focus-within:text-neon transition-colors text-lg"></i>
                            </div>
                            <input type="number" name="percentage" id="percentage" value="{{ old('percentage', $discount->percentage) }}" min="1" max="100" required class="w-full pl-11 pr-12 py-3 border-gray-700/80 rounded-xl bg-dark/50 text-white focus:ring-neon focus:border-neon focus:bg-dark transition-all duration-300 shadow-inner font-mono text-lg font-bold">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-neon font-bold text-lg">%</div>
                        </div>
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $discount->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neon"></div>
                            <span class="ml-3 text-sm font-medium text-white">Status Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="pt-8 mt-2 border-t border-gray-800/80">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-dark border border-gray-700 flex items-center justify-center">
                            <i class="ph ph-package text-neon text-xl"></i>
                        </div>
                        <div>
                            <label class="block text-base font-bold text-white">Berlaku Untuk Paket Gym Berikut <span class="text-red-400">*</span></label>
                            <p class="text-xs text-gray-400">Centang paket gym mana saja yang diperbolehkan menggunakan promo ini saat pendaftaran.</p>
                        </div>
                    </div>
                    
                    @php
                        $selectedPackages = $discount->gymPackages->pluck('id')->toArray();
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        @foreach($packages as $pkg)
                            <label class="relative flex items-start p-5 cursor-pointer rounded-2xl border-2 border-gray-800/60 bg-dark/30 hover:bg-dark/80 hover:border-neon/50 transition-all duration-300 group has-[:checked]:border-neon has-[:checked]:bg-neon/5 has-[:checked]:shadow-[0_0_15px_rgba(212,255,0,0.1)]">
                                <div class="flex items-center h-6 mt-0.5">
                                    <input type="checkbox" name="gym_packages[]" value="{{ $pkg->id }}" class="w-5 h-5 bg-dark border-gray-600 rounded text-neon focus:ring-neon focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark transition-all" {{ in_array($pkg->id, old('gym_packages', $selectedPackages)) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-4 text-sm flex-1">
                                    <span class="font-bold text-white group-hover:text-neon group-has-[:checked]:text-neon transition-colors block text-base">{{ $pkg->name }}</span>
                                    <div class="flex flex-col gap-1 mt-1.5">
                                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-400 bg-gray-800/50 px-2 py-1 rounded-md w-fit">
                                            <i class="ph ph-clock"></i> {{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }}
                                        </span>
                                        <span class="font-mono text-neon/80 text-sm font-semibold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <i class="ph-fill ph-check-circle text-neon absolute top-4 right-4 text-xl opacity-0 group-has-[:checked]:opacity-100 transition-opacity drop-shadow-[0_0_8px_rgba(212,255,0,0.5)]"></i>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-8 flex flex-col-reverse sm:flex-row justify-end gap-4 border-t border-gray-800/80 mt-8">
                    <a href="{{ route('discounts.index') }}" class="px-6 py-3 border border-gray-700 rounded-xl text-gray-300 hover:bg-gray-800 hover:text-white transition-all text-sm font-bold text-center flex items-center justify-center gap-2">
                        <i class="ph ph-x"></i> Batal
                    </a>
                    <button type="submit" class="group relative px-8 py-3 bg-neon hover:bg-[#c4e600] text-darker rounded-xl font-black transition-all duration-300 text-sm shadow-[0_0_20px_rgba(212,255,0,0.2)] hover:shadow-[0_0_30px_rgba(212,255,0,0.4)] hover:-translate-y-1 overflow-hidden flex items-center justify-center gap-2">
                        <div class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1s_forwards]"></div>
                        <i class="ph ph-check-circle text-xl"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
