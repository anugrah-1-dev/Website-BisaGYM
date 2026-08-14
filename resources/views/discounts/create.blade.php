<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('discounts.index') }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                Tambah Promo Diskon Baru
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-card rounded-2xl border border-gray-800 p-6 md:p-8 shadow-xl">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('discounts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nama Promo Diskon <span class="text-red-400">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Promo Pelajar / Mahasiswa" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        <p class="mt-1 text-xs text-gray-500">Akan ditampilkan pada dropdown pendaftaran member.</p>
                    </div>

                    <div>
                        <label for="percentage" class="block text-sm font-medium text-gray-300 mb-1">Persentase Diskon (%) <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="number" name="percentage" id="percentage" value="{{ old('percentage') }}" min="1" max="100" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm pr-10">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-bold">%</div>
                        </div>
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neon"></div>
                            <span class="ml-3 text-sm font-medium text-white">Status Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-800">
                    <label class="block text-sm font-medium text-neon mb-3 flex items-center gap-2">
                        <i class="ph ph-package"></i> Berlaku Untuk Paket Gym Berikut <span class="text-red-400">*</span>
                    </label>
                    <p class="text-xs text-gray-400 mb-4">Centang paket gym mana saja yang diperbolehkan menggunakan promo ini saat pendaftaran.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($packages as $pkg)
                            <label class="relative flex items-start p-4 cursor-pointer rounded-xl border border-gray-700 bg-dark hover:border-neon/50 transition-colors group">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="gym_packages[]" value="{{ $pkg->id }}" class="w-5 h-5 bg-dark border-gray-600 rounded text-neon focus:ring-neon focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark" {{ in_array($pkg->id, old('gym_packages', [])) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm flex-1">
                                    <span class="font-medium text-white group-hover:text-neon transition-colors block">{{ $pkg->name }}</span>
                                    <span class="text-xs text-gray-400 block mt-0.5">{{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }} • Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-gray-800">
                    <a href="{{ route('discounts.index') }}" class="px-5 py-2.5 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center gap-2">
                        <i class="ph ph-check-circle text-lg"></i>
                        Simpan Diskon
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
