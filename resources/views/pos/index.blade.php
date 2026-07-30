<x-app-layout>
    <x-slot name="header">
        {{ __('POS Kasir Snack') }}
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="pos()">
        
        <!-- Daftar Produk -->
        <div class="lg:col-span-2">
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-medium">Pilih Produk</h3>
                    <input type="text" x-model="search" placeholder="Cari produk..." class="border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm w-48">
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
                    @foreach($snacks as $snack)
                        <button type="button" 
                            @click="addItem({{ $snack->id }}, '{{ addslashes($snack->name) }}', {{ $snack->selling_price }}, {{ $snack->stock }})"
                            x-show="search === '' || '{{ strtolower($snack->name) }}'.includes(search.toLowerCase())"
                            class="bg-dark hover:bg-gray-800 border border-gray-800 hover:border-neon rounded-2xl p-5 text-center transition-all duration-300 group flex flex-col items-center justify-between min-h-[160px] shadow-sm hover:shadow-neon/10 hover:-translate-y-1">
                            
                            <div class="w-16 h-16 rounded-full bg-gray-800/50 group-hover:bg-neon/10 flex items-center justify-center text-4xl mb-3 transition-colors">
                                @if($snack->category === 'Minuman') 🥤
                                @elseif($snack->category === 'Makanan') 🍫
                                @elseif($snack->category === 'Suplemen') 💊
                                @else 📦 @endif
                            </div>
                            
                            <div class="flex-1 w-full flex flex-col justify-end">
                                <p class="text-sm font-semibold text-white leading-tight mb-1 group-hover:text-neon transition-colors line-clamp-2">{{ $snack->name }}</p>
                                <div class="flex items-center justify-between w-full mt-2 pt-2 border-t border-gray-800/50">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-medium">Stok: {{ $snack->stock }}</p>
                                    <p class="text-neon font-bold text-sm">Rp{{ number_format($snack->selling_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Keranjang Belanja -->
        <div class="lg:col-span-1">
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg flex flex-col sticky top-4">
                <div class="p-6 border-b border-gray-800">
                    <h3 class="text-white font-medium">Keranjang</h3>
                </div>

                <div class="p-4 flex-1 space-y-2 min-h-40 max-h-80 overflow-y-auto">
                    <template x-if="cart.length === 0">
                        <div class="text-center text-gray-500 py-8">
                            <i class="ph ph-shopping-cart text-3xl block mb-2 text-gray-600"></i>
                            Keranjang kosong
                        </div>
                    </template>
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="flex items-center justify-between bg-dark rounded-xl p-4 border border-gray-800 shadow-sm transition-all hover:border-gray-700">
                            <div class="flex-1 min-w-0 pr-2">
                                <p class="text-sm font-semibold text-white truncate mb-0.5" x-text="item.name"></p>
                                <p class="text-xs text-neon font-bold" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></p>
                            </div>
                            <div class="flex items-center space-x-3 bg-gray-900 rounded-lg p-1 border border-gray-800">
                                <button @click="decrease(idx)" class="w-7 h-7 rounded-md bg-gray-800 hover:bg-red-500/20 hover:text-red-400 text-gray-300 text-sm flex items-center justify-center transition-colors"><i class="ph ph-minus"></i></button>
                                <span class="text-white text-sm font-bold w-6 text-center" x-text="item.qty"></span>
                                <button @click="increase(idx)" class="w-7 h-7 rounded-md bg-gray-800 hover:bg-neon/20 hover:text-neon text-gray-300 text-sm flex items-center justify-center transition-colors"><i class="ph ph-plus"></i></button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-6 border-t border-gray-800 space-y-4">
                    <div class="flex justify-between text-lg font-bold">
                        <span class="text-gray-300">Total</span>
                        <span class="text-neon" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>

                    <form method="POST" action="{{ route('pos.checkout') }}" @submit.prevent="submitForm($event)">
                        @csrf
                        
                        <!-- Metode Pembayaran -->
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-400 mb-2">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer border rounded-lg p-2.5 text-center transition-all flex items-center justify-center space-x-2 text-xs"
                                    :class="paymentMethod === 'cash' ? 'border-neon bg-neon/10 text-neon font-bold shadow-[0_0_10px_rgba(224,255,0,0.1)]' : 'border-gray-800 text-gray-400 hover:border-gray-700'">
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="hidden">
                                    <i class="ph ph-money text-base"></i>
                                    <span>Tunai</span>
                                </label>
                                <label class="cursor-pointer border rounded-lg p-2.5 text-center transition-all flex items-center justify-center space-x-2 text-xs"
                                    :class="paymentMethod === 'transfer' ? 'border-neon bg-neon/10 text-neon font-bold shadow-[0_0_10px_rgba(224,255,0,0.1)]' : 'border-gray-800 text-gray-400 hover:border-gray-700'">
                                    <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="hidden">
                                    <i class="ph ph-bank text-base"></i>
                                    <span>Non-Tunai</span>
                                </label>
                            </div>
                        </div>

                        <!-- KALKULATOR TUNAI / CASH CALCULATOR -->
                        <div x-show="paymentMethod === 'cash'" x-transition class="bg-dark/80 rounded-xl p-4 border border-gray-800 mb-4 space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-800 pb-2">
                                <span class="text-xs font-semibold text-gray-300 flex items-center gap-1.5">
                                    <i class="ph ph-calculator text-neon text-base"></i> Kalkulator Tunai
                                </span>
                                <button type="button" @click="showKeypad = !showKeypad" 
                                    class="text-[11px] text-neon hover:underline flex items-center gap-1 font-medium bg-neon/10 px-2 py-0.5 rounded border border-neon/20">
                                    <i class="ph ph-keypad"></i> <span x-text="showKeypad ? 'Sembunyikan Keypad' : 'Buka Keypad'"></span>
                                </button>
                            </div>

                            <!-- Input Uang Diterima -->
                            <div>
                                <label class="block text-[11px] font-medium text-gray-400 mb-1">Uang Diterima (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-semibold">Rp</span>
                                    <input type="number" min="0" step="1000" x-model.number="cashGiven" placeholder="0"
                                        class="w-full pl-9 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white font-mono text-base font-bold focus:ring-neon focus:border-neon">
                                </div>
                            </div>

                            <!-- Quick Preset Denomination Buttons -->
                            <div>
                                <p class="text-[10px] text-gray-400 font-medium mb-1.5 uppercase tracking-wider">Pilihan Cepat / Uang Pas:</p>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <button type="button" @click="setCash(total)" class="bg-gray-800 hover:bg-neon hover:text-darker text-neon font-bold text-xs py-1.5 rounded-lg border border-neon/30 transition-all">
                                        Pas
                                    </button>
                                    <button type="button" @click="setCash(10000)" class="bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs py-1.5 rounded-lg border border-gray-700 transition-colors">
                                        10rb
                                    </button>
                                    <button type="button" @click="setCash(20000)" class="bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs py-1.5 rounded-lg border border-gray-700 transition-colors">
                                        20rb
                                    </button>
                                    <button type="button" @click="setCash(50000)" class="bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs py-1.5 rounded-lg border border-gray-700 transition-colors">
                                        50rb
                                    </button>
                                    <button type="button" @click="setCash(100000)" class="bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs py-1.5 rounded-lg border border-gray-700 transition-colors">
                                        100rb
                                    </button>
                                    <button type="button" @click="setCash(0)" class="bg-gray-800 hover:bg-red-500/20 text-red-400 text-xs py-1.5 rounded-lg border border-red-500/30 transition-colors">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Keypad Kalkulator -->
                            <div x-show="showKeypad" x-transition class="pt-2 border-t border-gray-800/80">
                                <div class="grid grid-cols-3 gap-1.5 font-mono text-sm">
                                    <template x-for="btn in ['7','8','9','4','5','6','1','2','3','0','00','C']" :key="btn">
                                        <button type="button" @click="pressKeypad(btn)"
                                            class="bg-gray-900 hover:bg-gray-800 active:scale-95 text-white font-bold py-2 rounded border border-gray-800 transition-all flex items-center justify-center"
                                            :class="btn === 'C' ? 'text-red-400 bg-red-500/10 hover:bg-red-500/20' : ''">
                                            <span x-text="btn"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Status Kembalian / Kurang -->
                            <div class="pt-1">
                                <template x-if="cart.length > 0 && cashGiven >= total">
                                    <div class="bg-green-500/10 border border-green-500/40 rounded-xl p-3 text-center shadow-[0_0_15px_rgba(34,197,94,0.1)]">
                                        <span class="block text-[11px] text-green-400 font-semibold uppercase tracking-wider">Kembalian</span>
                                        <span class="text-xl font-bold text-green-400 font-mono" x-text="'Rp ' + (cashGiven - total).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                                <template x-if="cart.length > 0 && cashGiven > 0 && cashGiven < total">
                                    <div class="bg-red-500/10 border border-red-500/40 rounded-xl p-3 text-center">
                                        <span class="block text-[11px] text-red-400 font-semibold uppercase tracking-wider">Uang Kurang</span>
                                        <span class="text-lg font-bold text-red-400 font-mono" x-text="'Rp ' + (total - cashGiven).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                                <template x-if="cart.length > 0 && (!cashGiven || cashGiven === 0)">
                                    <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-2.5 text-center text-xs text-gray-400 italic">
                                        Masukkan nominal uang tunai yang diterima
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div id="cart-inputs"></div>
                        <button type="submit" :disabled="cart.length === 0 || (paymentMethod === 'cash' && cashGiven < total)"
                            class="w-full bg-neon hover:bg-[#c4e600] disabled:opacity-40 disabled:cursor-not-allowed text-darker font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2 shadow-lg shadow-neon/10">
                            <i class="ph ph-cash-register text-xl"></i>
                            <span>Checkout</span>
                        </button>
                    </form>

                    <button @click="cart = []; cashGiven = 0;" x-show="cart.length > 0"
                        class="w-full text-gray-400 hover:text-red-400 text-sm transition-colors py-1">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pos() {
            return {
                cart: [],
                search: '',
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
