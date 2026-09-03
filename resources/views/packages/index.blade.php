<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-neon/10 flex items-center justify-center text-neon">
                    <i class="ph ph-tags text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ __('Manajemen Paket & Harga') }}</h2>
                    <p class="text-xs text-gray-400">Atur paket member dan tarif kunjungan harian</p>
                </div>
            </div>
        </div>
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

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ request('tab', 'member') }}' }">

        {{-- Tab Nav --}}
        <div class="flex space-x-1 mb-6 bg-dark rounded-xl p-1 w-fit border border-gray-800">
            <button @click="tab = 'member'"
                :class="tab === 'member' ? 'bg-neon text-darker font-bold' : 'text-gray-400 hover:text-white'"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                <i class="ph ph-ticket text-base"></i> Paket Member
            </button>
            <button @click="tab = 'non-member'"
                :class="tab === 'non-member' ? 'bg-pink-500 text-white font-bold' : 'text-gray-400 hover:text-white'"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                <i class="ph ph-door-open text-base"></i> Tarif Kunjungan Harian
            </button>
        </div>

        {{-- ══════════════════ TAB: PAKET MEMBER ══════════════════ --}}
        <div x-show="tab === 'member'" x-transition>
            <div class="flex justify-end mb-4">
                <a href="{{ route('gym-packages.create') }}"
                    class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2.5 px-5 rounded-lg transition-all hover:shadow-[0_0_15px_rgba(224,255,0,0.2)] hover:-translate-y-0.5 text-sm flex items-center">
                    <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Paket
                </a>
            </div>

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
                            @forelse ($memberPackages as $pkg)
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
                                    <td class="px-6 py-4 text-gray-300">{{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-neon font-bold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-400">Rp {{ number_format($pkg->admin_fee, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($pkg->is_active)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('gym-packages.edit', $pkg->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-neon hover:bg-neon/10 transition-colors" title="Edit Paket">
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
                                        Belum ada paket member.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ══════════════════ TAB: KUNJUNGAN HARIAN ══════════════════ --}}
        <div x-show="tab === 'non-member'" x-transition>

            {{-- Info banner --}}
            <div class="mb-5 p-4 rounded-xl bg-pink-500/10 border border-pink-500/30 flex items-start gap-3">
                <i class="ph ph-info text-pink-400 text-xl mt-0.5 shrink-0"></i>
                <div>
                    <p class="text-pink-300 text-sm font-semibold">Tarif Kunjungan Harian (Non-Member)</p>
                    <p class="text-gray-400 text-xs mt-0.5">Tarif ini digunakan di kasir untuk pengunjung yang tidak memiliki kartu member. Durasi otomatis 1 hari. Hanya <span class="text-white font-medium">1 tarif aktif</span> yang akan digunakan saat kasir memproses pembayaran.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Form Tambah Tarif Baru --}}
                <div class="lg:col-span-2">
                    <div class="bg-card rounded-xl border border-gray-800 shadow-lg p-5">
                        <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
                            <i class="ph ph-plus-circle text-pink-400 text-lg"></i> Tambah Tarif Baru
                        </h3>
                        @if($errors->any() && old('_form') === 'non-member')
                            <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-xs">
                                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('gym-packages.store-non-member') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="_form" value="non-member">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Nama Tarif <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Cth: Tiket Masuk Harian"
                                    class="w-full border-gray-700 rounded-lg bg-dark text-white text-sm focus:ring-pink-500 focus:border-pink-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Harga (Rp) <span class="text-red-400">*</span></label>
                                <input type="number" name="price" value="{{ old('price') }}" placeholder="50000" min="0"
                                    class="w-full border-gray-700 rounded-lg bg-dark text-white text-sm focus:ring-pink-500 focus:border-pink-500" required>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" name="is_active" id="nm_is_active_new" value="1" checked
                                    class="h-4 w-4 text-pink-500 bg-dark border-gray-700 rounded focus:ring-pink-500">
                                <label for="nm_is_active_new" class="text-sm text-gray-300 cursor-pointer">Aktifkan tarif ini</label>
                            </div>
                            <div class="pt-1 border-t border-gray-800 flex gap-2 text-xs text-gray-500">
                                <i class="ph ph-clock shrink-0 mt-0.5"></i>
                                <span>Durasi otomatis <strong class="text-gray-400">1 hari</strong> — berlaku untuk 1 kunjungan saja.</span>
                            </div>
                            <button type="submit"
                                class="w-full py-2.5 bg-pink-500 hover:bg-pink-400 text-white rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                                <i class="ph ph-plus-circle"></i> Tambah Tarif
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Daftar Tarif --}}
                <div class="lg:col-span-3 space-y-3">
                    @forelse ($nonMemberPackages as $pkg)
                        <div x-data="{ editing: false }" class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
                            {{-- Display Mode --}}
                            <div x-show="!editing" class="p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg {{ $pkg->is_active ? 'bg-pink-500/20' : 'bg-gray-700/50' }} flex items-center justify-center shrink-0">
                                        <i class="ph ph-door-open text-xl {{ $pkg->is_active ? 'text-pink-400' : 'text-gray-500' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $pkg->name }}</p>
                                        <p class="text-neon font-bold text-lg">Rp {{ number_format($pkg->price, 0, ',', '.') }}</p>
                                        <p class="text-gray-500 text-xs">{{ $pkg->duration }} {{ ucfirst($pkg->duration_unit) }} &bull; 1 orang</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    @if($pkg->is_active)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400 border border-green-500/30 whitespace-nowrap">
                                            <i class="ph ph-check-circle mr-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-700 text-gray-500 border border-gray-600 whitespace-nowrap">Nonaktif</span>
                                    @endif
                                    <button @click="editing = true"
                                        class="p-2 rounded-lg bg-gray-800 text-gray-400 hover:text-blue-400 hover:bg-blue-400/10 transition-colors" title="Edit">
                                        <i class="ph ph-pencil-simple"></i>
                                    </button>
                                    <form method="POST" action="{{ route('gym-packages.destroy', $pkg->id) }}" class="inline" onsubmit="return confirm('Yakin hapus tarif ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Edit Mode (inline) --}}
                            <div x-show="editing" x-transition class="p-5 border-t border-gray-800 bg-dark/40">
                                <p class="text-xs text-gray-400 mb-3 font-medium">Edit Tarif</p>
                                <form method="POST" action="{{ route('gym-packages.update-non-member', $pkg->id) }}" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="col-span-2">
                                            <label class="block text-xs text-gray-500 mb-1">Nama Tarif</label>
                                            <input type="text" name="name" value="{{ $pkg->name }}" required
                                                class="w-full border-gray-700 rounded-lg bg-dark text-white text-sm focus:ring-pink-500 focus:border-pink-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Harga (Rp)</label>
                                            <input type="number" name="price" value="{{ $pkg->price }}" min="0" required
                                                class="w-full border-gray-700 rounded-lg bg-dark text-white text-sm focus:ring-pink-500 focus:border-pink-500">
                                        </div>
                                        <div class="flex items-end pb-1">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="is_active" value="1" {{ $pkg->is_active ? 'checked' : '' }}
                                                    class="h-4 w-4 text-pink-500 bg-dark border-gray-700 rounded focus:ring-pink-500">
                                                <span class="text-sm text-gray-300">Aktif</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 pt-1">
                                        <button type="submit"
                                            class="flex-1 py-2 bg-pink-500 hover:bg-pink-400 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                                            <i class="ph ph-floppy-disk"></i> Simpan
                                        </button>
                                        <button type="button" @click="editing = false"
                                            class="px-4 py-2 border border-gray-700 rounded-lg text-gray-400 hover:bg-gray-800 text-sm transition-colors">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-card rounded-xl border border-dashed border-gray-700 p-10 text-center">
                            <i class="ph ph-door-open text-5xl text-gray-600 mb-3 block"></i>
                            <p class="text-gray-400 font-medium text-sm">Belum ada tarif kunjungan harian</p>
                            <p class="text-gray-600 text-xs mt-1">Tambahkan tarif menggunakan form di sebelah kiri</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

    </div>

</x-app-layout>
