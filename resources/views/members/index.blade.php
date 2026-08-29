<x-app-layout>
    <x-slot name="header">
        {{ __('Data Member') }}
    </x-slot>

    <div class="mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('members.index') }}" class="flex flex-col sm:flex-row w-full lg:max-w-2xl gap-2">
            <div class="relative w-full sm:w-2/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors" placeholder="Cari nama, NIK, atau ID VIP...">
            </div>
            <div class="w-full sm:w-1/3">
                <select name="status" onchange="this.form.submit()" class="block w-full py-2 px-3 border border-gray-700 rounded-lg bg-dark text-white focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Aktif</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired / Inaktif</option>
                </select>
            </div>
        </form>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <a href="{{ route('members.create-existing') }}" class="flex items-center space-x-2 bg-gray-800 hover:bg-gray-700 text-neon border border-neon/30 font-bold py-2 px-4 rounded-lg transition-colors shadow-md w-full sm:w-auto justify-center">
                <i class="ph ph-identification-card text-xl"></i>
                <span>Registrasi Member Lama</span>
            </a>
            <a href="{{ route('members.create') }}" class="flex items-center space-x-2 bg-neon hover:bg-[#c4e600] text-darker font-bold py-2 px-4 rounded-lg transition-colors shadow-lg shadow-neon/20 w-full sm:w-auto justify-center">
                <i class="ph ph-user-plus text-xl"></i>
                <span>Registrasi Member Baru</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Member Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-8">
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg flex items-center gap-5 hover:border-gray-700 transition-colors">
            <div class="w-14 h-14 shrink-0 rounded-full bg-neon/10 text-neon flex items-center justify-center text-3xl">
                <i class="ph ph-users"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400 mb-1">Total Member</p>
                <p class="text-3xl font-bold text-white tracking-tight">{{ number_format($stats['total'] ?? 0) }}</p>
            </div>
        </div>
        
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg flex items-center gap-5 hover:border-gray-700 transition-colors">
            <div class="w-14 h-14 shrink-0 rounded-full bg-green-500/10 text-green-400 flex items-center justify-center text-3xl">
                <i class="ph ph-user-check"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400 mb-1">Member Aktif</p>
                <p class="text-3xl font-bold text-white tracking-tight">{{ number_format($stats['active'] ?? 0) }}</p>
            </div>
        </div>
        
        <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg flex items-center gap-5 hover:border-gray-700 transition-colors">
            <div class="w-14 h-14 shrink-0 rounded-full bg-red-500/10 text-red-400 flex items-center justify-center text-3xl">
                <i class="ph ph-user-minus"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400 mb-1">Expired / Inaktif</p>
                <p class="text-3xl font-bold text-white tracking-tight">{{ number_format($stats['inactive'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4 font-medium">Member Info</th>
                        <th class="px-6 py-4 font-medium">Tipe / Paket</th>
                        <th class="px-6 py-4 font-medium">Tanggal Expired</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($members as $member)
                        <tr class="hover:bg-dark/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-800 flex items-center justify-center border border-gray-700 overflow-hidden">
                                        @if($member->photo_path)
                                            <img src="{{ Storage::url($member->photo_path) }}" class="h-full w-full object-cover">
                                        @else
                                            <i class="ph ph-user text-xl text-gray-500"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('members.show', $member->id) }}" class="font-medium text-white hover:text-neon transition-colors">{{ $member->name }}</a>
                                        <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $member->member_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-800 text-gray-300 rounded text-xs border border-gray-700">{{ ucfirst($member->member_type) }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($member->status === 'active')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                                @elseif($member->status === 'pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Belum Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('members.show', $member->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-700 text-gray-300 hover:text-neon hover:border-neon bg-dark transition-all duration-150"
                                        title="Lihat Detail Member">
                                        <i class="ph ph-eye text-sm"></i>
                                        <span>Detail</span>
                                    </a>
                                    <a href="{{ route('members.ecard', $member->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-700 text-gray-300 hover:text-neon hover:border-neon bg-dark transition-all duration-150"
                                        title="Lihat E-Card & QR Code">
                                        <i class="ph ph-qr-code text-sm"></i>
                                        <span>E-Card</span>
                                    </a>
                                    <form method="POST" action="{{ route('members.destroy', $member->id) }}" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus member ini? Semua data transaksi dan absensi akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-700 text-gray-300 hover:text-red-400 hover:border-red-400 bg-dark transition-all duration-150" title="Hapus Member">
                                            <i class="ph ph-trash text-sm"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="ph ph-users-three text-4xl mb-2 text-gray-600 block"></i>
                                Belum ada data member.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
            <div class="px-6 py-4 border-t border-gray-800 bg-dark">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
