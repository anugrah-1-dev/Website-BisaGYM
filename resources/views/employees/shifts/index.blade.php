<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <span>{{ __('Jadwal Shift') }} - {{ $employee->name }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Form Tambah/Edit Jadwal --}}
        <div class="lg:col-span-1">
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg sticky top-6">
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                    <i class="ph ph-calendar-plus text-neon"></i> Set Jadwal Baru
                </h3>
                
                <form method="POST" action="{{ route('employees.shifts.store', $employee->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    
                    <div>
                        <label class="flex items-center space-x-2 cursor-pointer mb-3">
                            <input type="checkbox" name="is_day_off" id="is_day_off" value="1" class="rounded border-gray-700 bg-dark text-neon focus:ring-neon">
                            <span class="text-sm font-medium text-gray-300">Tandai sebagai Hari Libur</span>
                        </label>
                    </div>

                    <div id="shift_fields" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Tipe Shift</label>
                            <input type="text" name="shift_type" id="shift_type" value="Pagi" required placeholder="Contoh: Pagi, Siang, Malam"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Jam Mulai</label>
                                <input type="time" name="start_time" id="start_time" value="07:00"
                                    class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Jam Selesai</label>
                                <input type="time" name="end_time" id="end_time" value="15:00"
                                    class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center justify-center">
                            <i class="ph ph-floppy-disk mr-2"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Riwayat Jadwal --}}
        <div class="lg:col-span-2">
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                                <th class="px-6 py-4 font-medium">Tanggal</th>
                                <th class="px-6 py-4 font-medium">Tipe Shift</th>
                                <th class="px-6 py-4 font-medium text-center">Waktu</th>
                                <th class="px-6 py-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @forelse ($shifts as $shift)
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-6 py-4 text-white font-medium">
                                        {{ \Carbon\Carbon::parse($shift->date)->format('d M Y') }}
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($shift->date)->locale('id')->isoFormat('dddd') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($shift->is_day_off)
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Libur</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">{{ $shift->shift_type }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-300 font-mono">
                                        @if($shift->is_day_off)
                                            -
                                        @else
                                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('employees.shifts.destroy', $shift->id) }}" class="inline" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        <i class="ph ph-calendar-x text-4xl mb-2 block text-gray-600"></i>
                                        Belum ada jadwal yang diatur.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('is_day_off').addEventListener('change', function() {
            const fields = document.getElementById('shift_fields');
            if (this.checked) {
                fields.classList.add('opacity-50', 'pointer-events-none');
                document.getElementById('shift_type').required = false;
            } else {
                fields.classList.remove('opacity-50', 'pointer-events-none');
                document.getElementById('shift_type').required = true;
            }
        });
    </script>
</x-app-layout>
