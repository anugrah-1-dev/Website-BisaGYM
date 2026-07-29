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

        $memberTotal = $memberTransactions->where('payment_status', 'paid')->sum('amount');
        $snackTotal = $snackTransactions->sum('total_amount');

        return view('transactions.index', compact(
            'type', 'dateFrom', 'dateTo',
            'memberTransactions', 'snackTransactions',
            'memberTotal', 'snackTotal'
        ));
    }
}
