<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('members.index') }}" class="flex items-center space-x-1 text-gray-400 hover:text-neon transition-colors px-3 py-1.5 bg-dark border border-gray-700 rounded-lg hover:border-neon text-sm font-medium">
                <i class="ph ph-arrow-left text-lg"></i>
                <span>Kembali</span>
            </a>
            <span class="pl-2 border-l border-gray-700 font-semibold text-lg">Detail Member: {{ $member->name }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Profile Card -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg text-center">
                <div class="h-24 w-24 rounded-full bg-gray-800 border-2 border-neon flex items-center justify-center overflow-hidden mx-auto mb-4">
                    @if($member->photo_path)
                        <img src="{{ Storage::url($member->photo_path) }}" class="h-full w-full object-cover">
                    @else
                        <i class="ph ph-user text-4xl text-gray-500"></i>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-white">{{ $member->name }}</h2>
                <p class="text-neon font-mono text-sm mt-1">{{ $member->member_id }}</p>

                <div class="mt-3">
                    @if($member->status === 'active')
                        <span class="px-3 py-1 text-sm rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                    @elseif($member->status === 'pending')
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 font-medium">Belum Aktif</span>
                    @else
                        <span class="px-3 py-1 text-sm rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-800 text-left text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-gray-400">Tipe</span><span class="text-white">{{ ucfirst($member->member_type) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Aktif</span><span class="text-white">{{ \Carbon\Carbon::parse($member->activation_date)->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Expired</span><span class="text-{{ $member->status === 'expired' ? 'red' : 'white' }}-400">{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Perpanjangan</span><span class="text-white">{{ $member->extension_count }}x</span></div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('members.ecard', $member->id) }}" target="_blank" class="flex items-center justify-center space-x-1 bg-dark border border-neon text-neon hover:bg-neon/10 py-2 px-3 rounded-lg text-xs font-medium transition-colors">
                        <i class="ph ph-identification-card"></i><span>E-Card</span>
                    </a>
                    <a href="{{ route('members.edit', $member->id) }}" class="flex items-center justify-center space-x-1 bg-dark border border-gray-700 text-gray-300 hover:bg-gray-800 py-2 px-3 rounded-lg text-xs font-medium transition-colors">
                        <i class="ph ph-pencil-simple"></i><span>Edit Data</span>
                    </a>
                </div>
            </div>

            <!-- Stats Absensi -->
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                <h3 class="text-white font-medium mb-3 text-sm uppercase tracking-wider">Statistik Kehadiran</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center bg-dark rounded-lg p-3 border border-gray-800">
                        <span class="text-gray-400 text-sm">Minggu Ini</span>
                        <span class="text-neon font-bold text-lg">{{ $attendanceStats['this_week'] }}x</span>
                    </div>
                    <div class="flex justify-between items-center bg-dark rounded-lg p-3 border border-gray-800">
                        <span class="text-gray-400 text-sm">Bulan Ini</span>
                        <span class="text-neon font-bold text-lg">{{ $attendanceStats['this_month'] }}x</span>
                    </div>
                    <div class="flex justify-between items-center bg-dark rounded-lg p-3 border border-gray-800">
                        <span class="text-gray-400 text-sm">Total Kunjungan</span>
                        <span class="text-neon font-bold text-lg">{{ $attendanceStats['total'] }}x</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Diri -->
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2">Data Diri</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-400">NIK</dt><dd class="text-white font-mono mt-1">{{ $member->nik }}</dd></div>
                    <div><dt class="text-gray-400">Jenis Kelamin</dt><dd class="text-white mt-1">{{ $member->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                    <div><dt class="text-gray-400">Tempat, Tgl Lahir</dt><dd class="text-white mt-1">{{ $member->place_of_birth }}, {{ \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y') }}</dd></div>
                    <div><dt class="text-gray-400">Pekerjaan</dt><dd class="text-white mt-1">{{ $member->job ?: '-' }}</dd></div>
                    <div><dt class="text-gray-400">No. WhatsApp</dt><dd class="text-white mt-1">{{ $member->phone }}</dd></div>
                    <div><dt class="text-gray-400">Email</dt><dd class="text-white mt-1">{{ $member->email }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-400">Alamat</dt><dd class="text-white mt-1">{{ $member->address }}</dd></div>
                </dl>
            </div>

            <!-- Perpanjangan Paket -->
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center">
                    <i class="ph ph-arrows-clockwise text-neon mr-2"></i> Perpanjangan Paket (Renewal)
                </h3>
                <form method="POST" action="{{ route('members.renewal', $member->id) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($packages as $pkg)
                            <label class="relative block cursor-pointer group">
                                <input type="radio" name="package_id" value="{{ $pkg->id }}" class="peer sr-only" required>
                                <div class="rounded-lg border-2 border-gray-700 bg-dark p-3 transition-colors peer-checked:border-neon peer-checked:bg-neon/10 group-hover:border-neon/50 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $pkg->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $pkg->duration }} {{ $pkg->duration_unit }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-neon">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2 px-6 rounded-lg transition-colors text-sm flex items-center">
                            <i class="ph ph-receipt text-lg mr-2"></i> Proses Perpanjangan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Riwayat Kehadiran -->
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-800">
                    <h3 class="text-white font-medium">Riwayat Kehadiran Terbaru</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-800">
                        @forelse($recentAttendances as $att)
                            <tr class="hover:bg-dark/50">
                                <td class="px-6 py-3 font-mono text-neon">{{ \Carbon\Carbon::parse($att->attendance_time)->format('d M Y') }}</td>
                                <td class="px-6 py-3 text-gray-400">{{ \Carbon\Carbon::parse($att->attendance_time)->format('H:i') }} WIB</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-6 text-center text-gray-500">Belum ada riwayat kehadiran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
