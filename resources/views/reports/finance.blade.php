<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-chart-line-up mr-2 text-neon"></i> {{ __('Laporan Keuangan') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        
        {{-- Filter Section --}}
        <div class="bg-card rounded-xl border border-gray-800 p-4 shadow-lg flex flex-col md:flex-row gap-4 items-center justify-between">
            <div>
                <h3 class="text-white font-medium">Filter Periode</h3>
                <p class="text-xs text-gray-400">Pilih bulan dan tahun laporan keuangan</p>
            </div>
            <form method="GET" action="{{ route('financial-report.index') }}" class="flex items-center gap-3">
                <select name="month" class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
                <input type="number" name="year" value="{{ $year }}" min="2020" class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm w-24">
                <button type="submit" class="px-4 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                    <i class="ph ph-funnel mr-2"></i> Filter
                </button>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Pemasukan --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-trend-up text-8xl text-green-500"></i>
                </div>
                <h3 class="text-gray-400 text-sm font-medium mb-1">Total Pemasukan</h3>
                <p class="text-3xl font-bold text-green-400 font-mono mb-4">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                <div class="space-y-1 text-xs text-gray-400">
                    <div class="flex justify-between"><span>Member Gym:</span> <span>Rp {{ number_format($memberIncome, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Kantin/Snack:</span> <span>Rp {{ number_format($snackIncome, 0, ',', '.') }}</span></div>
                </div>
            </div>

            {{-- Pengeluaran --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-trend-down text-8xl text-red-500"></i>
                </div>
                <h3 class="text-gray-400 text-sm font-medium mb-1">Total Pengeluaran</h3>
                <p class="text-3xl font-bold text-red-400 font-mono mb-4">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                <div class="space-y-1 text-xs text-gray-400">
                    <div class="flex justify-between"><span>Gaji Karyawan:</span> <span>Rp {{ number_format($payrollExpense, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Operasional:</span> <span>Rp {{ number_format($generalExpense, 0, ',', '.') }}</span></div>
                </div>
            </div>

            {{-- Laba Bersih --}}
            <div class="bg-card rounded-xl {{ $netProfit >= 0 ? 'border-neon' : 'border-red-500' }} p-6 shadow-[0_0_15px_rgba(224,255,0,0.05)] relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-coins text-8xl {{ $netProfit >= 0 ? 'text-neon' : 'text-red-500' }}"></i>
                </div>
                <h3 class="text-gray-400 text-sm font-medium mb-1">Laba Bersih (Net Profit)</h3>
                <p class="text-4xl font-bold {{ $netProfit >= 0 ? 'text-neon' : 'text-red-400' }} font-mono mb-2">
                    {{ $netProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                </p>
                <p class="text-xs {{ $netProfit >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    @if($netProfit >= 0)
                        <i class="ph ph-check-circle"></i> Keuangan surplus bulan ini.
                    @else
                        <i class="ph ph-warning-circle"></i> Keuangan defisit bulan ini.
                    @endif
                </p>
            </div>
        </div>

        {{-- Breakdown Tables --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Rincian Gaji --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                    <h3 class="text-white font-medium flex items-center"><i class="ph ph-users text-blue-400 mr-2"></i> Rincian Gaji Karyawan (Lunas)</h3>
                    <span class="text-xs bg-gray-800 text-gray-300 px-2 py-1 rounded">Bulan {{ date('F', mktime(0,0,0,$month,1)) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-dark border-b border-gray-800 text-xs text-gray-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Karyawan</th>
                                <th class="px-4 py-3 font-medium text-right">Total Dibayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($payrollsDetail as $pr)
                                <tr class="hover:bg-dark/50">
                                    <td class="px-4 py-3 text-gray-300">{{ $pr->employee->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-right text-red-400 font-mono">Rp {{ number_format($pr->total_salary, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-500 text-xs italic">Tidak ada pembayaran gaji lunas bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Rincian Operasional --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                    <h3 class="text-white font-medium flex items-center"><i class="ph ph-receipt text-orange-400 mr-2"></i> Rincian Operasional Lain-lain</h3>
                    <span class="text-xs bg-gray-800 text-gray-300 px-2 py-1 rounded">Bulan {{ date('F', mktime(0,0,0,$month,1)) }}</span>
                </div>
                <div class="overflow-x-auto max-h-[300px]">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="sticky top-0 bg-dark border-b border-gray-800 text-xs text-gray-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Tgl</th>
                                <th class="px-4 py-3 font-medium">Keterangan</th>
                                <th class="px-4 py-3 font-medium text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($expensesDetail as $exp)
                                <tr class="hover:bg-dark/50">
                                    <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">{{ \Carbon\Carbon::parse($exp->date)->format('d M') }}</td>
                                    <td class="px-4 py-3 text-gray-300">{{ $exp->description }}</td>
                                    <td class="px-4 py-3 text-right text-red-400 font-mono whitespace-nowrap">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500 text-xs italic">Tidak ada pengeluaran operasional bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
