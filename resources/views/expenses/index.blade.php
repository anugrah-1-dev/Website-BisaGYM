<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-shopping-cart-open mr-2 text-red-400"></i> {{ __('Pengeluaran Operasional') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Form Tambah Pengeluaran --}}
        <div class="lg:col-span-1">
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg sticky top-6">
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                    <i class="ph ph-plus-circle text-neon"></i> Input Pengeluaran Baru
                </h3>
                
                <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal Pengeluaran</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" required min="1000" step="1000" placeholder="Misal: 150000"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono text-red-400">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Keterangan / Deskripsi Bebas</label>
                        <textarea name="description" rows="3" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" placeholder="Contoh: Bayar listrik bulan Juli, Beli sabun pel, dll..."></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center justify-center">
                            <i class="ph ph-floppy-disk mr-2"></i> Simpan Pengeluaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Riwayat Pengeluaran --}}
        <div class="lg:col-span-2">
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
                <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                    <h3 class="text-white font-medium">Riwayat Pengeluaran</h3>
                </div>
                <div class="overflow-x-auto max-h-[600px]">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-dark border-b border-gray-800 shadow">
                            <tr class="text-xs uppercase tracking-wider text-gray-400">
                                <th class="px-6 py-4 font-medium">Tanggal</th>
                                <th class="px-6 py-4 font-medium">Keterangan</th>
                                <th class="px-6 py-4 font-medium text-right">Nominal</th>
                                <th class="px-6 py-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @forelse ($expenses as $expense)
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-6 py-4 text-white font-medium whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-300">
                                        {{ $expense->description }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-red-400 font-mono font-bold whitespace-nowrap">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" class="inline" onsubmit="return confirm('Yakin hapus catatan pengeluaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <i class="ph ph-receipt-x text-4xl mb-2 block text-gray-600"></i>
                                        Belum ada catatan pengeluaran yang diinputkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
