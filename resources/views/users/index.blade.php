<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <i class="ph ph-users-three mr-2 text-neon"></i> {{ __('Manajemen User Login') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 flex items-center">
            <i class="ph ph-warning-circle text-xl mr-3"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-800 bg-dark/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-white">Daftar Pengguna Sistem</h3>
                <p class="text-sm text-gray-400 mt-1">Kelola akses Admin dan Developer di sini.</p>
            </div>
            <a href="{{ route('users.create') }}" class="w-full md:w-auto px-5 py-2.5 bg-neon hover:bg-[#c4e600] text-darker rounded-xl font-bold transition-all duration-200 shadow-lg shadow-neon/20 flex items-center justify-center text-sm">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah User Baru
            </a>
        </div>
        
        <div class="overflow-x-auto p-2">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-gray-400 border-b border-gray-800">
                        <th class="px-6 py-4 font-semibold">Nama Pengguna</th>
                        <th class="px-6 py-4 font-semibold">Email Login</th>
                        <th class="px-6 py-4 font-semibold">Role Akses</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($users as $user)
                        <tr class="hover:bg-dark/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 mr-4 border border-gray-700 group-hover:border-neon/50 transition-colors">
                                        <i class="ph ph-user text-xl"></i>
                                    </div>
                                    <div class="text-white font-medium">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                <div class="flex items-center">
                                    <i class="ph ph-envelope-simple text-gray-500 mr-2"></i>
                                    {{ $user->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    @php
                                        $color = match($role->name) {
                                            'admin' => 'bg-red-500/10 text-red-400 border-red-500/30',
                                            'developer' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                            default => 'bg-gray-500/10 text-gray-400 border-gray-500/30'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full border {{ $color }} text-xs font-semibold tracking-wide uppercase">
                                        <i class="ph ph-shield-check mr-1.5"></i> {{ $role->name }}
                                    </span>
                                @endforeach

                                @if($user->is_location_restricted)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/30 ml-2" title="Wajib Login di Radius {{ $user->allowed_radius_meters ?? 500 }} Meter">
                                        <i class="ph ph-map-pin mr-1"></i> GPS {{ $user->allowed_radius_meters ?? 500 }}m
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-500/10 text-blue-400 rounded-lg hover:bg-blue-500/20 hover:text-blue-300 transition-colors border border-blue-500/20">
                                        <i class="ph ph-pencil-simple mr-1.5"></i> Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Mereka tidak akan bisa login lagi.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg hover:bg-red-500/20 hover:text-red-300 transition-colors border border-red-500/20">
                                            <i class="ph ph-trash mr-1.5"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                <div class="w-20 h-20 mx-auto bg-gray-800/50 rounded-full flex items-center justify-center mb-4">
                                    <i class="ph ph-users text-4xl text-gray-400"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-400">Belum ada data pengguna tingkat atas.</p>
                                <p class="text-sm mt-1">Silakan tambahkan user baru untuk memberikan akses Admin/Developer.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
