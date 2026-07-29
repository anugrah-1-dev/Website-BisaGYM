<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-neon/10 flex items-center justify-center text-neon">
                    <i class="ph ph-package text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ __('Inventaris Snack') }}</h2>
                    <p class="text-xs text-gray-400">Kelola stok dan harga produk jualan</p>
                </div>
            </div>
            <a href="{{ route('snacks.create') }}" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2.5 px-5 rounded-lg transition-all hover:shadow-[0_0_15px_rgba(224,255,0,0.2)] hover:-translate-y-0.5 text-sm flex items-center">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Produk
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4 font-medium">Produk</th>
                        <th class="px-6 py-4 font-medium">Kategori</th>
                        <th class="px-6 py-4 font-medium text-center">Stok</th>
                        <th class="px-6 py-4 font-medium">Harga Modal</th>
                        <th class="px-6 py-4 font-medium">Harga Jual</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($snacks as $snack)
                        <tr class="hover:bg-dark/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-white">{{ $snack->name }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $snack->snack_code }}</p>
                            </td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-gray-800 border border-gray-700 rounded text-gray-300">{{ $snack->category }}</span></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold {{ $snack->stock <= 5 ? 'text-red-400' : 'text-white' }}">{{ $snack->stock }}</span>
                                @if($snack->stock <= 5)
                                    <span class="text-xs text-red-400 block">Stok rendah!</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400">Rp {{ number_format($snack->capital_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-neon font-bold">Rp {{ number_format($snack->selling_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('snacks.edit', $snack->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-neon hover:bg-neon/10 transition-colors" title="Edit">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </a>
                                    <form method="POST" action="{{ route('snacks.destroy', $snack->id) }}" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
