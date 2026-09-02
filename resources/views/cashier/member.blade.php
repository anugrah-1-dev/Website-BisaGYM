<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <i class="ph ph-cash-register text-neon text-2xl"></i>
            <span>{{ __('Kasir Pembayaran Member') }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 flex items-center">
            <i class="ph ph-warning-circle text-xl mr-3"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Kolom Kiri: Form Pencarian VIP ID -->
        <div class="lg:col-span-5">
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg p-6 mb-6">
                <h3 class="text-white font-medium mb-4 flex items-center gap-2 border-b border-gray-800 pb-2">
                    <i class="ph ph-barcode text-neon"></i> Scan / Input VIP ID (Member)
                </h3>
                
                <form method="GET" action="{{ route('cashier.member') }}" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">VIP ID Member</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-identification-card text-gray-500 text-xl"></i>
                            </div>
                            <input type="text" name="vip_id" value="{{ $vip_id ?? '' }}" class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-dark text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon font-mono text-lg transition-colors" placeholder="VIP-XXXXXXXX-XXXX" autofocus required autocomplete="off">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Scan barcode di E-Card member atau ketik manual VIP ID-nya.</p>
                    </div>
                    
                    <button type="submit" class="w-full bg-dark hover:bg-gray-800 border border-gray-700 hover:border-neon text-white font-medium py-3 px-4 rounded-lg transition-colors flex justify-center items-center gap-2">
                        <i class="ph ph-magnifying-glass"></i> Cari Tagihan Member
                    </button>
                </form>
            </div>

            <!-- Form Non Member (Harian) -->
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg p-6">
                <h3 class="text-white font-medium mb-4 flex items-center gap-2 border-b border-gray-800 pb-2">
                    <i class="ph ph-user-plus text-pink-400"></i> Pendaftaran Non-Member (Harian)
                </h3>
                
                <form method="POST" action="{{ route('cashier.pay-non-member') }}" class="space-y-4" onsubmit="return confirm('Proses pembayaran harian Non-Member sejumlah Rp 50.000?')" x-data="{ method: 'cash' }">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full px-3 py-2 border border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm" placeholder="Nama pelanggan" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Nomor HP</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-700 rounded-lg bg-dark text-white focus:ring-pink-500 focus:border-pink-500 text-sm" placeholder="Contoh: 0812..." required>
                    </div>
                    
                    <div class="pt-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                            <label @click="method = 'cash'" :class="method === 'cash' ? 'border-pink-500 bg-pink-500/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="cash" class="sr-only" required checked>
                                <div class="text-center">
                                    <i class="ph ph-money text-xl transition-colors" :class="method === 'cash' ? 'text-pink-400' : 'text-gray-400'"></i>
                                    <div class="text-xs font-medium" :class="method === 'cash' ? 'text-white' : 'text-gray-300'">Cash</div>
                                </div>
                            </label>
                            
                            <label @click="method = 'transfer'" :class="method === 'transfer' ? 'border-pink-500 bg-pink-500/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="transfer" class="sr-only" required>
                                <div class="text-center">
                                    <i class="ph ph-bank text-xl transition-colors" :class="method === 'transfer' ? 'text-pink-400' : 'text-gray-400'"></i>
                                    <div class="text-xs font-medium" :class="method === 'transfer' ? 'text-white' : 'text-gray-300'">Transfer</div>
                                </div>
                            </label>

                            <label @click="method = 'qris'" :class="method === 'qris' ? 'border-pink-500 bg-pink-500/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="qris" class="sr-only" required>
                                <div class="text-center">
                                    <i class="ph ph-qr-code text-xl transition-colors" :class="method === 'qris' ? 'text-pink-400' : 'text-gray-400'"></i>
                                    <div class="text-xs font-medium" :class="method === 'qris' ? 'text-white' : 'text-gray-300'">QRIS</div>
                                </div>
                            </label>

                            <label @click="method = 'debit'" :class="method === 'debit' ? 'border-pink-500 bg-pink-500/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="debit" class="sr-only" required>
                                <div class="text-center">
                                    <i class="ph ph-credit-card text-xl transition-colors" :class="method === 'debit' ? 'text-pink-400' : 'text-gray-400'"></i>
                                    <div class="text-xs font-medium" :class="method === 'debit' ? 'text-white' : 'text-gray-300'">Debit</div>
                                </div>
                            </label>

                            <label @click="method = 'gratis'" :class="method === 'gratis' ? 'border-pink-500 bg-pink-500/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="gratis" class="sr-only" required>
                                <div class="text-center">
                                    <i class="ph ph-gift text-xl transition-colors" :class="method === 'gratis' ? 'text-pink-400' : 'text-gray-400'"></i>
                                    <div class="text-xs font-medium" :class="method === 'gratis' ? 'text-white' : 'text-gray-300'">Gratis</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-pink-500 hover:bg-pink-400 text-white font-bold py-3 px-4 rounded-lg transition-colors flex justify-center items-center gap-2 mt-4">
                        <i class="ph ph-check-circle text-lg"></i> Bayar Rp 50.000
                    </button>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Tagihan & Pembayaran -->
        <div class="lg:col-span-7">
            @if($vip_id)
                @if(!$member)
                    <div class="bg-card rounded-xl border border-red-500/30 p-8 text-center shadow-lg">
                        <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-x-circle text-red-500 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Member Tidak Ditemukan</h3>
                        <p class="text-gray-400">Tidak ada member dengan VIP ID <span class="font-mono text-gray-300">{{ $vip_id }}</span></p>
                        <a href="{{ route('cashier.member') }}" class="mt-4 inline-block px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">Coba Lagi</a>
                    </div>
                @elseif($member && !$unpaidTransaction)
                    <div class="bg-card rounded-xl border border-green-500/30 p-8 text-center shadow-lg">
                        <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-check-circle text-green-500 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $member->name }}</h3>
                        <p class="text-gray-400 mb-6">Member ini tidak memiliki tagihan pembayaran / sudah lunas semua.</p>
                        <a href="{{ route('members.show', $member->id) }}" class="inline-flex px-6 py-2 bg-dark hover:bg-gray-800 border border-gray-700 text-white rounded-lg transition-colors items-center gap-2">
                            <i class="ph ph-user"></i> Lihat Profil Member
                        </a>
                    </div>
                @else
                    <!-- Tampilan Rincian Pembayaran -->
                    <div class="bg-card rounded-xl border border-neon/50 shadow-[0_0_15px_rgba(212,255,0,0.1)] overflow-hidden">
                        <div class="bg-dark/50 border-b border-gray-800 p-6 flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-white mb-1">Rincian Tagihan</h3>
                                <p class="text-sm text-gray-400 font-mono">{{ $unpaidTransaction->transaction_code }}</p>
                            </div>
                            <span class="px-3 py-1 bg-yellow-500/20 text-yellow-500 border border-yellow-500/30 rounded-full text-xs font-bold uppercase tracking-wider">Unpaid</span>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-800">
                                @if($member->photo_path)
                                    <img src="{{ Storage::url($member->photo_path) }}" class="w-16 h-16 rounded-lg object-cover border border-gray-700" alt="Foto Member">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-800 border border-gray-700 flex items-center justify-center">
                                        <i class="ph ph-user text-2xl text-gray-500"></i>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-white font-bold text-lg">{{ $member->name }}</h4>
                                    <p class="text-sm text-gray-400 font-mono">{{ $member->member_id }} • {{ $member->phone }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center pb-4 border-b border-gray-800">
                                    <span class="text-gray-400 text-sm">Paket</span>
                                    <span class="text-white font-medium">{{ $unpaidTransaction->package->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-4 border-b border-gray-800">
                                    <span class="text-gray-400 text-sm">Jenis Transaksi</span>
                                    <span class="text-white font-medium capitalize">{{ $unpaidTransaction->transaction_type === 'new' ? 'Pendaftaran Baru' : 'Perpanjangan Paket' }}</span>
                                </div>
                                
                                @if($unpaidTransaction->transaction_type === 'new')
                                    <div class="flex justify-between items-center pb-4 border-b border-gray-800 mt-4">
                                        <span class="text-gray-400 text-sm">Harga Paket</span>
                                        <span class="text-white font-medium">Rp {{ number_format($unpaidTransaction->package->price, 0, ',', '.') }}</span>
                                    </div>
                                    @if($unpaidTransaction->discount_percentage > 0)
                                    <div class="flex justify-between items-center pb-4 border-b border-gray-800 mt-4">
                                        <span class="text-gray-400 text-sm">Diskon Profesi ({{ $unpaidTransaction->discount_percentage }}%)</span>
                                        <span class="text-pink-400 font-medium">- Rp {{ number_format(($unpaidTransaction->package->price * $unpaidTransaction->discount_percentage) / 100, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    @if($unpaidTransaction->admin_fee > 0)
                                    <div class="flex justify-between items-center pb-4 border-b border-gray-800 mt-4">
                                        <span class="text-gray-400 text-sm">Biaya Admin (Pembuatan Kartu)</span>
                                        <span class="text-white font-medium">+ Rp {{ number_format($unpaidTransaction->admin_fee, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                @endif
                            </div>
                            
                            <div class="bg-dark p-6 rounded-xl border border-gray-800 mb-6 flex justify-between items-center">
                                <span class="text-gray-400 text-lg">Total Dibayar</span>
                                <span class="text-3xl font-bold text-neon" id="totalAmount" data-amount="{{ $unpaidTransaction->amount }}">Rp {{ number_format($unpaidTransaction->amount, 0, ',', '.') }}</span>
                            </div>

                            <form method="POST" action="{{ route('cashier.pay', $unpaidTransaction->id) }}" x-data="{ method: 'cash' }">
                                @csrf
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-3">Pilih Metode Pembayaran</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                                        <label @click="method = 'cash'" :class="method === 'cash' ? 'border-neon bg-neon/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-colors">
                                            <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio" required checked>
                                            <div class="text-center">
                                                <i class="ph ph-money text-3xl mb-2 transition-colors" :class="method === 'cash' ? 'text-neon' : 'text-gray-400'"></i>
                                                <div class="font-medium text-sm" :class="method === 'cash' ? 'text-white' : 'text-gray-300'">Cash</div>
                                            </div>
                                        </label>
                                        
                                        <label @click="method = 'transfer'" :class="method === 'transfer' ? 'border-neon bg-neon/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-colors">
                                            <input type="radio" name="payment_method" value="transfer" class="sr-only payment-method-radio" required>
                                            <div class="text-center">
                                                <i class="ph ph-bank text-3xl mb-2 transition-colors" :class="method === 'transfer' ? 'text-neon' : 'text-gray-400'"></i>
                                                <div class="font-medium text-sm" :class="method === 'transfer' ? 'text-white' : 'text-gray-300'">Transfer</div>
                                            </div>
                                        </label>

                                        <label @click="method = 'qris'" :class="method === 'qris' ? 'border-neon bg-neon/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-colors">
                                            <input type="radio" name="payment_method" value="qris" class="sr-only payment-method-radio" required>
                                            <div class="text-center">
                                                <i class="ph ph-qr-code text-3xl mb-2 transition-colors" :class="method === 'qris' ? 'text-neon' : 'text-gray-400'"></i>
                                                <div class="font-medium text-sm" :class="method === 'qris' ? 'text-white' : 'text-gray-300'">QRIS</div>
                                            </div>
                                        </label>

                                        <label @click="method = 'debit'" :class="method === 'debit' ? 'border-neon bg-neon/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-colors">
                                            <input type="radio" name="payment_method" value="debit" class="sr-only payment-method-radio" required>
                                            <div class="text-center">
                                                <i class="ph ph-credit-card text-3xl mb-2 transition-colors" :class="method === 'debit' ? 'text-neon' : 'text-gray-400'"></i>
                                                <div class="font-medium text-sm" :class="method === 'debit' ? 'text-white' : 'text-gray-300'">Debit</div>
                                            </div>
                                        </label>

                                        <label @click="method = 'gratis'" :class="method === 'gratis' ? 'border-neon bg-neon/10' : 'border-gray-700 hover:border-gray-500'" class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-colors">
                                            <input type="radio" name="payment_method" value="gratis" class="sr-only payment-method-radio" required>
                                            <div class="text-center">
                                                <i class="ph ph-gift text-3xl mb-2 transition-colors" :class="method === 'gratis' ? 'text-neon' : 'text-gray-400'"></i>
                                                <div class="font-medium text-sm" :class="method === 'gratis' ? 'text-white' : 'text-gray-300'">Gratis</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Kalkulator Kasir (Hanya tampil untuk Cash) -->
                                <div id="calculatorSection" x-show="method === 'cash'" class="mb-6 bg-dark/30 p-4 rounded-xl border border-gray-800">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Uang Diterima (Rp)</label>
                                        <input type="text" id="receivedAmount" class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-3 text-xl text-white font-bold focus:ring-neon focus:border-neon" placeholder="0">
                                        <div class="flex gap-2 mt-3 overflow-x-auto pb-2">
                                            @php
                                                $amt = (int) $unpaidTransaction->amount;
                                                $suggs = [];
                                                if ($amt > 0) {
                                                    $suggs[] = ceil($amt / 50000) * 50000;
                                                    $suggs[] = ceil($amt / 100000) * 100000;
                                                    $suggs[] = ceil($amt / 500000) * 500000;
                                                    $suggs[] = ceil($amt / 1000000) * 1000000;
                                                    
                                                    // Filter out values less than or equal to exact amount
                                                    $suggs = array_filter(array_unique($suggs), function($val) use ($amt) {
                                                        return $val > $amt;
                                                    });
                                                    sort($suggs);
                                                }
                                                // Fallback defaults if amount is 0 or something weird
                                                if (empty($suggs)) {
                                                    $suggs = [50000, 100000, 150000, 200000];
                                                }
                                                // Limit to 4 buttons
                                                $suggs = array_slice($suggs, 0, 4);
                                            @endphp

                                            @foreach($suggs as $val)
                                                @php
                                                    // Format: if >= 1 million use JT, else use K
                                                    if ($val >= 1000000 && $val % 100000 == 0) {
                                                        $label = rtrim(rtrim(number_format($val / 1000000, 1, ',', ''), '0'), ',') . 'Jt';
                                                    } else {
                                                        $label = number_format($val / 1000, 0, ',', '.') . 'K';
                                                    }
                                                @endphp
                                                <button type="button" class="quick-cash px-3 py-1 bg-gray-800 hover:bg-gray-700 rounded text-sm text-gray-300 whitespace-nowrap" data-val="{{ $val }}">{{ $label }}</button>
                                            @endforeach
                                            <button type="button" class="quick-cash px-3 py-1 bg-neon/20 hover:bg-neon/30 text-neon rounded text-sm whitespace-nowrap border border-neon/30" data-val="{{ $unpaidTransaction->amount }}">Uang Pas</button>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-800">
                                        <span class="text-gray-400 text-sm">Kembalian</span>
                                        <span class="text-2xl font-bold text-white" id="changeAmount">Rp 0</span>
                                    </div>
                                </div>
                                
                                <button type="submit" id="btnSubmitPayment" class="w-full py-4 bg-neon hover:bg-[#c4e600] text-darker font-bold text-lg rounded-xl shadow-[0_0_15px_rgba(212,255,0,0.3)] hover:shadow-[0_0_20px_rgba(212,255,0,0.5)] transition-all flex justify-center items-center gap-2">
                                    <i class="ph ph-check-circle text-2xl"></i> Proses Pembayaran Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @else
                <!-- State Kosong / Belum cari -->
                <div class="h-full min-h-[400px] flex flex-col items-center justify-center text-gray-500 bg-card rounded-xl border border-gray-800 shadow-lg border-dashed">
                    <i class="ph ph-barcode text-6xl mb-4 opacity-50"></i>
                    <p class="text-lg">Scan barcode e-card member</p>
                    <p class="text-sm">Tagihan pembayaran akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentRadios = document.querySelectorAll('.payment-method-radio');
            const calculatorSection = document.getElementById('calculatorSection');
            const receivedAmountInput = document.getElementById('receivedAmount');
            const changeAmountDisplay = document.getElementById('changeAmount');
            const btnSubmitPayment = document.getElementById('btnSubmitPayment');
            const quickCashBtns = document.querySelectorAll('.quick-cash');
            
            const totalAmountElement = document.getElementById('totalAmount');
            if (!totalAmountElement) return; // Mencegah error jika elemen tidak ada
            
            const totalAmount = parseInt(totalAmountElement.getAttribute('data-amount')) || 0;

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function parseRupiah(str) {
                return parseInt(str.replace(/[^0-9]/g, '')) || 0;
            }

            function updateChange() {
                const received = parseRupiah(receivedAmountInput.value);
                const change = received - totalAmount;
                
                if (change < 0 && received > 0) {
                    changeAmountDisplay.innerHTML = `<span class="text-red-500">Kurang: ${formatRupiah(Math.abs(change))}</span>`;
                } else {
                    changeAmountDisplay.innerHTML = formatRupiah(change > 0 ? change : 0);
                }
            }


            // Handle manual input
            receivedAmountInput.addEventListener('input', function(e) {
                const val = parseRupiah(e.target.value);
                if (val > 0) {
                    e.target.value = new Intl.NumberFormat('id-ID').format(val);
                } else {
                    e.target.value = '';
                }
                updateChange();
            });

            // Handle quick cash buttons
            quickCashBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-val'));
                    receivedAmountInput.value = new Intl.NumberFormat('id-ID').format(val);
                    updateChange();
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
