<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use App\Models\SnackRestock;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SnackController extends Controller
{
    public function index()
    {
        $snacks = Snack::orderBy('category')->orderBy('name')->get();
        $restocks = SnackRestock::with(['snack', 'user'])->latest('restock_date')->latest('id')->paginate(15);

        $totalGudang = $snacks->sum('stock_gudang');
        $totalKulkas = $snacks->sum('stock_kulkas');
        $totalProductTypes = $snacks->count();

        return view('snacks.index', compact('snacks', 'restocks', 'totalGudang', 'totalKulkas', 'totalProductTypes'));
    }

    public function create()
    {
        return view('snacks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'snack_code' => 'required|string|unique:snacks,snack_code',
            'category' => 'required|string',
            'capital_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_gudang' => 'nullable|integer|min:0',
            'stock_kulkas' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        $stockGudang = $request->stock_gudang ?? $request->stock ?? 0;
        $stockKulkas = $request->stock_kulkas ?? 0;
        $totalStock = $stockGudang + $stockKulkas;

        $snack = Snack::create([
            'name' => $request->name,
            'snack_code' => $request->snack_code,
            'category' => $request->category,
            'capital_price' => $request->capital_price,
            'selling_price' => $request->selling_price,
            'stock_gudang' => $stockGudang,
            'stock_kulkas' => $stockKulkas,
            'stock' => $totalStock,
        ]);

        \App\Models\ActivityLog::log('CREATE', 'Kantin', "Menambahkan produk baru: {$snack->name} (Gudang: {$stockGudang}, Etalase/Kulkas: {$stockKulkas})");

        return redirect()->route('snacks.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show(Snack $snack) { return redirect()->route('snacks.edit', $snack); }

    public function edit(Snack $snack)
    {
        return view('snacks.edit', compact('snack'));
    }

    public function update(Request $request, Snack $snack)
    {
        $request->validate([
            'name' => 'required|string',
            'snack_code' => 'required|string|unique:snacks,snack_code,' . $snack->id,
            'category' => 'required|string',
            'capital_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_gudang' => 'required|integer|min:0',
            'stock_kulkas' => 'required|integer|min:0',
        ]);

        $stockGudang = (int) $request->stock_gudang;
        $stockKulkas = (int) $request->stock_kulkas;
        $totalStock = $stockGudang + $stockKulkas;

        $oldStockGudang = $snack->stock_gudang;
        $oldStockKulkas = $snack->stock_kulkas;
        $oldPrice = $snack->selling_price;

        $snack->update([
            'name' => $request->name,
            'snack_code' => $request->snack_code,
            'category' => $request->category,
            'capital_price' => $request->capital_price,
            'selling_price' => $request->selling_price,
            'stock_gudang' => $stockGudang,
            'stock_kulkas' => $stockKulkas,
            'stock' => $totalStock,
        ]);

        $desc = "Memperbarui produk {$snack->name}";
        if ($oldStockGudang != $stockGudang || $oldStockKulkas != $stockKulkas) {
            $desc .= " (Gudang: $oldStockGudang -> $stockGudang, Etalase/Kulkas: $oldStockKulkas -> $stockKulkas)";
        }
        if ($oldPrice != $snack->selling_price) {
            $desc .= " (Harga dari Rp " . number_format($oldPrice, 0, ',', '.') . " ke Rp " . number_format($snack->selling_price, 0, ',', '.') . ")";
        }

        \App\Models\ActivityLog::log('UPDATE', 'Kantin', $desc);

        return redirect()->route('snacks.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Snack $snack)
    {
        $name = $snack->name;
        $snack->delete();

        \App\Models\ActivityLog::log('DELETE', 'Kantin', "Menghapus produk: {$name}");

        return redirect()->route('snacks.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function storeIncoming(Request $request)
    {
        $request->validate([
            'snack_id' => 'required|exists:snacks,id',
            'destination' => 'required|in:gudang,kulkas',
            'quantity' => 'required|integer|min:1',
            'capital_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
            'notes' => 'nullable|string',
            'restock_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $snack = Snack::findOrFail($request->snack_id);
            $qty = (int) $request->quantity;
            $capitalPrice = (float) $request->capital_price;
            $totalCost = $qty * $capitalPrice;
            $destination = $request->destination;

            if ($destination === 'kulkas') {
                $snack->increment('stock_kulkas', $qty);
            } else {
                $snack->increment('stock_gudang', $qty);
            }

            $snack->refresh();
            $snack->stock = ($snack->stock_gudang ?? 0) + ($snack->stock_kulkas ?? 0);
            $snack->save();

            // Otomatis tercatat di Laporan Pengeluaran Operasional (Expenses)
            $expense = Expense::create([
                'date' => Carbon::parse($request->restock_date)->format('Y-m-d'),
                'category' => 'Pembelian Snack / Restok',
                'amount' => $totalCost,
                'description' => "Restok {$snack->name} ({$qty} Pcs)" . ($request->supplier ? " - Supplier: {$request->supplier}" : ""),
            ]);

            // Catat di Laporan Barang Masuk & Restok Snack
            SnackRestock::create([
                'snack_id' => $snack->id,
                'user_id' => Auth::id(),
                'expense_id' => $expense->id,
                'type' => 'incoming_supplier',
                'qty_gudang' => $destination === 'gudang' ? $qty : 0,
                'qty_kulkas' => $destination === 'kulkas' ? $qty : 0,
                'capital_price' => $capitalPrice,
                'total_cost' => $totalCost,
                'supplier' => $request->supplier,
                'notes' => $request->notes,
                'restock_date' => $request->restock_date,
            ]);

            DB::commit();

            \App\Models\ActivityLog::log('CREATE', 'Kantin', "Input Barang Masuk: {$snack->name} +{$qty} pcs ke {$destination} (Total Modal: Rp " . number_format($totalCost, 0, ',', '.') . ")");

            return redirect()->route('snacks.index')->with('success', "Barang masuk berhasil dicatat! Stok {$destination} bertambah {$qty} pcs & pengeluaran otomatis tercatat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mencatat barang masuk: ' . $e->getMessage()]);
        }
    }

    public function refillKulkas(Request $request)
    {
        $request->validate([
            'snack_id' => 'required|exists:snacks,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $snack = Snack::findOrFail($request->snack_id);
            $qty = (int) $request->quantity;

            if ($snack->stock_gudang < $qty) {
                DB::rollBack();
                return back()->withErrors(['error' => "Stok gudang {$snack->name} tidak mencukupi! Tersisa di gudang: {$snack->stock_gudang} pcs."]);
            }

            $snack->decrement('stock_gudang', $qty);
            $snack->increment('stock_kulkas', $qty);
            $snack->refresh();
            $snack->stock = ($snack->stock_gudang ?? 0) + ($snack->stock_kulkas ?? 0);
            $snack->save();

            SnackRestock::create([
                'snack_id' => $snack->id,
                'user_id' => Auth::id(),
                'expense_id' => null,
                'type' => 'refill_kulkas',
                'qty_gudang' => -$qty,
                'qty_kulkas' => $qty,
                'capital_price' => $snack->capital_price,
                'total_cost' => 0,
                'supplier' => null,
                'notes' => $request->notes ?? 'Refill dari Gudang ke Etalase/Kulkas',
                'restock_date' => now(),
            ]);

            DB::commit();

            \App\Models\ActivityLog::log('UPDATE', 'Kantin', "Refill Etalase/Kulkas: Pindah {$qty} pcs {$snack->name} dari Gudang ke Etalase/Kulkas");

            return redirect()->route('snacks.index')->with('success', "Berhasil memindahkan {$qty} pcs {$snack->name} dari Gudang ke Etalase/Kulkas!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memindahkan ke Etalase/Kulkas: ' . $e->getMessage()]);
        }
    }
}
