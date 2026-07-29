<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-neon/10 flex items-center justify-center text-neon">
                    <i class="ph ph-identification-badge text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ __('Manajemen Karyawan') }}</h2>
                    <p class="text-xs text-gray-400">Atur profil, shift, dan gaji karyawan</p>
                </div>
            </div>
            <a href="{{ route('employees.create') }}" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2.5 px-5 rounded-lg transition-all hover:shadow-[0_0_15px_rgba(224,255,0,0.2)] hover:-translate-y-0.5 text-sm flex items-center">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Karyawan
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
                        <th class="px-6 py-4 font-medium">Nama & Posisi</th>
                        <th class="px-6 py-4 font-medium">Kontak</th>
                        <th class="px-6 py-4 font-medium">Gaji Pokok</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-dark/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 border border-gray-700">
                                        <i class="ph ph-user text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-white">{{ $emp->name }}</p>
                                        <p class="text-xs text-neon">{{ $emp->position ?? 'Karyawan' }}</p>
                                        @if($emp->user)
                                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full"><i class="ph ph-key"></i> Memiliki Akses Login</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-300"><i class="ph ph-whatsapp text-green-400 mr-1"></i> {{ $emp->phone ?? '-' }}</p>
                                @if($emp->user)
                                    <p class="text-gray-400 text-xs mt-1"><i class="ph ph-envelope text-gray-500 mr-1"></i> {{ $emp->user->email }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-white font-medium">
                                Rp {{ number_format($emp->base_salary, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($emp->status === 'active')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('employees.shifts.index', $emp->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-blue-400 hover:text-white hover:bg-blue-500 transition-colors" title="Jadwal Shift">
                                        <i class="ph ph-calendar-blank text-lg"></i>
                                    </a>
                                    <a href="{{ route('employees.payrolls.index', $emp->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-green-400 hover:text-white hover:bg-green-500 transition-colors" title="Penggajian">
                                        <i class="ph ph-money text-lg"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $emp->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-neon hover:bg-neon/10 transition-colors" title="Edit Profil">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </a>
                                    <form method="POST" action="{{ route('employees.destroy', $emp->id) }}" class="inline" onsubmit="return confirm('Yakin hapus data karyawan ini?')">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="ph ph-users text-4xl mb-2 block text-gray-600"></i>
                                Belum ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
