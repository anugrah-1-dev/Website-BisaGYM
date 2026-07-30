<x-app-layout>
    <x-slot name="header">
        {{ __('Scan Absensi Member') }}
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scan Box -->
        <div class="lg:col-span-1">
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="ph ph-barcode text-9xl text-neon"></i>
                </div>
                
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 relative z-10">Scan E-Card / QR Code</h3>
                
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 text-sm flex items-start relative z-10">
                        <i class="ph ph-check-circle text-xl mr-2 mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm flex items-start relative z-10">
                        <i class="ph ph-warning-circle text-xl mr-2 mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="mb-4 p-4 rounded-lg bg-yellow-500/10 border border-yellow-500/50 text-yellow-400 text-sm flex items-start relative z-10">
                        <i class="ph ph-info text-xl mr-2 mt-0.5"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('attendance.store') }}" class="relative z-10 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Arahkan Scanner atau ketik VIP ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-scan text-neon text-xl"></i>
                            </div>
                            <input type="text" name="member_id" id="member_id" required autofocus autocomplete="off"
                                class="block w-full pl-10 pr-3 py-3 border border-neon rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors text-lg font-mono shadow-[0_0_15px_rgba(224,255,0,0.1)]" 
                                placeholder="VIP-YYYYMMDD-...">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-neon hover:bg-[#c4e600] text-darker font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i class="ph ph-check-square-offset text-xl"></i>
                        <span>Proses Absensi</span>
                    </button>
                </form>
                
                <p class="text-xs text-gray-500 mt-4 text-center relative z-10">
                    Pastikan cursor berada di dalam kotak input saat melakukan scan QR Code. Scanner otomatis akan memproses data.
                </p>
            </div>
            
            <!-- Attendance Stats -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="bg-card rounded-xl border border-gray-800 p-4 shadow-lg text-center">
                    <p class="text-xs text-gray-400 mb-1">Total Hadir <br>(Tanggal Terpilih)</p>
                    <p class="text-2xl font-bold text-neon">{{ $totalSelectedDate }}</p>
                </div>
                <div class="bg-card rounded-xl border border-gray-800 p-4 shadow-lg text-center">
                    <p class="text-xs text-gray-400 mb-1">Rata-rata <br>Harian (Total)</p>
                    <p class="text-2xl font-bold text-white">{{ $averageDaily }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Table -->
        <div class="lg:col-span-2">
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col h-full">
                <div class="p-6 border-b border-gray-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-white font-medium">Log Kehadiran</h3>
                    
                    <form method="GET" action="{{ route('attendance.index') }}" class="flex items-center gap-2">
                        <label for="date" class="text-sm text-gray-400">Tanggal:</label>
                        <input type="date" name="date" id="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="border-gray-700 rounded bg-dark text-white text-sm focus:ring-neon focus:border-neon px-3 py-1.5">
                    </form>
                </div>
                
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark/50 border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                                <th class="px-6 py-4 font-medium">Waktu</th>
                                <th class="px-6 py-4 font-medium">Member</th>
                                <th class="px-6 py-4 font-medium">Petugas Jaga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @forelse ($attendances as $att)
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-neon">{{ \Carbon\Carbon::parse($att->attendance_time)->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-white">{{ $att->member->name }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $att->member->member_id }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">
                                        <i class="ph ph-user-circle mr-1"></i> {{ $att->user->name ?? 'Sistem' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                        <i class="ph ph-clock text-4xl mb-2 block text-gray-600"></i>
                                        Belum ada member yang absen hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // Extra safety measure for scanners that don't send Enter automatically
            // Standard barcode scanners send Enter at the end, which triggers standard form submit.
            // If they don't, this snippet submits if length matches standard VIP ID length.
            const input = document.getElementById('member_id');
            if(input) {
                input.addEventListener('input', function() {
                    if (this.value.startsWith('VIP-') && this.value.length >= 24) { // Length of VIP-YYYYMMDD-HHMMSS-XXXX
                        this.form.submit();
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
