<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight flex items-center gap-2">
                <i class="ph ph-cash-register text-neon text-2xl"></i> {{ __('POS Kasir Snack') }}
            </h2>
            <span class="text-xs text-gray-400 font-mono bg-dark px-3 py-1.5 rounded-lg border border-gray-800 flex items-center gap-1.5">
                <i class="ph ph-clock text-neon"></i> {{ date('d M Y') }}
            </span>
        </div>
    </x-slot>

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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="pos()">
        
        <!-- Left Side: Product Gallery & Filters (7 columns on lg, 8 on xl) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">
            <div class="bg-card rounded-2xl border border-gray-800 p-5 md:p-6 shadow-xl">
                
                <!-- Search & Category Header -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-stretch sm:items-center mb-6">
                    <!-- Categories Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                        <button type="button" @click="selectedCategory = 'all'"
                            :class="selectedCategory === 'all' ? 'bg-neon text-darker font-bold shadow-md shadow-neon/20' : 'bg-dark hover:bg-gray-800 text-gray-400 hover:text-white border border-gray-800'"
                            class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                            <i class="ph ph-squares-four"></i> Semua
                        </button>
                        <button type="button" @click="selectedCategory = 'minuman'"
                            :class="selectedCategory === 'minuman' ? 'bg-neon text-darker font-bold shadow-md shadow-neon/20' : 'bg-dark hover:bg-gray-800 text-gray-400 hover:text-white border border-gray-800'"
                            class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                            🥤 Minuman
                        </button>
                        <button type="button" @click="selectedCategory = 'makanan'"
                            :class="selectedCategory === 'makanan' ? 'bg-neon text-darker font-bold shadow-md shadow-neon/20' : 'bg-dark hover:bg-gray-800 text-gray-400 hover:text-white border border-gray-800'"
                            class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                            🍫 Makanan
                        </button>
                        <button type="button" @click="selectedCategory = 'suplemen'"
                            :class="selectedCategory === 'suplemen' ? 'bg-neon text-darker font-bold shadow-md shadow-neon/20' : 'bg-dark hover:bg-gray-800 text-gray-400 hover:text-white border border-gray-800'"
                            class="px-3.5 py-2 rounded-xl text-xs transition-all whitespace-nowrap flex items-center gap-1.5">
                            💊 Suplemen
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative min-w-[200px]">
                        <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" x-model="search" placeholder="Cari produk..." 
                            class="w-full pl-9 pr-4 py-2 bg-dark border border-gray-800 rounded-xl text-white text-xs placeholder-gray-500 focus:outline-none focus:border-neon focus:ring-1 focus:ring-neon transition-all">
                    </div>
                </div>
                
                <!-- Product Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 max-h-[calc(100vh-280px)] overflow-y-auto pr-1">
                    @foreach($snacks as $snack)
                        <button type="button" 
                            @click="addItem({{ $snack->id }}, '{{ addslashes($snack->name) }}', {{ $snack->selling_price }}, {{ $snack->stock_kulkas }})"
                            x-show="(selectedCategory === 'all' || '{{ strtolower($snack->category) }}' === selectedCategory) && (search === '' || '{{ strtolower($snack->name) }}'.includes(search.toLowerCase()))"
                            class="bg-dark hover:bg-gray-800/80 border border-gray-800/80 hover:border-neon/60 rounded-2xl p-4 text-center transition-all duration-200 group flex flex-col items-center justify-between min-h-[170px] shadow-md relative overflow-hidden active:scale-95">
                            
                            <!-- Category Badge Icon -->
                            <div class="w-14 h-14 rounded-2xl bg-gray-800/60 group-hover:bg-neon/10 group-hover:scale-105 flex items-center justify-center text-3xl mb-3 transition-all duration-300 shadow-inner">
                                @if($snack->category === 'Minuman') 🥤
                                @elseif($snack->category === 'Makanan') 🍫
                                @elseif($snack->category === 'Suplemen') 💊
                                @else 📦 @endif
                            </div>
                            
                            <div class="w-full flex flex-col justify-end">
                                <p class="text-xs font-semibold text-white leading-snug mb-2 group-hover:text-neon transition-colors line-clamp-2">{{ $snack->name }}</p>
                                <div class="flex items-center justify-between w-full pt-2 border-t border-gray-800/60 text-xs">
                                    <span class="text-[10px] text-gray-400 font-mono bg-gray-900 px-2 py-0.5 rounded border border-gray-800">Stok Etalase/Kulkas: {{ $snack->stock_kulkas }}</span>
                                    <span class="text-neon font-bold font-mono">Rp{{ number_format($snack->selling_price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Plus Overlay Badge -->
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-neon text-darker text-xs w-6 h-6 rounded-full flex items-center justify-center font-bold shadow-md">
                                <i class="ph ph-plus-bold"></i>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side: Cart & Checkout Panel (5 columns on lg, 4 on xl) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-card rounded-2xl border border-gray-800 shadow-xl flex flex-col h-full">
                
                <!-- Cart Header -->
                <div class="p-5 border-b border-gray-800 flex items-center justify-between bg-dark/40 rounded-t-2xl">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-shopping-bag text-neon text-xl"></i>
                        <h3 class="text-white font-semibold text-sm">Keranjang Belanja</h3>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-800 font-mono" x-text="cart.reduce((sum, item) => sum + item.qty, 0) + ' item'"></span>
                </div>

                <!-- Cart Items List -->
                <div class="p-4 flex-1 space-y-2.5 overflow-y-auto max-h-[240px] min-h-[140px]">
                    <template x-if="cart.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-center text-gray-500 py-10">
                            <div class="w-14 h-14 rounded-full bg-dark flex items-center justify-center mb-3 border border-gray-800 text-gray-600">
                                <i class="ph ph-shopping-cart text-2xl"></i>
                            </div>
                            <p class="text-xs font-medium text-gray-400 mb-1">Keranjang masih kosong</p>
                            <p class="text-[11px] text-gray-600">Pilih produk di sebelah kiri untuk menambahkan</p>
                        </div>
                    </template>

                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="flex items-center justify-between gap-3 bg-dark/80 rounded-xl p-3 border border-gray-800/80 hover:border-gray-700 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-white truncate" x-text="item.name"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[11px] text-gray-400 font-mono" x-text="'@ Rp ' + item.price.toLocaleString('id-ID')"></span>
                                    <span class="text-neon font-bold text-xs font-mono" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-gray-900 rounded-lg p-1 border border-gray-800">
                                <button @click="decrease(idx)" class="w-6 h-6 rounded bg-gray-800 hover:bg-red-500/20 hover:text-red-400 text-gray-300 text-xs flex items-center justify-center transition-colors">
                                    <i class="ph ph-minus"></i>
                                </button>
                                <span class="text-white text-xs font-bold font-mono w-5 text-center" x-text="item.qty"></span>
                                <button @click="increase(idx)" class="w-6 h-6 rounded bg-gray-800 hover:bg-neon/20 hover:text-neon text-gray-300 text-xs flex items-center justify-center transition-colors">
                                    <i class="ph ph-plus"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Checkout & Calculator Section -->
                <div class="p-5 border-t border-gray-800 bg-dark/30 space-y-4 rounded-b-2xl">
                    
                    <!-- Total Display Box -->
                    <div class="bg-dark rounded-xl p-4 border border-gray-800 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Total Tagihan</span>
                            <span class="text-2xl font-black text-neon font-mono" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                        </div>
                        <button type="button" @click="cart = []; cashGiven = 0;" x-show="cart.length > 0"
                            class="text-xs text-gray-400 hover:text-red-400 bg-gray-900 px-2.5 py-1.5 rounded-lg border border-gray-800 transition-colors flex items-center gap-1">
                            <i class="ph ph-trash"></i> Reset
                        </button>
                    </div>

                    <form method="POST" action="{{ route('pos.checkout') }}" @submit.prevent="submitForm($event)">
                        @csrf
                        
                        <!-- Payment Method Toggle -->
                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px;" class="sm:grid-cols-4">
                                <label class="cursor-pointer border rounded-xl p-2.5 text-center transition-all flex items-center justify-center space-x-2 text-xs"
                                    :class="paymentMethod === 'cash' ? 'border-neon bg-neon/10 text-neon font-bold shadow-[0_0_10px_rgba(224,255,0,0.1)]' : 'border-gray-800 bg-dark text-gray-400 hover:border-gray-700'">
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="hidden">
                                    <i class="ph ph-money text-base"></i>
                                    <span>Tunai</span>
                                </label>
                                <label class="cursor-pointer border rounded-xl p-2.5 text-center transition-all flex items-center justify-center space-x-2 text-xs"
                                    :class="paymentMethod === 'transfer' ? 'border-neon bg-neon/10 text-neon font-bold shadow-[0_0_10px_rgba(224,255,0,0.1)]' : 'border-gray-800 bg-dark text-gray-400 hover:border-gray-700'">
                                    <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="hidden">
                                    <i class="ph ph-bank text-base"></i>
                                    <span>Transfer</span>
                                </label>
                                <label class="cursor-pointer border rounded-xl p-2.5 text-center transition-all flex items-center justify-center space-x-2 text-xs"
                                    :class="paymentMethod === 'qris' ? 'border-neon bg-neon/10 text-neon font-bold shadow-[0_0_10px_rgba(224,255,0,0.1)]' : 'border-gray-800 bg-dark text-gray-400 hover:border-gray-700'">
                                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="hidden">
                                    <i class="ph ph-qr-code text-base"></i>
                                    <span>QRIS</span>
                                </label>
                                <label class="cursor-pointer border rounded-xl p-2.5 text-center transition-all flex items-center justify-center space-x-2 text-xs"
                                    :class="paymentMethod === 'debit' ? 'border-neon bg-neon/10 text-neon font-bold shadow-[0_0_10px_rgba(224,255,0,0.1)]' : 'border-gray-800 bg-dark text-gray-400 hover:border-gray-700'">
                                    <input type="radio" name="payment_method" value="debit" x-model="paymentMethod" class="hidden">
                                    <i class="ph ph-credit-card text-base"></i>
                                    <span>Debit</span>
                                </label>
                            </div>
                        </div>

                        <!-- Cash Calculator Panel -->
                        <div x-show="paymentMethod === 'cash'" x-transition class="bg-dark rounded-xl p-4 border border-gray-800 mb-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-300 flex items-center gap-1.5">
                                    <i class="ph ph-calculator text-neon"></i> Uang Tunai Konsumen
                                </span>
                                <button type="button" @click="showKeypad = !showKeypad" 
                                    class="text-[11px] text-neon hover:underline flex items-center gap-1 font-medium bg-neon/10 px-2.5 py-1 rounded-lg border border-neon/20">
                                    <i class="ph ph-keypad"></i> <span x-text="showKeypad ? 'Tutup Keypad' : 'Keypad Digital'"></span>
                                </button>
                            </div>

                            <!-- Input Cash -->
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 text-xs font-bold font-mono">Rp</span>
                                <input type="number" min="0" step="1000" x-model.number="cashGiven" placeholder="0"
                                    class="w-full pl-10 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-xl text-white font-mono text-sm font-bold focus:ring-1 focus:ring-neon focus:border-neon">
                            </div>

                            <!-- Quick Preset Chips (Fixed 3 columns inline style) -->
                            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px;">
                                <button type="button" @click="setCash(total)" class="bg-gray-900 hover:bg-neon hover:text-darker text-neon font-bold text-xs py-2 rounded-lg border border-neon/30 transition-all text-center">
                                    Pas
                                </button>
                                <button type="button" @click="setCash(10000)" class="bg-gray-900 hover:bg-gray-800 text-gray-200 text-xs py-2 rounded-lg border border-gray-800 transition-colors text-center">
                                    10rb
                                </button>
                                <button type="button" @click="setCash(20000)" class="bg-gray-900 hover:bg-gray-800 text-gray-200 text-xs py-2 rounded-lg border border-gray-800 transition-colors text-center">
                                    20rb
                                </button>
                                <button type="button" @click="setCash(50000)" class="bg-gray-900 hover:bg-gray-800 text-gray-200 text-xs py-2 rounded-lg border border-gray-800 transition-colors text-center">
                                    50rb
                                </button>
                                <button type="button" @click="setCash(100000)" class="bg-gray-900 hover:bg-gray-800 text-gray-200 text-xs py-2 rounded-lg border border-gray-800 transition-colors text-center">
                                    100rb
                                </button>
                                <button type="button" @click="setCash(0)" class="bg-gray-900 hover:bg-red-500/20 text-red-400 text-xs py-2 rounded-lg border border-red-500/20 transition-colors text-center">
                                    Reset
                                </button>
                            </div>

                            <!-- Collapsible Keypad Dialer (Fixed 3 columns inline style) -->
                            <div x-show="showKeypad" x-transition class="pt-2 border-t border-gray-800">
                                <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px;" class="font-mono text-xs">
                                    <template x-for="btn in ['7','8','9','4','5','6','1','2','3','0','00','C']" :key="btn">
                                        <button type="button" @click="pressKeypad(btn)"
                                            class="bg-gray-900 hover:bg-gray-800 active:scale-95 text-white font-bold py-2.5 rounded-lg border border-gray-800 transition-all flex items-center justify-center"
                                            :class="btn === 'C' ? 'text-red-400 bg-red-500/10 hover:bg-red-500/20 border-red-500/20' : ''">
                                            <span x-text="btn"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Kembalian Display Box -->
                            <div class="pt-1">
                                <template x-if="cart.length > 0 && cashGiven >= total">
                                    <div class="bg-green-500/10 border border-green-500/40 rounded-xl p-3 text-center shadow-[0_0_15px_rgba(34,197,94,0.1)]">
                                        <span class="block text-[10px] text-green-400 font-semibold uppercase tracking-wider">Kembalian</span>
                                        <span class="text-xl font-bold text-green-400 font-mono" x-text="'Rp ' + (cashGiven - total).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                                <template x-if="cart.length > 0 && cashGiven > 0 && cashGiven < total">
                                    <div class="bg-red-500/10 border border-red-500/40 rounded-xl p-3 text-center">
                                        <span class="block text-[10px] text-red-400 font-semibold uppercase tracking-wider">Kurang</span>
                                        <span class="text-base font-bold text-red-400 font-mono" x-text="'Rp ' + (total - cashGiven).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                                <template x-if="cart.length > 0 && (!cashGiven || cashGiven === 0)">
                                    <div class="bg-gray-900/60 border border-gray-800 rounded-xl p-2 text-center text-xs text-gray-400 italic">
                                        Masukkan nominal pembayaran tunai
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div id="cart-inputs"></div>
                        
                        <!-- Submit Button -->
                        <button type="submit" :disabled="cart.length === 0 || (paymentMethod === 'cash' && cashGiven < total)"
                            class="w-full bg-neon hover:bg-[#c4e600] disabled:opacity-40 disabled:cursor-not-allowed text-darker font-bold py-3.5 px-4 rounded-xl transition-all flex items-center justify-center space-x-2 shadow-lg shadow-neon/10 active:scale-98 text-sm">
                            <i class="ph ph-cash-register text-xl"></i>
                            <span>Selesaikan Transaksi</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <script>
        function pos() {
            return {
                cart: [],
                search: '',
                selectedCategory: 'all',
                paymentMethod: 'cash',
                cashGiven: 0,
                showKeypad: false,
                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                setCash(val) {
                    this.cashGiven = parseInt(val) || 0;
                },
                pressKeypad(key) {
                    if (key === 'C') {
                        this.cashGiven = 0;
                    } else if (key === '00') {
                        const current = this.cashGiven ? String(this.cashGiven) : '';
                        this.cashGiven = current ? parseInt(current + '00') : 0;
                    } else {
                        const current = this.cashGiven ? String(this.cashGiven) : '';
                        this.cashGiven = parseInt(current + key);
                    }
                },
                addItem(id, name, price, stock) {
                    const existing = this.cart.find(i => i.id === id);
                    if (existing) {
                        if (existing.qty < stock) existing.qty++;
                        else alert('Stok ' + name + ' hanya tersisa ' + stock);
                    } else {
                        this.cart.push({ id, name, price, qty: 1, stock });
                    }
                },
                increase(idx) {
                    const item = this.cart[idx];
                    if (item.qty < item.stock) item.qty++;
                    else alert('Stok ' + item.name + ' hanya tersisa ' + item.stock);
                },
                decrease(idx) {
                    if (this.cart[idx].qty > 1) {
                        this.cart[idx].qty--;
                    } else {
                        this.cart.splice(idx, 1);
                    }
                },
                submitForm(e) {
                    if (this.cart.length === 0) return;
                    if (this.paymentMethod === 'cash' && this.cashGiven < this.total) {
                        alert('Uang tunai yang dimasukkan kurang dari total belanja!');
                        return;
                    }
                    const form = e.target;
                    const container = document.getElementById('cart-inputs');
                    container.innerHTML = '';
                    this.cart.forEach((item, idx) => {
                        container.innerHTML += '<input type="hidden" name="items[' + idx + '][snack_id]" value="' + item.id + '">';
                        container.innerHTML += '<input type="hidden" name="items[' + idx + '][qty]" value="' + item.qty + '">';
                    });
                    if (this.paymentMethod === 'cash') {
                        container.innerHTML += '<input type="hidden" name="cash_given" value="' + (this.cashGiven || 0) + '">';
                    }
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>
