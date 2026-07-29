<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
                <i class="ph ph-list-checks mr-2 text-neon"></i> {{ __('Input Absensi Harian') }}
            </h2>
            <a href="{{ route('employee-attendances.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                &larr; Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('employee-attendances.store') }}" class="space-y-6">
            @csrf
            
            {{-- Pilih Tanggal --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg flex flex-col md:flex-row gap-6 items-center justify-between">
                <div>
                    <h3 class="text-white font-medium text-lg">Pilih Tanggal Absen</h3>
                    <p class="text-sm text-gray-400">Pilih tanggal untuk mencatat absensi karyawan.</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="date" name="date" id="attendance_date" value="{{ $date }}" required
                        class="w-full md:w-48 border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-lg font-mono"
                        onchange="window.location.href='{{ route('employee-attendances.create') }}?date=' + this.value">
                </div>
            </div>

            {{-- Daftar Karyawan --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
                <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                    <h3 class="text-white font-medium">Daftar Karyawan Aktif</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    @forelse($employees as $emp)
                        @php
                            $existing = $existingAttendances->get($emp->id);
                            $currentStatus = $existing ? $existing->status : 'Hadir';
                            $currentNotes = $existing ? $existing->notes : '';
                        @endphp
                        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center p-4 rounded-lg border {{ $existing ? 'border-neon/30 bg-neon/5' : 'border-gray-800 bg-dark' }}">
                            
                            {{-- Info Karyawan --}}
                            <div class="flex-1">
                                <p class="text-white font-bold">{{ $emp->name }}</p>
                                <p class="text-xs text-gray-400">{{ $emp->position }}</p>
                            </div>
                            
                            {{-- Input Status & Keterangan --}}
                            <div class="flex flex-col md:flex-row gap-3 w-full md:w-2/3">
                                <select name="attendances[{{ $emp->id }}][status]" class="w-full md:w-1/3 border-gray-700 rounded-lg bg-gray-900 text-white focus:ring-neon focus:border-neon text-sm font-medium">
                                    <option value="Hadir" {{ $currentStatus == 'Hadir' ? 'selected' : '' }}>🟢 Hadir</option>
                                    <option value="Izin" {{ $currentStatus == 'Izin' ? 'selected' : '' }}>🔵 Izin</option>
                                    <option value="Sakit" {{ $currentStatus == 'Sakit' ? 'selected' : '' }}>🟡 Sakit</option>
                                    <option value="Alpa" {{ $currentStatus == 'Alpa' ? 'selected' : '' }}>🔴 Alpa</option>
                                    <option value="Libur" {{ $currentStatus == 'Libur' ? 'selected' : '' }}>⚪ Libur</option>
                                </select>
                                <input type="text" name="attendances[{{ $emp->id }}][notes]" value="{{ $currentNotes }}" placeholder="Keterangan (Opsional)..." 
                                    class="w-full md:w-2/3 border-gray-700 rounded-lg bg-gray-900 text-gray-300 focus:ring-neon focus:border-neon text-sm">
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="ph ph-users text-4xl mb-2 block text-gray-600"></i>
                            Belum ada data karyawan yang aktif.
                        </div>
                    @endforelse
                </div>

                @if($employees->count() > 0)
                <div class="p-6 border-t border-gray-800 bg-dark/30">
                    <button type="submit" class="w-full py-3 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold text-lg transition-colors shadow-lg shadow-neon/20 flex items-center justify-center">
                        <i class="ph ph-floppy-disk mr-2"></i> Simpan Absensi Hari Ini
                    </button>
                    @if($existingAttendances->count() > 0)
                        <p class="text-center text-xs text-neon mt-3"><i class="ph ph-info"></i> Data absensi untuk tanggal ini sudah pernah disimpan sebelumnya. Menyimpan ulang akan memperbarui data.</p>
                    @endif
                </div>
                @endif
            </div>

        </form>
    </div>
</x-app-layout>
