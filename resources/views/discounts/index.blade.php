<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-10">
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-3 tracking-tight">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-neon to-[#c4e600] flex items-center justify-center text-darker shadow-lg shadow-neon/20">
                        <i class="ph ph-percent text-2xl"></i>
                    </div>
                    {{ __('Manajemen Diskon / Promo') }}
                </h2>
                <p class="text-sm text-gray-400 mt-2 font-medium ml-13">Atur diskon yang berlaku untuk paket membership Anda.</p>
            </div>
            <div class="mt-6 sm:mt-0">
                <a href="{{ route('discounts.create') }}" class="group relative inline-flex items-center gap-2 px-6 py-3 bg-neon hover:bg-[#c4e600] text-darker font-bold rounded-xl transition-all duration-300 hover:scale-[1.02] active:scale-95 shadow-[0_0_20px_rgba(212,255,0,0.3)] hover:shadow-[0_0_30px_rgba(212,255,0,0.5)] overflow-hidden">
                    <div class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1s_forwards]"></div>
                    <i class="ph ph-plus-circle text-xl transition-transform group-hover:rotate-90 duration-300"></i>
                    <span>Tambah Diskon Baru</span>
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

    <div class="relative mt-4">
        <!-- Glow effect behind the table -->
        <div class="absolute inset-0 bg-neon/5 blur-3xl -z-10 rounded-3xl"></div>
        
        <div class="bg-card/80 backdrop-blur-xl rounded-2xl border border-gray-800/60 overflow-hidden shadow-2xl relative z-10">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="text-xs uppercase bg-dark/50 border-b border-gray-800/80 text-gray-300">
                        <tr>
                            <th class="px-8 py-5 font-semibold tracking-wider">Nama Promo</th>
                            <th class="px-6 py-5 font-semibold tracking-wider">Potongan Diskon</th>
                            <th class="px-6 py-5 font-semibold tracking-wider">Berlaku Untuk Paket</th>
                            <th class="px-6 py-5 font-semibold tracking-wider">Status</th>
                            <th class="px-8 py-5 font-semibold tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($discounts as $discount)
                            <tr class="hover:bg-gray-800/30 transition-all duration-300 group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-dark flex items-center justify-center border border-gray-700 group-hover:border-neon/50 transition-colors">
                                            <i class="ph ph-tag text-neon"></i>
                                        </div>
                                        <span class="font-bold text-white text-base group-hover:text-neon transition-colors">{{ $discount->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neon/10 border border-neon/20 text-neon font-black text-lg shadow-[0_0_10px_rgba(212,255,0,0.1)]">
                                        <i class="ph ph-trend-down text-sm"></i>
                                        {{ $discount->percentage }}%
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($discount->gymPackages->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($discount->gymPackages as $pkg)
                                                <span class="inline-flex items-center gap-1 text-xs bg-dark text-gray-300 px-3 py-1 rounded-full border border-gray-700 shadow-inner group-hover:border-gray-600 transition-colors">
                                                    <i class="ph-fill ph-package text-gray-500"></i>
                                                    {{ $pkg->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs bg-red-500/10 text-red-400 rounded-full border border-red-500/20">
                                            <i class="ph ph-warning-circle"></i> Tidak ada paket (Belum aktif)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if($discount->is_active)
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-green-500/10 text-green-400 border border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]">
                                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('discounts.edit', $discount) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-dark text-gray-400 hover:text-neon hover:bg-neon/10 border border-gray-700 hover:border-neon/30 transition-all duration-300 hover:scale-110" title="Edit Promo">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </a>
                                        <form action="{{ route('discounts.destroy', $discount) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus promo diskon ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-dark text-gray-400 hover:text-red-400 hover:bg-red-400/10 border border-gray-700 hover:border-red-400/30 transition-all duration-300 hover:scale-110" title="Hapus Promo">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-dark rounded-full flex items-center justify-center border border-gray-800 mb-4 shadow-inner">
                                            <i class="ph ph-ticket text-4xl text-gray-600"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-white mb-1">Belum Ada Promo Diskon</h3>
                                        <p class="text-sm text-gray-400 max-w-sm">Anda belum menambahkan satupun promo diskon. Klik tombol "Tambah Diskon Baru" untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
