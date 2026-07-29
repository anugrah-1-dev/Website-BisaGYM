<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-briefcase mr-2 text-neon"></i> {{ __('Informasi Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Profile Card --}}
        <div class="bg-card rounded-xl border border-gray-800 shadow-lg p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
            <div class="w-24 h-24 rounded-full bg-dark flex items-center justify-center text-4xl text-neon border-2 border-gray-700 shrink-0">
                <i class="ph ph-user"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-2xl font-bold text-white">{{ $employee->name }}</h3>
                <p class="text-neon font-medium mb-4">{{ $employee->position ?? 'Karyawan' }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-dark p-3 rounded-lg border border-gray-800">
                        <p class="text-gray-500 mb-1"><i class="ph ph-whatsapp"></i> No. Telepon</p>
                        <p class="text-white">{{ $employee->phone ?? '-' }}</p>
                    </div>
                    <div class="bg-dark p-3 rounded-lg border border-gray-800">
                        <p class="text-gray-500 mb-1"><i class="ph ph-calendar-star"></i> Tanggal Bergabung</p>
                        <p class="text-white">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <div class="bg-dark p-3 rounded-lg border border-gray-800">
                        <p class="text-gray-500 mb-1"><i class="ph ph-money"></i> Gaji Pokok</p>
                        <p class="text-white font-mono">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Jadwal Shift Terkini --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-800 flex items-center justify-between bg-dark/50">
                    <h3 class="text-white font-medium flex items-center"><i class="ph ph-calendar-blank text-blue-400 mr-2"></i> Jadwal Shift Terkini</h3>
                </div>
                <div class="p-4 flex-1 overflow-y-auto max-h-[400px]">
                    <div class="space-y-3">
                        @forelse ($employee->shifts as $shift)
                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $shift->is_day_off ? 'border-red-500/20 bg-red-500/5' : 'border-gray-800 bg-dark' }}">
                                <div>
                                    <p class="text-white font-medium">{{ \Carbon\Carbon::parse($shift->date)->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($shift->date)->locale('id')->isoFormat('dddd') }}</p>
                                </div>
                                <div class="text-right">
                                    @if($shift->is_day_off)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Libur</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 mb-1 inline-block">{{ $shift->shift_type }}</span>
                                        <p class="text-xs font-mono text-gray-300">
                                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="ph ph-calendar-x text-3xl mb-2 block"></i>
                                Belum ada jadwal shift.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Riwayat Gaji --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-800 flex items-center justify-between bg-dark/50">
                    <h3 class="text-white font-medium flex items-center"><i class="ph ph-receipt text-green-400 mr-2"></i> Riwayat Penggajian</h3>
                </div>
                <div class="p-4 flex-1 overflow-y-auto max-h-[400px]">
                    <div class="space-y-3">
                        @forelse ($employee->payrolls as $pr)
                            <div class="p-3 rounded-lg border border-gray-800 bg-dark">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="text-white font-bold">{{ date('F', mktime(0,0,0,$pr->month,1)) }} {{ $pr->year }}</p>
                                    @if($pr->status === 'paid')
                                        <span class="px-2 py-1 text-[10px] rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Lunas</span>
                                    @else
                                        <span class="px-2 py-1 text-[10px] rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Belum Dibayar</span>
                                    @endif
                                </div>
                                <div class="space-y-1 text-xs mb-3 border-t border-b border-gray-800 py-2">
                                    <div class="flex justify-between text-gray-300">
                                        <span>Gaji Pokok</span> <span>Rp {{ number_format($pr->base_salary, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-green-400">
                                        <span>Tunjangan/Bonus</span> <span>+Rp {{ number_format($pr->allowances, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-red-400">
                                        <span>Potongan</span> <span>-Rp {{ number_format($pr->deductions, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400 text-xs">Total Diterima</span>
                                    <span class="text-neon font-bold text-lg font-mono">Rp {{ number_format($pr->total_salary, 0, ',', '.') }}</span>
                                </div>
                                @if($pr->notes)
                                    <p class="text-[10px] text-gray-500 mt-2 bg-gray-900 p-2 rounded">Catatan: {{ $pr->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="ph ph-wallet text-3xl mb-2 block"></i>
                                Belum ada riwayat gaji.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
