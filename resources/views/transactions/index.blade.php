<x-app-layout>
    <x-slot name="header">
        {{ __('Laporan Transaksi') }}
    </x-slot>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('transactions.index') }}" class="bg-card rounded-xl border border-gray-800 p-4 shadow-lg mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-400 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
        </div>
        <button type="submit" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2 px-4 rounded-lg transition-colors text-sm">
            <i class="ph ph-funnel"></i> Filter
        </button>
        <a href="{{ route('transactions.index') }}" class="border border-gray-700 text-gray-300 hover:bg-gray-800 py-2 px-4 rounded-lg text-sm transition-colors">Reset</a>
        
        <a href="{{ route('transactions.export', ['type' => $type, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-lg transition-colors text-sm ml-auto flex items-center shadow-[0_0_15px_rgba(22,163,74,0.2)]">
            <i class="ph ph-file-xls text-lg mr-2"></i> Export Excel
        </a>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-card rounded-xl border border-gray-800 p-5 shadow-lg">
            <p class="text-gray-400 text-sm">Pendapatan Member</p>
            <p class="text-2xl font-bold text-neon mt-1">Rp {{ number_format($memberTotal, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $memberTransactions->count() }} transaksi</p>
        </div>
        <div class="bg-card rounded-xl border border-gray-800 p-5 shadow-lg">
            <p class="text-gray-400 text-sm">Pendapatan Snack/POS</p>
            <p class="text-2xl font-bold text-neon mt-1">Rp {{ number_format($snackTotal, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $snackTransactions->count() }} transaksi</p>
        </div>
        <div class="bg-card rounded-xl border border-green-800 border-2 p-5 shadow-lg">
            <p class="text-gray-400 text-sm">Total Pemasukan</p>
            <p class="text-2xl font-bold text-green-400 mt-1">Rp {{ number_format($memberTotal + $snackTotal, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Periode yang dipilih</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex space-x-1 mb-4 bg-dark rounded-lg p-1 w-fit border border-gray-800">
        <a href="?type=member&date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $type === 'member' ? 'bg-neon text-darker' : 'text-gray-400 hover:text-white' }}">
            <i class="ph ph-users mr-1"></i> Transaksi Member
        </a>
        <a href="?type=snack&date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $type === 'snack' ? 'bg-neon text-darker' : 'text-gray-400 hover:text-white' }}">
            <i class="ph ph-storefront mr-1"></i> Transaksi Snack
        </a>
    </div>

    @if($type === 'member')
    <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4 font-medium">Kode Transaksi</th>
                        <th class="px-6 py-4 font-medium">Member</th>
                        <th class="px-6 py-4 font-medium">Paket</th>
                        <th class="px-6 py-4 font-medium">Tipe</th>
                        <th class="px-6 py-4 font-medium">Metode</th>
                        <th class="px-6 py-4 font-medium">Petugas</th>
                        <th class="px-6 py-4 font-medium text-right">Nominal</th>
                        <th class="px-6 py-4 font-medium">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($memberTransactions as $trx)
                        <tr class="hover:bg-dark/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $trx->transaction_code }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('members.show', $trx->member_id) }}" class="text-white hover:text-neon transition-colors font-medium">{{ $trx->member->name ?? '-' }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ $trx->package->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($trx->transaction_type === 'new')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">Baru</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Renewal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->payment_status === 'unpaid')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30"><i class="ph ph-warning-circle mr-1"></i>Belum Bayar</span>
                                @elseif($trx->payment_method === 'transfer')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30"><i class="ph ph-bank mr-1"></i>Transfer</span>
                                @elseif($trx->payment_method === 'gratis')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30"><i class="ph ph-gift mr-1"></i>Gratis (Mr. Abie)</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"><i class="ph ph-money mr-1"></i>Cash</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ $trx->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-neon font-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">Tidak ada data transaksi member pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4 font-medium">Kode Transaksi</th>
                        <th class="px-6 py-4 font-medium">Detail Barang</th>
                        <th class="px-6 py-4 font-medium">Metode</th>
                        <th class="px-6 py-4 font-medium">Petugas</th>
                        <th class="px-6 py-4 font-medium text-right">Total</th>
                        <th class="px-6 py-4 font-medium">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($snackTransactions as $trx)
                        <tr class="hover:bg-dark/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $trx->transaction_code }}</td>
                            <td class="px-6 py-4">
                                @foreach($trx->details as $detail)
                                    <p class="text-gray-300 text-xs">{{ $detail->snack->name ?? '?' }} x{{ $detail->quantity }} = <span class="text-neon">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span></p>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                @if(($trx->payment_method ?? 'cash') === 'transfer')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30"><i class="ph ph-bank mr-1"></i>Non-Tunai</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"><i class="ph ph-money mr-1"></i>Tunai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ $trx->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-neon font-bold">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data transaksi snack pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</x-app-layout>
