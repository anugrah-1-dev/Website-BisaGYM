<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ph ph-percent text-neon"></i>
                {{ __('Manajemen Diskon / Promo') }}
            </h2>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('discounts.create') }}" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors text-sm shadow-[0_0_10px_rgba(212,255,0,0.2)]">
                    <i class="ph ph-plus-circle text-lg"></i>
                    Tambah Diskon Baru
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-neon/10 border border-neon/30 text-neon flex items-start gap-3">
            <i class="ph ph-check-circle text-xl mt-0.5"></i>
            <div>
                <h4 class="font-bold">Berhasil!</h4>
                <p class="text-sm opacity-90">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-card rounded-2xl border border-gray-800 overflow-hidden shadow-lg">
        <div class="p-6 border-b border-gray-800">
            <h3 class="text-lg font-medium text-white">Daftar Promo Diskon</h3>
            <p class="text-sm text-gray-400 mt-1">Kelola jenis diskon yang bisa diterapkan pada paket gym tertentu saat pendaftaran member.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="text-xs uppercase bg-dark border-b border-gray-800 text-gray-300">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nama Promo</th>
                        <th class="px-6 py-4 font-medium">Potongan Diskon</th>
                        <th class="px-6 py-4 font-medium">Berlaku Untuk Paket</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($discounts as $discount)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-white">{{ $discount->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-neon font-bold">
                                {{ $discount->percentage }}%
                            </td>
                            <td class="px-6 py-4">
                                @if($discount->gymPackages->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($discount->gymPackages as $pkg)
                                            <span class="text-[10px] bg-gray-700 text-gray-300 px-2 py-0.5 rounded-full border border-gray-600">
                                                {{ $pkg->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-red-400 italic">Tidak ada paket (Belum aktif)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($discount->is_active)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('discounts.edit', $discount) }}" class="text-gray-400 hover:text-neon transition-colors" title="Edit">
                                        <i class="ph ph-pencil-simple text-xl"></i>
                                    </a>
                                    <form action="{{ route('discounts.destroy', $discount) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus promo diskon ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors" title="Hapus">
                                            <i class="ph ph-trash text-xl"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="ph ph-ticket text-4xl mb-3 block opacity-50"></i>
                                <p>Belum ada data promo diskon.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
