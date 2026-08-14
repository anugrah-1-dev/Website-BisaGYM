<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-neon/10 flex items-center justify-center text-neon">
                    <i class="ph ph-ticket text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ __('Manajemen Paket Gym') }}</h2>
                    <p class="text-xs text-gray-400">Atur paket langganan dan harganya</p>
                </div>
            </div>
            <a href="{{ route('gym-packages.create') }}" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2.5 px-5 rounded-lg transition-all hover:shadow-[0_0_15px_rgba(224,255,0,0.2)] hover:-translate-y-0.5 text-sm flex items-center">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Paket
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-4 font-medium">Nama Paket</th>
                        <th class="px-6 py-4 font-medium">Kategori</th>
                        <th class="px-6 py-4 font-medium text-center">Max Member</th>
                        <th class="px-6 py-4 font-medium">Durasi</th>
                        <th class="px-6 py-4 font-medium">Harga</th>
                        <th class="px-6 py-4 font-medium">Biaya Admin</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($packages as $pkg)
                        <tr class="hover:bg-dark/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-white">{{ $pkg->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-800 text-gray-300 rounded text-xs border border-gray-700 uppercase">{{ $pkg->category }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pkg->max_members >= 2)
                                    <span class="px-3 py-1.5 text-xs rounded-full bg-pink-500/20 text-pink-400 border border-pink-500/30 flex items-center justify-center w-fit mx-auto gap-2">
                                        <i class="ph ph-users text-sm"></i> <span>{{ $pkg->max_members }} orang</span>
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 text-xs rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center w-fit mx-auto gap-2">
                                        <i class="ph ph-user text-sm"></i> <span>1 orang</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-neon font-bold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-400">
                                Rp {{ number_format($pkg->admin_fee, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pkg->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('gym-packages.edit', $pkg->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-neon hover:bg-neon/10 transition-colors" title="Edit Paket">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </a>
                                    <form method="POST" action="{{ route('gym-packages.destroy', $pkg->id) }}" class="inline" onsubmit="return confirm('Yakin hapus paket ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="ph ph-package text-4xl mb-2 block text-gray-600"></i>
                                Belum ada data paket.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
