<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <span>{{ __('Gaji & Penggajian') }} - {{ $employee->name }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-400 flex items-center">
            <i class="ph ph-check-circle text-xl mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Form Tambah/Edit Gaji --}}
        <div class="lg:col-span-1">
            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg sticky top-6">
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                    <i class="ph ph-money text-neon"></i> Buat Slip Gaji
                </h3>
                
                <form method="POST" action="{{ route('employees.payrolls.store', $employee->id) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Bulan</label>
                            <select name="month" required class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Tahun</label>
                            <input type="number" name="year" required value="{{ date('Y') }}" min="2020"
                                class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Gaji Pokok</label>
                        <input type="number" name="base_salary" id="base_salary" value="{{ intval($employee->base_salary) }}" required min="0" step="1000"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tunjangan / Bonus</label>
                        <input type="number" name="allowances" id="allowances" value="0" min="0" step="1000"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Potongan</label>
                        <input type="number" name="deductions" id="deductions" value="0" min="0" step="1000"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono text-red-400">
                    </div>
                    
                    <div class="p-3 bg-dark/50 border border-gray-700 rounded-lg">
                        <p class="text-xs text-gray-400 mb-1">Total Gaji Bersih</p>
                        <p class="text-xl font-bold text-neon" id="total_display">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Status Pembayaran</label>
                        <select name="status" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <option value="paid">Lunas (Paid)</option>
                            <option value="unpaid">Belum Lunas (Unpaid)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Catatan</label>
                        <textarea name="notes" rows="2" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm" placeholder="Catatan bonus dll..."></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center justify-center">
                            <i class="ph ph-floppy-disk mr-2"></i> Simpan Data Gaji
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Riwayat Penggajian --}}
        <div class="lg:col-span-2">
            <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark border-b border-gray-800 text-xs uppercase tracking-wider text-gray-400">
                                <th class="px-6 py-4 font-medium">Periode</th>
                                <th class="px-6 py-4 font-medium">Rincian</th>
                                <th class="px-6 py-4 font-medium text-right">Total Bersih</th>
                                <th class="px-6 py-4 font-medium text-center">Status</th>
                                <th class="px-6 py-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @forelse ($payrolls as $pr)
                                <tr class="hover:bg-dark/50 transition-colors">
                                    <td class="px-6 py-4 text-white font-medium">
                                        {{ date('F', mktime(0,0,0,$pr->month,1)) }} {{ $pr->year }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="flex justify-between text-gray-300"><span class="text-gray-500">Pokok:</span> <span>{{ number_format($pr->base_salary, 0, ',', '.') }}</span></div>
                                        <div class="flex justify-between text-green-400"><span class="text-gray-500">Bonus:</span> <span>+{{ number_format($pr->allowances, 0, ',', '.') }}</span></div>
                                        <div class="flex justify-between text-red-400"><span class="text-gray-500">Potongan:</span> <span>-{{ number_format($pr->deductions, 0, ',', '.') }}</span></div>
                                        @if($pr->notes)
                                            <p class="text-[10px] text-gray-500 mt-1 italic">"{{ Str::limit($pr->notes, 30) }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-neon font-bold">
                                        Rp {{ number_format($pr->total_salary, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($pr->status === 'paid')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Lunas</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Belum Dibayar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('employees.payrolls.destroy', $pr->id) }}" class="inline" onsubmit="return confirm('Hapus data gaji ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <i class="ph ph-receipt text-4xl mb-2 block text-gray-600"></i>
                                        Belum ada riwayat penggajian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        function calculateTotal() {
            const base = parseInt(document.getElementById('base_salary').value) || 0;
            const allow = parseInt(document.getElementById('allowances').value) || 0;
            const deduc = parseInt(document.getElementById('deductions').value) || 0;
            
            const total = base + allow - deduc;
            document.getElementById('total_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        document.getElementById('base_salary').addEventListener('input', calculateTotal);
        document.getElementById('allowances').addEventListener('input', calculateTotal);
        document.getElementById('deductions').addEventListener('input', calculateTotal);
    </script>
</x-app-layout>
