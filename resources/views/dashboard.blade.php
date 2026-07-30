<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-squares-four mr-2 text-neon"></i> {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="space-y-8 md:space-y-10">
        
        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-dark to-gray-900 rounded-xl border border-gray-800 p-8 shadow-lg relative overflow-hidden mb-8 md:mb-10" style="margin-bottom: 2.5rem;">
            <div class="absolute -right-10 -top-10 text-gray-800 opacity-20">
                <i class="ph ph-barbell text-[200px]"></i>
            </div>
            <div class="relative z-10">
                <h3 class="text-3xl font-bold text-white mb-2">Selamat Datang kembali, <span class="text-neon">{{ Auth::user()->name }}</span>! 👋</h3>
                <p class="text-gray-400">Berikut adalah ringkasan performa dan aktivitas gym Anda hari ini.</p>
            </div>
        </div>

        {{-- 4 Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 mb-8 md:mb-10" style="margin-bottom: 2.5rem;">
            
            {{-- Total Member Aktif --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 md:p-7 shadow-lg relative overflow-hidden group hover:border-neon/50 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-users text-7xl text-neon"></i>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-neon/10 flex items-center justify-center text-neon">
                        <i class="ph ph-users text-2xl"></i>
                    </div>
                    <h3 class="text-gray-400 font-medium">Member Aktif</h3>
                </div>
                <p class="text-4xl font-bold text-white font-mono relative z-10">{{ number_format($activeMembersCount) }}</p>
            </div>

            {{-- Pendapatan Bulan Ini --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 md:p-7 shadow-lg relative overflow-hidden group hover:border-green-500/50 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-money text-7xl text-green-500"></i>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-green-500/10 flex items-center justify-center text-green-400">
                        <i class="ph ph-money text-2xl"></i>
                    </div>
                    <h3 class="text-gray-400 font-medium">Pendapatan (Bulan Ini)</h3>
                </div>
                <p class="text-2xl font-bold text-green-400 font-mono relative z-10">Rp {{ number_format($totalIncomeThisMonth, 0, ',', '.') }}</p>
            </div>

            {{-- Kunjungan Hari Ini --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 md:p-7 shadow-lg relative overflow-hidden group hover:border-blue-500/50 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-person-simple-run text-7xl text-blue-500"></i>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                        <i class="ph ph-person-simple-run text-2xl"></i>
                    </div>
                    <h3 class="text-gray-400 font-medium">Kunjungan Hari Ini</h3>
                </div>
                <p class="text-4xl font-bold text-white font-mono relative z-10">{{ number_format($memberVisitsToday) }}</p>
            </div>

            {{-- Karyawan Hadir --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 md:p-7 shadow-lg relative overflow-hidden group hover:border-purple-500/50 transition-colors">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="ph ph-identification-badge text-7xl text-purple-500"></i>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                        <i class="ph ph-identification-badge text-2xl"></i>
                    </div>
                    <h3 class="text-gray-400 font-medium">Karyawan Hadir</h3>
                </div>
                <p class="text-4xl font-bold text-white font-mono relative z-10">{{ number_format($employeePresentToday) }}</p>
            </div>

        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 mb-8 md:mb-10" style="margin-bottom: 2.5rem;">
            
            {{-- Grafik Kehadiran Member --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 md:p-7 shadow-lg flex flex-col">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-800">
                    <h3 class="text-white font-medium flex items-center">
                        <i class="ph ph-chart-line-up text-neon text-xl mr-2"></i> Grafik Kehadiran Member (Bulanan)
                    </h3>
                    <span class="text-xs font-mono text-neon bg-neon/10 border border-neon/30 px-2.5 py-1 rounded-full">{{ date('Y') }}</span>
                </div>
                <div class="relative flex-1 min-h-[300px]">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            {{-- Grafik Member Baru vs Perpanjang --}}
            <div class="bg-card rounded-xl border border-gray-800 p-6 md:p-7 shadow-lg flex flex-col">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-800">
                    <h3 class="text-white font-medium flex items-center">
                        <i class="ph ph-users-three text-cyan-400 text-xl mr-2"></i> Grafik Member Baru vs Perpanjang
                    </h3>
                    <span class="text-xs font-mono text-cyan-400 bg-cyan-500/10 border border-cyan-500/30 px-2.5 py-1 rounded-full">{{ date('Y') }}</span>
                </div>
                <div class="relative flex-1 min-h-[300px]">
                    <canvas id="memberTypeChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Tables Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8" style="margin-top: 2.5rem;">
            
            {{-- 5 Transaksi Terbaru --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                    <h3 class="text-white font-medium flex items-center"><i class="ph ph-receipt text-neon mr-2"></i> 5 Transaksi Member Terbaru</h3>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-gray-400 hover:text-neon transition-colors">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-dark border-b border-gray-800 text-xs text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 font-medium">Waktu</th>
                                <th class="px-4 py-3 font-medium">Member</th>
                                <th class="px-4 py-3 font-medium">Paket</th>
                                <th class="px-4 py-3 font-medium text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($recentTransactions as $trx)
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">{{ $trx->created_at->diffForHumans() }}</td>
                                    <td class="px-4 py-3 text-white font-medium">{{ $trx->member->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-gray-300 text-xs">{{ $trx->package->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right text-neon font-mono whitespace-nowrap">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Belum ada transaksi member.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 5 Kunjungan Terakhir --}}
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-800 bg-dark/50 flex justify-between items-center">
                    <h3 class="text-white font-medium flex items-center"><i class="ph ph-fingerprint text-blue-400 mr-2"></i> 5 Kunjungan Terakhir Hari Ini</h3>
                    <a href="{{ route('attendance.index') }}" class="text-xs text-gray-400 hover:text-blue-400 transition-colors">Buka Scanner &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-dark border-b border-gray-800 text-xs text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 font-medium">Waktu Tap</th>
                                <th class="px-4 py-3 font-medium">Member</th>
                                <th class="px-4 py-3 font-medium">Status Member</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($recentVisits as $visit)
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-400 text-xs font-mono whitespace-nowrap">{{ \Carbon\Carbon::parse($visit->attendance_time)->format('H:i:s') }}</td>
                                    <td class="px-4 py-3 text-white font-medium">{{ $visit->member->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3">
                                        @if($visit->member && $visit->member->expiry_date >= \Carbon\Carbon::today()->format('Y-m-d'))
                                            <span class="px-2 py-1 text-[10px] rounded bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 text-[10px] rounded bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 italic">Belum ada kunjungan hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = @json($chartMonths);

            // Attendance Chart
            const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
            new Chart(ctxAttendance, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Kunjungan Member',
                        data: @json($attendanceChartData),
                        borderColor: '#E0FF00',
                        backgroundColor: 'rgba(224, 255, 0, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#E0FF00',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9CA3AF', font: { family: 'Inter', size: 12 } }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#9CA3AF', font: { family: 'Inter' } },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#9CA3AF', stepSize: 1, font: { family: 'Inter' } },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        }
                    }
                }
            });

            // Member Type Chart
            const ctxMemberType = document.getElementById('memberTypeChart').getContext('2d');
            new Chart(ctxMemberType, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Member Baru',
                            data: @json($newMembersChartData),
                            backgroundColor: '#00F0FF',
                            borderRadius: 6
                        },
                        {
                            label: 'Perpanjang',
                            data: @json($renewalMembersChartData),
                            backgroundColor: '#A855F7',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9CA3AF', font: { family: 'Inter', size: 12 } }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#9CA3AF', font: { family: 'Inter' } },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#9CA3AF', stepSize: 1, font: { family: 'Inter' } },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
