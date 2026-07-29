<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-calendar-check mr-2 text-neon"></i> {{ __('Absensi Karyawan') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        
        {{-- Header Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('employee-attendances.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                <select name="month" class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
                <input type="number" name="year" value="{{ $year }}" min="2020" class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm w-24">
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors text-sm border border-gray-700 flex items-center">
                    <i class="ph ph-funnel mr-2"></i> Filter
                </button>
            </form>

            <a href="{{ route('employee-attendances.create') }}" class="w-full md:w-auto px-6 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors shadow-lg shadow-neon/20 flex items-center justify-center">
                <i class="ph ph-list-checks mr-2"></i> Input Absen Harian
            </a>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                <h3 class="text-white font-medium">Riwayat Absensi (Bulan {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-dark border-b border-gray-800">
                        <tr class="text-xs uppercase tracking-wider text-gray-400">
                            <th class="px-6 py-4 font-medium">Tanggal</th>
                            <th class="px-6 py-4 font-medium">Karyawan</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm">
                        @forelse ($attendances as $att)
                            <tr class="hover:bg-dark/50 transition-colors">
                                <td class="px-6 py-4 text-white font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ $att->employee->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $colorClass = match($att->status) {
                                            'Hadir' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                            'Izin' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                            'Sakit' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                            'Alpa' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                            'Libur' => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                                            default => 'bg-gray-800 text-gray-400'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full border {{ $colorClass }}">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-400 italic">
                                    {{ $att->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <i class="ph ph-calendar-x text-4xl mb-2 block text-gray-600"></i>
                                    Belum ada data absensi di bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
