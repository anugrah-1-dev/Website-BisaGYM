<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-neon/10 border border-neon/20 flex items-center justify-center text-neon shadow-[0_0_15px_rgba(224,255,0,0.15)]">
                    <i class="ph ph-package text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ __('Inventaris & Stok Snack') }}</h2>
                    <p class="text-xs text-gray-400">Manajemen Stok Gudang, Stok Kulkas POS, dan Laporan Riwayat Restok</p>
                </div>
            </div>

            <!-- Header Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openIncomingModal()" class="bg-dark hover:bg-gray-800 text-neon border border-neon/30 hover:border-neon font-semibold py-2 px-3.5 rounded-xl transition-all text-xs flex items-center shadow-md active:scale-95">
                    <i class="ph ph-plus-circle text-base mr-1.5"></i> + Barang Masuk (Supplier)
                </button>
                <button type="button" onclick="openRefillModal()" class="bg-dark hover:bg-gray-800 text-cyan-400 border border-cyan-500/30 hover:border-cyan-400 font-semibold py-2 px-3.5 rounded-xl transition-all text-xs flex items-center shadow-md active:scale-95">
                    <i class="ph ph-arrows-down-up text-base mr-1.5"></i> ❄️ Refill Ke Kulkas
                </button>
                <a href="{{ route('snacks.create') }}" class="bg-neon hover:bg-[#c4e600] text-darker font-bold py-2 px-4 rounded-xl transition-all hover:shadow-[0_0_15px_rgba(224,255,0,0.2)] text-xs flex items-center active:scale-95">
                    <i class="ph ph-sparkle text-base mr-1.5"></i> Tambah Produk Baru
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Success & Error Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/40 text-green-400 flex items-center justify-between shadow-lg shadow-green-500/5">
            <div class="flex items-center">
                <i class="ph ph-check-circle text-2xl mr-3 text-green-400"></i>
                <span class="font-medium text-sm md:text-base">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-400/60 hover:text-green-400"><i class="ph ph-x text-lg"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/40 text-red-400 text-sm space-y-1 shadow-lg shadow-red-500/5">
            @foreach($errors->all() as $e)
                <div class="flex items-center gap-2"><i class="ph ph-warning-circle text-base"></i> <p>{{ $e }}</p></div>
            @endforeach
        </div>
    @endif

    <div x-data="{ activeTab: 'stok' }" class="space-y-6">

        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-card rounded-2xl border border-gray-800 p-5 shadow-lg flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Jenis Produk</p>
                    <p class="text-2xl font-black text-white mt-1 font-mono">{{ number_format($totalProductTypes) }} <span class="text-xs text-gray-500 font-normal">Varian</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gray-800/80 border border-gray-700 flex items-center justify-center text-gray-300 text-2xl">
                    <i class="ph ph-squares-four"></i>
                </div>
            </div>

            <div class="bg-card rounded-2xl border border-amber-500/20 p-5 shadow-lg flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Total Stok Gudang</p>
                    <p class="text-2xl font-black text-amber-400 mt-1 font-mono">{{ number_format($totalGudang) }} <span class="text-xs text-gray-400 font-normal">Pcs</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl">
                    <i class="ph ph-warehouse"></i>
                </div>
            </div>

            <div class="bg-card rounded-2xl border border-cyan-500/20 p-5 shadow-lg flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-cyan-400 uppercase tracking-wider">Total Stok Di Kulkas</p>
                    <p class="text-2xl font-black text-cyan-400 mt-1 font-mono">{{ number_format($totalKulkas) }} <span class="text-xs text-gray-400 font-normal">Pcs</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-2xl">
                    <i class="ph ph-thermometer-cold"></i>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-gray-800 pb-1">
            <button type="button" @click="activeTab = 'stok'"
                :class="activeTab === 'stok' ? 'border-neon text-neon font-bold bg-neon/10' : 'border-transparent text-gray-400 hover:text-white hover:bg-gray-800/40'"
                class="px-4 py-2.5 rounded-xl border text-sm transition-all flex items-center gap-2 font-medium">
                <i class="ph ph-package text-lg"></i>
                <span>Daftar Stok Produk (Gudang & Kulkas)</span>
            </button>
            <button type="button" @click="activeTab = 'laporan'"
                :class="activeTab === 'laporan' ? 'border-neon text-neon font-bold bg-neon/10' : 'border-transparent text-gray-400 hover:text-white hover:bg-gray-800/40'"
                class="px-4 py-2.5 rounded-xl border text-sm transition-all flex items-center gap-2 font-medium">
                <i class="ph ph-receipt text-lg"></i>
                <span>Laporan Barang Masuk & Restok</span>
                <span class="bg-gray-800 text-gray-300 text-xs px-2 py-0.5 rounded-full font-mono">{{ $restocks->total() }}</span>
            </button>
        </div>

        <!-- TAB 1: DAFTAR STOK PRODUK (GUDANG & KULKAS) -->
        <div x-show="activeTab === 'stok'" class="bg-card rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
            <div class="p-4 border-b border-gray-800 bg-dark/40 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <i class="ph ph-list-bullets text-neon text-lg"></i>
                    <h3 class="text-white text-sm font-semibold">Monitoring Stok Fisik Gudang vs Kulkas Display</h3>
                </div>
                <span class="text-xs text-gray-400 bg-gray-900 px-3 py-1 rounded-lg border border-gray-800 font-mono">
                    Total fisik: {{ number_format($totalGudang + $totalKulkas) }} Pcs
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark/80 border-b border-gray-800 text-[11px] uppercase tracking-wider text-gray-400">
                            <th class="px-6 py-4 font-semibold">Produk & Kode</th>
                            <th class="px-6 py-4 font-semibold">Kategori</th>
                            <th class="px-6 py-4 font-semibold text-center">Stok Gudang</th>
                            <th class="px-6 py-4 font-semibold text-center">Stok Kulkas (POS)</th>
                            <th class="px-6 py-4 font-semibold text-center">Total Stok</th>
                            <th class="px-6 py-4 font-semibold">Harga Modal</th>
                            <th class="px-6 py-4 font-semibold">Harga Jual</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi & Restok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/80 text-sm">
                        @forelse ($snacks as $snack)
                            <tr class="hover:bg-dark/40 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-white group-hover:text-neon transition-colors">{{ $snack->name }}</p>
                                    <p class="text-xs font-mono text-gray-500">{{ $snack->snack_code }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-xs bg-gray-900 border border-gray-800 rounded-lg text-gray-300 font-medium">
                                        @if($snack->category === 'Minuman') 🥤 Minuman
                                        @elseif($snack->category === 'Makanan') 🍫 Makanan
                                        @elseif($snack->category === 'Suplemen') 💊 Suplemen
                                        @else 📦 {{ $snack->category }} @endif
                                    </span>
                                </td>
                                
                                <!-- Stok Gudang -->
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="font-mono font-bold text-amber-400 px-3 py-1 rounded-xl bg-amber-500/10 border border-amber-500/20 min-w-[50px] text-center">
                                            {{ $snack->stock_gudang }}
                                        </span>
                                        @if($snack->stock_gudang == 0)
                                            <span class="text-[10px] text-gray-500 mt-1">Kosong</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Stok Kulkas -->
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="font-mono font-bold {{ $snack->stock_kulkas <= 3 ? 'text-red-400 bg-red-500/10 border-red-500/30' : 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20' }} px-3 py-1 rounded-xl border min-w-[50px] text-center">
                                            {{ $snack->stock_kulkas }}
                                        </span>
                                        @if($snack->stock_kulkas <= 3)
                                            <span class="text-[10px] text-red-400 mt-1 font-semibold flex items-center gap-1">
                                                <i class="ph ph-warning-circle"></i> Menipis
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Total Stok -->
                                <td class="px-6 py-4 text-center font-mono font-bold text-white">
                                    {{ $snack->stock_gudang + $snack->stock_kulkas }}
                                </td>

                                <td class="px-6 py-4 text-gray-400 font-mono text-xs">Rp {{ number_format($snack->capital_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-neon font-bold font-mono text-xs">Rp {{ number_format($snack->selling_price, 0, ',', '.') }}</td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <!-- Button Refill Ke Kulkas -->
                                        <button type="button" onclick="openRefillModal({{ $snack->id }}, '{{ addslashes($snack->name) }}', {{ $snack->stock_gudang }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 text-xs font-semibold transition-colors" title="Isi ulang kulkas dari gudang">
                                            <i class="ph ph-arrows-down-up"></i> Refill
                                        </button>

                                        <!-- Button Restok Supplier -->
                                        <button type="button" onclick="openIncomingModal({{ $snack->id }}, '{{ addslashes($snack->name) }}', {{ $snack->capital_price }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-neon/10 hover:bg-neon/20 text-neon border border-neon/30 text-xs font-semibold transition-colors" title="Tambah barang masuk dari supplier">
                                            <i class="ph ph-plus"></i> + Restok
                                        </button>

                                        <!-- Edit Product -->
                                        <a href="{{ route('snacks.edit', $snack->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors" title="Edit Detail Produk">
                                            <i class="ph ph-pencil-simple text-base"></i>
                                        </a>

                                        <!-- Delete Product -->
                                        <form method="POST" action="{{ route('snacks.destroy', $snack->id) }}" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-500/20 transition-colors" title="Hapus Produk">
                                                <i class="ph ph-trash text-base"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="ph ph-package text-4xl block mb-2 text-gray-600"></i>
                                    Belum ada data produk snack. Silakan klik tombol "Tambah Produk Baru".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: LAPORAN BARANG MASUK & RESTOK -->
        <div x-show="activeTab === 'laporan'" class="bg-card rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
            <div class="p-4 border-b border-gray-800 bg-dark/40 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ph ph-clock-counter-clockwise text-neon text-lg"></i>
                    <h3 class="text-white text-sm font-semibold">Laporan Riwayat Barang Masuk & Transfer Kulkas</h3>
                </div>
                <span class="text-xs text-gray-400 italic">Otomatis mencatat pengeluaran operasional & mutasi stok</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark/80 border-b border-gray-800 text-[11px] uppercase tracking-wider text-gray-400">
                            <th class="px-6 py-4 font-semibold">Waktu / Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Tipe Aktivitas</th>
                            <th class="px-6 py-4 font-semibold">Produk</th>
                            <th class="px-6 py-4 font-semibold text-center">Jumlah (Qty)</th>
                            <th class="px-6 py-4 font-semibold">Harga Modal Unit</th>
                            <th class="px-6 py-4 font-semibold">Total Biaya Beli</th>
                            <th class="px-6 py-4 font-semibold">Supplier / Catatan</th>
                            <th class="px-6 py-4 font-semibold">Operator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/80 text-sm">
                        @forelse ($restocks as $r)
                            <tr class="hover:bg-dark/40 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-gray-300 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($r->restock_date)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($r->type === 'incoming_supplier')
                                        <span class="px-2.5 py-1 text-xs font-semibold bg-green-500/10 text-green-400 border border-green-500/30 rounded-lg inline-flex items-center gap-1">
                                            <i class="ph ph-arrow-down-left"></i> Barang Masuk (Supplier)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 rounded-lg inline-flex items-center gap-1">
                                            <i class="ph ph-arrows-down-up"></i> Refill Ke Kulkas
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-white">{{ $r->snack->name ?? '-' }}</p>
                                    <p class="text-xs font-mono text-gray-500">{{ $r->snack->snack_code ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-bold">
                                    @if($r->type === 'incoming_supplier')
                                        @if($r->qty_gudang > 0)
                                            <span class="text-amber-400">+{{ $r->qty_gudang }} Gudang</span>
                                        @else
                                            <span class="text-cyan-400">+{{ $r->qty_kulkas }} Kulkas</span>
                                        @endif
                                    @else
                                        <span class="text-cyan-400 font-semibold">{{ $r->qty_kulkas }} Pcs <span class="text-[10px] text-gray-400 font-normal">(Gudang ➔ Kulkas)</span></span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-400">
                                    @if($r->type === 'incoming_supplier')
                                        Rp {{ number_format($r->capital_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    @if($r->type === 'incoming_supplier')
                                        <span class="text-neon font-bold">Rp {{ number_format($r->total_cost, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-300">
                                    @if($r->supplier)
                                        <p class="font-semibold text-white"><i class="ph ph-storefront text-gray-400 mr-1"></i> {{ $r->supplier }}</p>
                                    @endif
                                    @if($r->notes)
                                        <p class="text-gray-400 italic text-[11px]">{{ $r->notes }}</p>
                                    @endif
                                    @if(!$r->supplier && !$r->notes)
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                                    <span class="bg-gray-900 px-2 py-1 rounded border border-gray-800 text-gray-300 font-medium">
                                        <i class="ph ph-user text-gray-400 mr-1"></i> {{ $r->user->name ?? 'System' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="ph ph-receipt text-4xl block mb-2 text-gray-600"></i>
                                    Belum ada riwayat barang masuk / restok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($restocks->hasPages())
                <div class="p-4 border-t border-gray-800 bg-dark/20">
                    {{ $restocks->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL 1: INPUT BARANG MASUK (SUPPLIER) -->
    <div id="incomingModal" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-card border border-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                <h3 class="text-white font-bold text-base flex items-center gap-2">
                    <i class="ph ph-plus-circle text-neon text-xl"></i> Input Barang Masuk (Restok Supplier)
                </h3>
                <button onclick="closeIncomingModal()" class="text-gray-400 hover:text-white"><i class="ph ph-x text-xl"></i></button>
            </div>

            <form method="POST" action="{{ route('snacks.incoming') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Pilih Produk Snack</label>
                    <select id="incoming_snack_id" name="snack_id" required onchange="onIncomingSnackChange(this)"
                        class="w-full bg-dark border-gray-700 rounded-xl text-white text-sm focus:border-neon focus:ring-neon">
                        <option value="" disabled selected>-- Pilih Produk --</option>
                        @foreach($snacks as $s)
                            <option value="{{ $s->id }}" data-price="{{ $s->capital_price }}">
                                {{ $s->name }} ({{ $s->snack_code }}) - Gudang: {{ $s->stock_gudang }} | Kulkas: {{ $s->stock_kulkas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Tujuan Penyimpanan</label>
                        <select name="destination" required class="w-full bg-dark border-gray-700 rounded-xl text-white text-sm focus:border-neon focus:ring-neon">
                            <option value="gudang" selected>📦 Gudang Utama</option>
                            <option value="kulkas">❄️ Kulkas Display POS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Jumlah Masuk (Pcs)</label>
                        <input type="number" id="incoming_quantity" name="quantity" min="1" value="1" required oninput="calcIncomingTotal()"
                            class="w-full bg-dark border-gray-700 rounded-xl text-white text-sm font-mono font-bold focus:border-neon focus:ring-neon">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Harga Beli Modal / Unit (Rp)</label>
                        <input type="number" id="incoming_capital_price" name="capital_price" min="0" required oninput="calcIncomingTotal()"
                            class="w-full bg-dark border-gray-700 rounded-xl text-white text-sm font-mono focus:border-neon focus:ring-neon">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Tanggal Masuk</label>
                        <input type="datetime-local" name="restock_date" value="{{ date('Y-m-d\TH:i') }}" required
                            class="w-full bg-dark border-gray-700 rounded-xl text-white text-xs focus:border-neon focus:ring-neon">
                    </div>
                </div>

                <!-- Total Display Box -->
                <div class="bg-dark rounded-xl p-3 border border-gray-800 flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-semibold">Total Biaya Pembelian:</span>
                    <span id="incoming_total_display" class="text-lg font-black text-neon font-mono">Rp 0</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Nama Supplier (Opsional)</label>
                        <input type="text" name="supplier" placeholder="Contoh: Toko Grosir Jaya"
                            class="w-full bg-dark border-gray-700 rounded-xl text-white text-xs focus:border-neon focus:ring-neon">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Catatan Nota (Opsional)</label>
                        <input type="text" name="notes" placeholder="No Nota / Keterangan"
                            class="w-full bg-dark border-gray-700 rounded-xl text-white text-xs focus:border-neon focus:ring-neon">
                    </div>
                </div>

                <div class="p-3 bg-neon/5 border border-neon/20 rounded-xl text-[11px] text-gray-300 flex items-center gap-2">
                    <i class="ph ph-info text-neon text-base"></i>
                    <span>Total biaya pembelian akan <strong>otomatis tercatat</strong> ke Laporan Pengeluaran Operasional.</span>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeIncomingModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-neon hover:bg-[#c4e600] text-darker font-bold rounded-xl text-xs flex items-center gap-1 shadow-lg shadow-neon/20">
                        <i class="ph ph-floppy-disk"></i> Simpan Barang Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: REFILL KE KULKAS (GUDANG -> KULKAS) -->
    <div id="refillModal" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-card border border-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                <h3 class="text-white font-bold text-base flex items-center gap-2">
                    <i class="ph ph-arrows-down-up text-cyan-400 text-xl"></i> Refill Stok Ke Kulkas
                </h3>
                <button onclick="closeRefillModal()" class="text-gray-400 hover:text-white"><i class="ph ph-x text-xl"></i></button>
            </div>

            <form method="POST" action="{{ route('snacks.refill-kulkas') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Pilih Produk Snack</label>
                    <select id="refill_snack_id" name="snack_id" required onchange="onRefillSnackChange(this)"
                        class="w-full bg-dark border-gray-700 rounded-xl text-white text-sm focus:border-cyan-400 focus:ring-cyan-400">
                        <option value="" disabled selected>-- Pilih Produk --</option>
                        @foreach($snacks as $s)
                            <option value="{{ $s->id }}" data-gudang="{{ $s->stock_gudang }}">
                                {{ $s->name }} (Stok Gudang Tersisa: {{ $s->stock_gudang }} Pcs)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Jumlah Pindah Ke Kulkas (Pcs)</label>
                    <input type="number" id="refill_quantity" name="quantity" min="1" value="1" required
                        class="w-full bg-dark border-gray-700 rounded-xl text-white text-sm font-mono font-bold focus:border-cyan-400 focus:ring-cyan-400">
                    <p id="refill_gudang_info" class="text-[11px] text-amber-400 mt-1 italic"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Catatan Refill (Opsional)</label>
                    <input type="text" name="notes" placeholder="Contoh: Isi ulang kulkas pagi"
                        class="w-full bg-dark border-gray-700 rounded-xl text-white text-xs focus:border-cyan-400 focus:ring-cyan-400">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRefillModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-darker font-bold rounded-xl text-xs flex items-center gap-1 shadow-lg shadow-cyan-500/20">
                        <i class="ph ph-check-bold"></i> Pindahkan Ke Kulkas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Helper Scripts -->
    <script>
        function openIncomingModal(snackId = null, name = '', capitalPrice = 0) {
            const modal = document.getElementById('incomingModal');
            modal.classList.remove('hidden');
            if (snackId) {
                const select = document.getElementById('incoming_snack_id');
                select.value = snackId;
                document.getElementById('incoming_capital_price').value = capitalPrice;
                calcIncomingTotal();
            }
        }

        function closeIncomingModal() {
            document.getElementById('incomingModal').classList.add('hidden');
        }

        function onIncomingSnackChange(select) {
            const selectedOpt = select.options[select.selectedIndex];
            const price = selectedOpt.getAttribute('data-price') || 0;
            document.getElementById('incoming_capital_price').value = price;
            calcIncomingTotal();
        }

        function calcIncomingTotal() {
            const qty = parseInt(document.getElementById('incoming_quantity').value) || 0;
            const price = parseFloat(document.getElementById('incoming_capital_price').value) || 0;
            const total = qty * price;
            document.getElementById('incoming_total_display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function openRefillModal(snackId = null, name = '', stockGudang = 0) {
            const modal = document.getElementById('refillModal');
            modal.classList.remove('hidden');
            if (snackId) {
                const select = document.getElementById('refill_snack_id');
                select.value = snackId;
                document.getElementById('refill_gudang_info').innerText = 'Stok tersedia di gudang: ' + stockGudang + ' Pcs';
            }
        }

        function closeRefillModal() {
            document.getElementById('refillModal').classList.add('hidden');
        }

        function onRefillSnackChange(select) {
            const selectedOpt = select.options[select.selectedIndex];
            const gudang = selectedOpt.getAttribute('data-gudang') || 0;
            document.getElementById('refill_gudang_info').innerText = 'Stok tersedia di gudang: ' + gudang + ' Pcs';
        }
    </script>
</x-app-layout>
