<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-briefcase mr-2 text-neon"></i> {{ __('Informasi Pekerjaan Saya') }}
        </h2>
    </x-slot>

    <div class="space-y-6">

        {{-- Info Karyawan --}}
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="ph ph-identification-badge text-neon mr-2"></i> Data Pribadi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Nama:</span>
                    <span class="text-white font-medium ml-2">{{ $employee->name }}</span>
                </div>
                <div>
                    <span class="text-gray-400">No. Telepon:</span>
                    <span class="text-white font-medium ml-2">{{ $employee->phone ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Posisi:</span>
                    <span class="text-white font-medium ml-2">{{ $employee->position ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Gaji Pokok:</span>
                    <span class="text-neon font-mono font-medium ml-2">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Tanggal Bergabung:</span>
                    <span class="text-white font-medium ml-2">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Status:</span>
                    @if($employee->status === 'active')
                        <span class="ml-2 px-2 py-1 text-xs rounded bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                    @else
                        <span class="ml-2 px-2 py-1 text-xs rounded bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Riwayat Shift --}}
        <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-800 bg-dark/50">
                <h3 class="text-white font-medium flex items-center">
                    <i class="ph ph-clock text-blue-400 mr-2"></i> Riwayat Shift Terakhir
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-dark border-b border-gray-800 text-xs text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Jam Masuk</th>
                            <th class="px-4 py-3 font-medium">Jam Keluar</th>
                            <th class="px-4 py-3 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($employee->shifts->sortByDesc('date')->take(10) as $shift)
                            <tr class="hover:bg-dark/50 transition-colors">
                                <td class="px-4 py-3 text-white font-mono text-xs">{{ \Carbon\Carbon::parse($shift->date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $shift->start_time ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $shift->end_time ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $shift->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data shift.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Riwayat Gaji --}}
        <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-800 bg-dark/50">
                <h3 class="text-white font-medium flex items-center">
                    <i class="ph ph-money text-green-400 mr-2"></i> Riwayat Gaji Terakhir
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-dark border-b border-gray-800 text-xs text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-medium">Periode</th>
                            <th class="px-4 py-3 font-medium">Gaji Pokok</th>
                            <th class="px-4 py-3 font-medium">Bonus</th>
                            <th class="px-4 py-3 font-medium">Potongan</th>
                            <th class="px-4 py-3 font-medium text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($employee->payrolls->sortByDesc('period')->take(10) as $payroll)
                            <tr class="hover:bg-dark/50 transition-colors">
                                <td class="px-4 py-3 text-white font-mono text-xs">{{ $payroll->period }}</td>
                                <td class="px-4 py-3 text-gray-300 font-mono">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-green-400 font-mono">+Rp {{ number_format($payroll->bonus ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-red-400 font-mono">-Rp {{ number_format($payroll->deduction ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-neon font-mono font-semibold">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data gaji.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
