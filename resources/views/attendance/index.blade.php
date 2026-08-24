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

                @if(session('scanned_member'))
                    @php $sm = session('scanned_member'); @endphp
                    <div class="mb-5 p-4 rounded-xl bg-gradient-to-r from-darker to-gray-900 border {{ $sm['days_left'] > 7 ? 'border-neon/50 shadow-[0_0_15px_rgba(224,255,0,0.15)]' : ($sm['days_left'] >= 0 ? 'border-amber-500/50 shadow-[0_0_15px_rgba(245,158,11,0.15)]' : 'border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.15)]') }} relative z-10">
                        <div class="flex items-center space-x-4">
                            <div class="shrink-0">
                                @if($sm['photo_path'])
                                    <img src="{{ $sm['photo_path'] }}" alt="{{ $sm['name'] }}" class="w-16 h-16 rounded-full object-cover border-2 {{ $sm['days_left'] > 7 ? 'border-neon' : ($sm['days_left'] >= 0 ? 'border-amber-400' : 'border-red-400') }}">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-dark flex items-center justify-center text-2xl text-gray-400 border-2 {{ $sm['days_left'] > 7 ? 'border-neon' : ($sm['days_left'] >= 0 ? 'border-amber-400' : 'border-red-400') }}">
                                        <i class="ph ph-user"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <h4 class="text-lg font-bold text-white truncate">{{ $sm['name'] }}</h4>
                                    <span class="px-2 py-0.5 text-[10px] uppercase font-semibold rounded bg-gray-800 text-gray-300 border border-gray-700">{{ $sm['member_type'] ?? 'Member' }}</span>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">{{ $sm['member_id'] }}</p>

                                {{-- Remaining Days Highlight --}}
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @if($sm['days_left'] > 7)
                                        <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-green-500/20 text-green-400 border border-green-500/40 flex items-center">
                                            <i class="ph ph-hourglass-high mr-1.5 text-sm"></i> Sisa {{ $sm['days_left'] }} Hari
                                        </span>
                                    @elseif($sm['days_left'] >= 0)
                                        <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center animate-pulse">
                                            <i class="ph ph-warning-circle mr-1.5 text-sm"></i> {{ $sm['days_left'] == 0 ? 'Hari Ini Terakhir!' : 'Segera Habis! Sisa ' . $sm['days_left'] . ' Hari' }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-red-500/20 text-red-400 border border-red-500/40 flex items-center">
                                            <i class="ph ph-x-circle mr-1.5 text-sm"></i> Expired ({{ abs($sm['days_left']) }} hari lalu)
                                        </span>
                                    @endif

                                    <span class="text-xs text-gray-400">
                                        <i class="ph ph-calendar text-gray-500"></i> Berakhir: <strong class="text-gray-200">{{ $sm['expiry_date'] }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('attendance.store') }}" class="relative z-10 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Arahkan Scanner, ketik VIP ID, atau Nama Member</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-scan text-neon text-xl"></i>
                            </div>
                            <input type="text" name="identifier" id="identifier" required autofocus autocomplete="off"
                                class="block w-full pl-10 pr-3 py-3 border border-neon rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors text-lg font-mono shadow-[0_0_15px_rgba(224,255,0,0.1)]" 
                                placeholder="VIP-... atau Nama Member">
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
                <div class="bg-card rounded-xl border border-gray-800 p-4 shadow-lg text-center">
                    <p class="text-xs text-gray-400 mb-1">Rata-rata <br>Mingguan (Total)</p>
                    <p class="text-2xl font-bold text-white">{{ $averageWeekly }}</p>
                </div>
                <div class="bg-card rounded-xl border border-gray-800 p-4 shadow-lg text-center">
                    <p class="text-xs text-gray-400 mb-1">Rata-rata <br>Bulanan (Total)</p>
                    <p class="text-2xl font-bold text-white">{{ $averageMonthly }}</p>
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
                                <th class="px-6 py-4 font-medium">Sisa Masa Aktif</th>
                                <th class="px-6 py-4 font-medium">Petugas Jaga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @forelse ($attendances as $att)
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $expiry = \Carbon\Carbon::parse($att->member->expiry_date)->startOfDay();
                                    $daysLeft = (int) $today->diffInDays($expiry, false);
                                @endphp
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-neon">{{ \Carbon\Carbon::parse($att->attendance_time)->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-white">{{ $att->member->name }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $att->member->member_id }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($daysLeft > 7)
                                            <span class="px-2.5 py-1 text-xs rounded-md bg-green-500/10 text-green-400 border border-green-500/30 font-mono font-medium inline-flex items-center">
                                                <i class="ph ph-hourglass-high mr-1 text-sm"></i> {{ $daysLeft }} Hari Lagi
                                            </span>
                                        @elseif($daysLeft >= 0)
                                            <span class="px-2.5 py-1 text-xs rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/30 font-mono font-medium inline-flex items-center">
                                                <i class="ph ph-warning-circle mr-1 text-sm"></i> {{ $daysLeft == 0 ? 'Hari Ini Terakhir' : $daysLeft . ' Hari Lagi' }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs rounded-md bg-red-500/10 text-red-400 border border-red-500/30 font-mono font-medium inline-flex items-center">
                                                <i class="ph ph-x-circle mr-1 text-sm"></i> Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">
                                        <i class="ph ph-user-circle mr-1"></i> {{ $att->user->name ?? 'Sistem' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
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
