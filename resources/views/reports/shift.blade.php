<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ph ph-clock-afternoon text-neon text-2xl"></i>
                <span class="font-bold text-lg text-white">{{ __('Laporan Keuangan Shift Kasir') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('financial-report.index') }}" class="px-3 py-1.5 bg-dark border border-gray-700 hover:border-gray-500 rounded-lg text-xs font-medium text-gray-300 transition-colors flex items-center gap-1.5">
                    <i class="ph ph-chart-line text-sm"></i> Laporan Keuangan Bulanan
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filter Tanggal -->
    <div class="bg-card rounded-xl border border-gray-800 shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('shift-reports.index') }}" class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="flex-1 max-w-sm">
                <label class="block text-sm font-medium text-gray-400 mb-2">Pilih Tanggal Laporan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="ph ph-calendar text-lg"></i>
                    </div>
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="block w-full pl-10 pr-4 py-2.5 bg-dark border border-gray-700 rounded-lg text-white font-mono focus:ring-neon focus:border-neon transition-colors">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('shift-reports.index', ['date' => now()->format('Y-m-d')]) }}" class="px-4 py-2.5 bg-dark border border-gray-700 hover:border-neon text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                    <i class="ph ph-clock text-base text-neon"></i> Hari Ini
                </a>
                <a href="{{ route('shift-reports.index', ['date' => now()->subDay()->format('Y-m-d')]) }}" class="px-4 py-2.5 bg-dark border border-gray-700 hover:border-gray-500 text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    Kemarin
                </a>
                <button type="submit" class="px-5 py-2.5 bg-neon hover:bg-[#c4e600] text-darker font-bold text-sm rounded-lg transition-colors flex items-center gap-2">
                    <i class="ph ph-magnifying-glass text-base"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Header Tanggal Terpilih -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <span>Rekapitulasi Shift Tanggal:</span>
            <span class="font-mono text-neon bg-neon/10 px-3 py-1 rounded-lg border border-neon/20">
                {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
            </span>
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ==================== SHIFT PAGI ==================== -->
        <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col justify-between">
            <div>
                <!-- Shift Header -->
                <div class="bg-gradient-to-r from-amber-500/10 to-transparent p-6 border-b border-gray-800 flex justify-between items-center">
                    <div>
                        <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 w-fit mb-2">
                            <i class="ph ph-sun text-sm"></i> Shift 1 (Pagi)
                        </span>
                        <h3 class="text-lg font-bold text-white">Shift Pagi (07:00 - 15:00)</h3>
                    </div>
                    @if($reconciliationPagi && !is_null($reconciliationPagi->real_cash))
                        @if($reconciliationPagi->difference == 0)
                            <span class="px-3 py-1.5 bg-green-500/20 text-green-400 border border-green-500/30 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="ph ph-check-circle"></i> Kas Pas
                            </span>
                        @elseif($reconciliationPagi->difference < 0)
                            <span class="px-3 py-1.5 bg-red-500/20 text-red-400 border border-red-500/30 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="ph ph-warning-circle"></i> Kurang Rp {{ number_format(abs($reconciliationPagi->difference), 0, ',', '.') }}
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="ph ph-plus-circle"></i> Lebih Rp {{ number_format($reconciliationPagi->difference, 0, ',', '.') }}
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1.5 bg-gray-800 text-gray-400 border border-gray-700 rounded-full text-xs font-medium">
                            Belum Dikonfirmasi
                        </span>
                    @endif
                </div>

                <!-- Rincian Sistem -->
                <div class="p-6 space-y-4">
                    <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold border-b border-gray-800 pb-2">Pendapatan Sistem (Jam 07:00 - 15:00)</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-dark p-3.5 rounded-lg border border-gray-800">
                            <span class="text-xs text-gray-400 block mb-1">Penerimaan Cash (Tunai)</span>
                            <span class="text-lg font-bold text-emerald-400">Rp {{ number_format($pagiSystemCash, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-dark p-3.5 rounded-lg border border-gray-800">
                            <span class="text-xs text-gray-400 block mb-1">Penerimaan Transfer / QRIS</span>
                            <span class="text-lg font-bold text-indigo-400">Rp {{ number_format($pagiSystemTransfer, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-dark/80 p-4 rounded-xl border border-gray-800 flex justify-between items-center">
                        <span class="text-gray-400 text-sm font-medium">Total Pendapatan Sistem</span>
                        <span class="text-xl font-bold text-neon">Rp {{ number_format($pagiSystemCash + $pagiSystemTransfer, 0, ',', '.') }}</span>
                    </div>

                    <!-- Ringkasan Rekonsiliasi Real -->
                    @if($reconciliationPagi && !is_null($reconciliationPagi->real_cash))
                        <div class="pt-2">
                            <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold border-b border-gray-800 pb-2 mb-3">Hasil Rekonsiliasi Fisik</h4>
                            <div class="space-y-2 text-sm bg-dark/40 p-4 rounded-xl border border-gray-800">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Uang Fisik (Real di Laci):</span>
                                    <span class="text-white font-bold">Rp {{ number_format($reconciliationPagi->real_cash, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Uang Tunai Sistem:</span>
                                    <span class="text-gray-300">Rp {{ number_format($pagiSystemCash, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-800">
                                    <span class="text-gray-400 font-medium">Selisih (Fisik - Sistem):</span>
                                    @if($reconciliationPagi->difference == 0)
                                        <span class="text-green-400 font-bold">Rp 0 (PAS)</span>
                                    @elseif($reconciliationPagi->difference < 0)
                                        <span class="text-red-400 font-bold">- Rp {{ number_format(abs($reconciliationPagi->difference), 0, ',', '.') }} (KURANG)</span>
                                    @else
                                        <span class="text-yellow-400 font-bold">+ Rp {{ number_format($reconciliationPagi->difference, 0, ',', '.') }} (LEBIH)</span>
                                    @endif
                                </div>
                                @if($reconciliationPagi->notes)
                                    <div class="pt-2 border-t border-gray-800 text-xs text-gray-400 italic">
                                        <span class="font-semibold text-gray-300">Catatan:</span> {{ $reconciliationPagi->notes }}
                                    </div>
                                @endif
                                <div class="text-[11px] text-gray-500 pt-1">
                                    Petugas: {{ $reconciliationPagi->user->name ?? '-' }} ({{ \Carbon\Carbon::parse($reconciliationPagi->updated_at)->format('H:i WITA/WIB') }})
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Input Uang Real Shift Pagi -->
            <div class="p-6 bg-dark/50 border-t border-gray-800">
                <h4 class="text-xs uppercase tracking-wider text-gray-300 font-bold mb-3 flex items-center gap-1.5">
                    <i class="ph ph-[#000] ph-pencil-simple text-neon"></i> {{ $reconciliationPagi ? 'Update' : 'Input' }} Uang Fisik / Real Shift Pagi
                </h4>
                <form method="POST" action="{{ route('shift-reports.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="shift_type" value="pagi">

                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Jumlah Uang Cash Real di Laci (Rp)</label>
                        <input type="number" step="0.01" name="real_cash" value="{{ old('real_cash', $reconciliationPagi->real_cash ?? '') }}" class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-2.5 text-white font-bold focus:ring-neon focus:border-neon font-mono" placeholder="Masukkan jumlah uang cash fisik" required>
                        <p class="text-[11px] text-gray-500 mt-1">Sistem Tunai Pagi: <span class="text-emerald-400 font-mono">Rp {{ number_format($pagiSystemCash, 0, ',', '.') }}</span></p>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Keterangan / Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full bg-dark border border-gray-700 rounded-lg px-3 py-2 text-white text-xs focus:ring-neon focus:border-neon" placeholder="Catatan selisih atau alasan jika ada selisih uang...">{{ old('notes', $reconciliationPagi->notes ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-400 text-darker font-bold text-sm rounded-lg transition-colors flex justify-center items-center gap-2">
                        <i class="ph ph-floppy-disk text-base"></i> Simpan Laporan Shift Pagi
                    </button>
                </form>
            </div>
        </div>


        <!-- ==================== SHIFT MALAM ==================== -->
        <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col justify-between">
            <div>
                <!-- Shift Header -->
                <div class="bg-gradient-to-r from-blue-500/10 to-transparent p-6 border-b border-gray-800 flex justify-between items-center">
                    <div>
                        <span class="px-2.5 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 w-fit mb-2">
                            <i class="ph ph-moon text-sm"></i> Shift 2 (Malam)
                        </span>
                        <h3 class="text-lg font-bold text-white">Shift Malam (15:00 - 23:00)</h3>
                    </div>
                    @if($reconciliationMalam && !is_null($reconciliationMalam->real_cash))
                        @if($reconciliationMalam->difference == 0)
                            <span class="px-3 py-1.5 bg-green-500/20 text-green-400 border border-green-500/30 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="ph ph-check-circle"></i> Kas Pas
                            </span>
                        @elseif($reconciliationMalam->difference < 0)
                            <span class="px-3 py-1.5 bg-red-500/20 text-red-400 border border-red-500/30 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="ph ph-warning-circle"></i> Kurang Rp {{ number_format(abs($reconciliationMalam->difference), 0, ',', '.') }}
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="ph ph-plus-circle"></i> Lebih Rp {{ number_format($reconciliationMalam->difference, 0, ',', '.') }}
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1.5 bg-gray-800 text-gray-400 border border-gray-700 rounded-full text-xs font-medium">
                            Belum Dikonfirmasi
                        </span>
                    @endif
                </div>

                <!-- Rincian Sistem -->
                <div class="p-6 space-y-4">
                    <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold border-b border-gray-800 pb-2">Pendapatan Sistem (Jam 15:00 - 23:00)</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-dark p-3.5 rounded-lg border border-gray-800">
                            <span class="text-xs text-gray-400 block mb-1">Penerimaan Cash (Tunai)</span>
                            <span class="text-lg font-bold text-emerald-400">Rp {{ number_format($malamSystemCash, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-dark p-3.5 rounded-lg border border-gray-800">
                            <span class="text-xs text-gray-400 block mb-1">Penerimaan Transfer / QRIS</span>
                            <span class="text-lg font-bold text-indigo-400">Rp {{ number_format($malamSystemTransfer, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-dark/80 p-4 rounded-xl border border-gray-800 flex justify-between items-center">
                        <span class="text-gray-400 text-sm font-medium">Total Pendapatan Sistem</span>
                        <span class="text-xl font-bold text-neon">Rp {{ number_format($malamSystemCash + $malamSystemTransfer, 0, ',', '.') }}</span>
                    </div>

                    <!-- Ringkasan Rekonsiliasi Real -->
                    @if($reconciliationMalam && !is_null($reconciliationMalam->real_cash))
                        <div class="pt-2">
                            <h4 class="text-xs uppercase tracking-wider text-gray-400 font-bold border-b border-gray-800 pb-2 mb-3">Hasil Rekonsiliasi Fisik</h4>
                            <div class="space-y-2 text-sm bg-dark/40 p-4 rounded-xl border border-gray-800">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Uang Fisik (Real di Laci):</span>
                                    <span class="text-white font-bold">Rp {{ number_format($reconciliationMalam->real_cash, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Uang Tunai Sistem:</span>
                                    <span class="text-gray-300">Rp {{ number_format($malamSystemCash, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-800">
                                    <span class="text-gray-400 font-medium">Selisih (Fisik - Sistem):</span>
                                    @if($reconciliationMalam->difference == 0)
                                        <span class="text-green-400 font-bold">Rp 0 (PAS)</span>
                                    @elseif($reconciliationMalam->difference < 0)
                                        <span class="text-red-400 font-bold">- Rp {{ number_format(abs($reconciliationMalam->difference), 0, ',', '.') }} (KURANG)</span>
                                    @else
                                        <span class="text-yellow-400 font-bold">+ Rp {{ number_format($reconciliationMalam->difference, 0, ',', '.') }} (LEBIH)</span>
                                    @endif
                                </div>
                                @if($reconciliationMalam->notes)
                                    <div class="pt-2 border-t border-gray-800 text-xs text-gray-400 italic">
                                        <span class="font-semibold text-gray-300">Catatan:</span> {{ $reconciliationMalam->notes }}
                                    </div>
                                @endif
                                <div class="text-[11px] text-gray-500 pt-1">
                                    Petugas: {{ $reconciliationMalam->user->name ?? '-' }} ({{ \Carbon\Carbon::parse($reconciliationMalam->updated_at)->format('H:i WITA/WIB') }})
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Input Uang Real Shift Malam -->
            <div class="p-6 bg-dark/50 border-t border-gray-800">
                <h4 class="text-xs uppercase tracking-wider text-gray-300 font-bold mb-3 flex items-center gap-1.5">
                    <i class="ph ph-pencil-simple text-neon"></i> {{ $reconciliationMalam ? 'Update' : 'Input' }} Uang Fisik / Real Shift Malam
                </h4>
                <form method="POST" action="{{ route('shift-reports.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="shift_type" value="malam">

                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Jumlah Uang Cash Real di Laci (Rp)</label>
                        <input type="number" step="0.01" name="real_cash" value="{{ old('real_cash', $reconciliationMalam->real_cash ?? '') }}" class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-2.5 text-white font-bold focus:ring-neon focus:border-neon font-mono" placeholder="Masukkan jumlah uang cash fisik" required>
                        <p class="text-[11px] text-gray-500 mt-1">Sistem Tunai Malam: <span class="text-emerald-400 font-mono">Rp {{ number_format($malamSystemCash, 0, ',', '.') }}</span></p>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Keterangan / Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full bg-dark border border-gray-700 rounded-lg px-3 py-2 text-white text-xs focus:ring-neon focus:border-neon" placeholder="Catatan selisih atau alasan jika ada selisih uang...">{{ old('notes', $reconciliationMalam->notes ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-lg transition-colors flex justify-center items-center gap-2">
                        <i class="ph ph-floppy-disk text-base"></i> Simpan Laporan Shift Malam
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
