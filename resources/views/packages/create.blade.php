<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('gym-packages.index') }}" class="text-gray-400 hover:text-neon transition-colors"><i class="ph ph-arrow-left text-xl"></i></a>
            <span>{{ __('Tambah Paket Baru') }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('gym-packages.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nama Paket</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ old('price') }}" required min="0" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Biaya Admin (Rp)</label>
                        <input type="number" name="admin_fee" value="{{ old('admin_fee', 0) }}" required min="0" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Kategori</label>
                        <select name="category" id="category" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <option value="member" {{ old('category') == 'member' ? 'selected' : '' }}>Member Individu</option>
                            <option value="couple" {{ old('category') == 'couple' ? 'selected' : '' }}>Paket Couple</option>
                            <option value="non-member" {{ old('category') == 'non-member' ? 'selected' : '' }}>Non-Member</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Jumlah Member</label>
                        <input type="number" name="max_members" id="max_members" value="{{ old('max_members', 1) }}" required min="1" max="10"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        <p class="text-xs text-gray-500 mt-1">1 = individu, 2 = couple, dst.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Durasi</label>
                        <input type="number" name="duration" value="{{ old('duration', 1) }}" required min="1" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Satuan</label>
                        <select name="duration_unit" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <option value="hari" {{ old('duration_unit') == 'hari' ? 'selected' : '' }}>Hari</option>
                            <option value="bulan" {{ old('duration_unit') == 'bulan' ? 'selected' : '' }}>Bulan</option>
                            <option value="tahun" {{ old('duration_unit') == 'tahun' ? 'selected' : '' }}>Tahun</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="h-5 w-5 text-neon bg-dark border-gray-700 rounded focus:ring-neon">
                            <span class="text-sm font-medium text-gray-300">Status Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <a href="{{ route('gym-packages.index') }}" class="px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                        <i class="ph ph-plus-circle mr-2"></i> Tambah Paket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
