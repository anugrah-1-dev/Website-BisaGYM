<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('snacks.index') }}" class="text-gray-400 hover:text-neon transition-colors"><i class="ph ph-arrow-left text-xl"></i></a>
            <span>{{ isset($snack) ? 'Edit Produk: ' . $snack->name : 'Tambah Produk Baru' }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ isset($snack) ? route('snacks.update', $snack->id) : route('snacks.store') }}" class="space-y-4">
                @csrf
                @if(isset($snack)) @method('PUT') @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name', $snack->name ?? '') }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Kode Produk</label>
                        <input type="text" name="snack_code" value="{{ old('snack_code', $snack->snack_code ?? '') }}" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono" placeholder="SNK-001">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Kategori</label>
                        <select name="category" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <option value="Minuman" {{ old('category', $snack->category ?? '') === 'Minuman' ? 'selected' : '' }}>Minuman</option>
                            <option value="Makanan" {{ old('category', $snack->category ?? '') === 'Makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="Suplemen" {{ old('category', $snack->category ?? '') === 'Suplemen' ? 'selected' : '' }}>Suplemen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Harga Modal (Rp)</label>
                        <input type="number" name="capital_price" value="{{ old('capital_price', $snack->capital_price ?? '') }}" required min="0" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', $snack->selling_price ?? '') }}" required min="0" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Jumlah Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', $snack->stock ?? 0) }}" required min="0" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-2">
                    <a href="{{ route('snacks.index') }}" class="px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                        <i class="ph ph-floppy-disk mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
