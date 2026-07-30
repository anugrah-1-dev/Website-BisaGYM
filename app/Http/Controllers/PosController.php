<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use App\Models\SnackTransaction;
use App\Models\SnackTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $snacks = Snack::where('stock', '>', 0)->orderBy('category')->get();
        return view('pos.index', compact('snacks'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.snack_id' => 'required|exists:snacks,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $transactionCode = 'POS-' . now()->format('YmdHis') . '-' . rand(100, 999);

            $snackTrx = SnackTransaction::create([
                'transaction_code' => $transactionCode,
                'user_id' => Auth::id(),
                'total_amount' => 0,
                'payment_method' => $request->payment_method ?? 'cash',
                'transaction_date' => Carbon::now(),
            ]);

            foreach ($request->items as $item) {
                $snack = Snack::findOrFail($item['snack_id']);
                $qty = (int) $item['qty'];

                if ($snack->stock < $qty) {
                    DB::rollBack();
                    return back()->withErrors(['error' => "Stok {$snack->name} tidak mencukupi. Tersisa: {$snack->stock}"]);
                }

                $subtotal = $snack->selling_price * $qty;
                $totalAmount += $subtotal;

                SnackTransactionDetail::create([
                    'snack_transaction_id' => $snackTrx->id,
                    'snack_id' => $snack->id,
                    'quantity' => $qty,
                    'price_at_time' => $snack->selling_price,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok
                $snack->decrement('stock', $qty);
            }

            $snackTrx->update(['total_amount' => $totalAmount]);
            DB::commit();

            return redirect()->route('pos.index')->with('success', "Transaksi berhasil! Kode: {$transactionCode} | Total: Rp " . number_format($totalAmount, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
