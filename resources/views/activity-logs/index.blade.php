<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
                <i class="ph ph-clock-counter-clockwise mr-2 text-neon"></i> {{ __('Riwayat Aktivitas Sistem') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex items-center p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-400">
            <i class="ph ph-info text-2xl mr-3"></i>
            <div>
                <p class="text-sm font-medium">Ini adalah log aktivitas tingkat sistem.</p>
                <p class="text-xs mt-1 text-blue-400/80">Semua perubahan pada entitas penting seperti Akun User, Harga Paket Gym, atau Karyawan akan dicatat di sini secara otomatis. Halaman ini hanya dapat diakses oleh Developer.</p>
            </div>
        </div>
        <div class="bg-card rounded-xl border border-gray-800 shadow-xl overflow-hidden relative z-10">
            <div class="p-4 border-b border-gray-800 bg-darker/50 flex items-center justify-between">
                <h3 class="text-white font-medium">Log Aktivitas</h3>
                
                <form method="GET" action="{{ route('activity-logs.index') }}" class="flex items-center space-x-2">
                    <select name="user_id" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-neon focus:border-neon block w-full p-2.5">
                        <option value="">Semua Akun</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->roles->first()?->name ?? 'User' }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-neon hover:bg-neon/90 text-black font-semibold rounded-lg text-sm transition-colors flex items-center">
                        <i class="ph ph-funnel mr-2"></i> Filter
                    </button>
                    @if(request()->filled('user_id'))
                        <a href="{{ route('activity-logs.index') }}" class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg text-sm transition-colors flex items-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-400 uppercase bg-darker/50 border-b border-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Waktu / Tanggal</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Pengguna</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Aksi & Modul</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Detail Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($logs as $log)
                            <tr class="hover:bg-darker/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-white font-medium">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-800 border border-gray-700 flex items-center justify-center text-gray-400 mr-3 shrink-0">
                                            {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            @if($log->user_id)
                                                <a href="{{ route('activity-logs.index', ['user_id' => $log->user_id]) }}" class="text-white font-medium hover:text-neon transition-colors" title="Filter berdasarkan aktivitas akun ini">
                                                    {{ $log->user->name ?? 'User Terhapus' }}
                                                </a>
                                            @else
                                                <div class="text-white font-medium">User Terhapus</div>
                                            @endif
                                            <div class="text-xs text-gray-500">{{ $log->user->roles->first()?->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-800 text-gray-300 border border-gray-700 mb-1">
                                        {{ $log->module }}
                                    </span>
                                    <div class="mt-1">
                                        @if($log->action == 'CREATE')
                                            <span class="inline-flex items-center text-xs font-medium text-green-400"><i class="ph ph-plus-circle mr-1"></i> Ditambahkan</span>
                                        @elseif($log->action == 'UPDATE')
                                            <span class="inline-flex items-center text-xs font-medium text-blue-400"><i class="ph ph-pencil-simple mr-1"></i> Diperbarui</span>
                                        @elseif($log->action == 'DELETE')
                                            <span class="inline-flex items-center text-xs font-medium text-red-400"><i class="ph ph-trash mr-1"></i> Dihapus</span>
                                        @else
                                            <span class="inline-flex items-center text-xs font-medium text-gray-400">{{ $log->action }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-300 max-w-md">
                                    <p class="truncate hover:whitespace-normal transition-all" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-darker flex items-center justify-center mb-4">
                                            <i class="ph ph-clock text-3xl text-gray-600"></i>
                                        </div>
                                        <h3 class="text-white font-medium text-lg mb-1">Belum Ada Aktivitas</h3>
                                        <p class="text-gray-500 max-w-sm mx-auto">Sistem belum mencatat adanya perubahan data yang penting.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-800 bg-darker/30">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
