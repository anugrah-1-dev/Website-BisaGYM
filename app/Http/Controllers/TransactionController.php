<?php

namespace App\Http\Controllers;

use App\Models\MemberTransaction;
use App\Models\SnackTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'member');
        $dateFrom = $request->get('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));

        $memberTransactions = MemberTransaction::with(['member', 'package', 'user'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest('transaction_date')
            ->get();

        $snackTransactions = SnackTransaction::with(['user', 'details.snack'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest('transaction_date')
            ->get();

        $memberTotal = $memberTransactions->where('payment_status', 'paid')->where('payment_method', '!=', 'gratis')->sum('amount');
        $snackTotal = $snackTransactions->sum('total_amount');

        return view('transactions.index', compact(
            'type', 'dateFrom', 'dateTo',
            'memberTransactions', 'snackTransactions',
            'memberTotal', 'snackTotal'
        ));
    }
    public function export(Request $request)
    {
        $type = $request->get('type', 'member');
        $dateFrom = $request->get('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));

        if ($type === 'member') {
            $transactions = MemberTransaction::with(['member', 'package', 'user'])
                ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->latest('transaction_date')
                ->get();
        } else {
            $transactions = SnackTransaction::with(['user', 'details.snack'])
                ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->latest('transaction_date')
                ->get();
        }

        $fileName = 'laporan_transaksi_' . $type . '_' . $dateFrom . '_' . $dateTo . '.xls';

        return response()->view('exports.transactions', compact('type', 'dateFrom', 'dateTo', 'transactions'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function destroy($id)
    {
        $transaction = MemberTransaction::findOrFail($id);
        
        if ($transaction->transaction_type === 'renewal' && $transaction->payment_status === 'paid') {
            $member = $transaction->member;
            $package = $transaction->package;
            
            // revert expiry date
            if ($member && $package) {
                $currentExpiry = \Carbon\Carbon::parse($member->expiry_date);
                if ($package->duration_unit === 'hari')       $currentExpiry->subDays($package->duration);
                elseif ($package->duration_unit === 'bulan')  $currentExpiry->subMonths($package->duration);
                elseif ($package->duration_unit === 'tahun')  $currentExpiry->subYears($package->duration);
                
                $status = 'active';
                if (now()->greaterThan($currentExpiry)) {
                    $status = 'expired';
                }
                
                $member->update([
                    'expiry_date' => $currentExpiry,
                    'status' => $status,
                    'extension_count' => max(0, $member->extension_count - 1),
                ]);

                // revert linked member if applicable
                if ($package->max_members >= 2 && $member->linked_member_id) {
                    $linkedMember = $member->linkedMember;
                    if ($linkedMember) {
                        $linkedCurrentExpiry = \Carbon\Carbon::parse($linkedMember->expiry_date);
                        if ($package->duration_unit === 'hari')       $linkedCurrentExpiry->subDays($package->duration);
                        elseif ($package->duration_unit === 'bulan')  $linkedCurrentExpiry->subMonths($package->duration);
                        elseif ($package->duration_unit === 'tahun')  $linkedCurrentExpiry->subYears($package->duration);
                        
                        $linkedStatus = 'active';
                        if (now()->greaterThan($linkedCurrentExpiry)) {
                            $linkedStatus = 'expired';
                        }
                        
                        $linkedMember->update([
                            'expiry_date' => $linkedCurrentExpiry,
                            'status' => $linkedStatus,
                            'extension_count' => max(0, $linkedMember->extension_count - 1),
                        ]);
                    }
                }
            }
        }
        
        $transaction->delete();
        
        \App\Models\ActivityLog::log('DELETE', 'Transaksi', "Menghapus transaksi: {$transaction->transaction_code}");
        
        return back()->with('success', 'Transaksi berhasil dihapus dan data perpanjangan (jika ada) telah dikembalikan.');
    }

    public function update(Request $request, $id)
    {
        $transaction = MemberTransaction::findOrFail($id);

        $request->validate([
            'amount'         => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid',
            'payment_method' => 'nullable|string',
        ]);

        $oldAmount = $transaction->amount;
        $oldStatus = $transaction->payment_status;
        
        $transaction->update([
            'amount'         => $request->amount,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
        ]);

        \App\Models\ActivityLog::log('UPDATE', 'Transaksi', "Edit transaksi {$transaction->transaction_code}: Nominal lama Rp " . number_format($oldAmount, 0, ',', '.') . " menjadi Rp " . number_format($request->amount, 0, ',', '.') . ", Status: {$oldStatus} -> {$request->payment_status}");

        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }
}
