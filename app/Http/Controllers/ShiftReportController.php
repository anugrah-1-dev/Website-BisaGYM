<?php

namespace App\Http\Controllers;

use App\Models\MemberTransaction;
use App\Models\SnackTransaction;
use App\Models\ShiftReconciliation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);

        // Shift Time Ranges
        // Shift Pagi: 23:00:00 (hari sebelumnya) - 14:59:59
        $pagiStart = $carbonDate->copy()->subDay()->setTime(23, 0, 0);
        $pagiEnd   = $carbonDate->copy()->setTime(14, 59, 59);

        // Shift Malam: 15:00:00 - 22:59:59
        $malamStart = $carbonDate->copy()->setTime(15, 0, 0);
        $malamEnd   = $carbonDate->copy()->setTime(22, 59, 59);

        // --- Calculate System Totals for Shift Pagi ---
        $pagiMemberCash = MemberTransaction::where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$pagiStart, $pagiEnd])
            ->where(function($q) {
                $q->whereNull('payment_method')->orWhere('payment_method', 'cash');
            })->sum('amount');

        $pagiMemberTransfer = MemberTransaction::where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$pagiStart, $pagiEnd])
            ->whereIn('payment_method', ['transfer', 'qris', 'debit'])
            ->sum('amount');

        $pagiSnackCash = SnackTransaction::whereBetween('transaction_date', [$pagiStart, $pagiEnd])
            ->where(function($q) {
                $q->whereNull('payment_method')->orWhere('payment_method', 'cash');
            })->sum('total_amount');

        $pagiSnackTransfer = SnackTransaction::whereBetween('transaction_date', [$pagiStart, $pagiEnd])
            ->whereIn('payment_method', ['transfer', 'qris', 'debit'])
            ->sum('total_amount');

        $pagiSystemCash = $pagiMemberCash + $pagiSnackCash;
        $pagiSystemTransfer = $pagiMemberTransfer + $pagiSnackTransfer;

        // --- Calculate System Totals for Shift Malam ---
        $malamMemberCash = MemberTransaction::where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$malamStart, $malamEnd])
            ->where(function($q) {
                $q->whereNull('payment_method')->orWhere('payment_method', 'cash');
            })->sum('amount');

        $malamMemberTransfer = MemberTransaction::where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$malamStart, $malamEnd])
            ->whereIn('payment_method', ['transfer', 'qris', 'debit'])
            ->sum('amount');

        $malamSnackCash = SnackTransaction::whereBetween('transaction_date', [$malamStart, $malamEnd])
            ->where(function($q) {
                $q->whereNull('payment_method')->orWhere('payment_method', 'cash');
            })->sum('total_amount');

        $malamSnackTransfer = SnackTransaction::whereBetween('transaction_date', [$malamStart, $malamEnd])
            ->whereIn('payment_method', ['transfer', 'qris', 'debit'])
            ->sum('total_amount');

        $malamSystemCash = $malamMemberCash + $malamSnackCash;
        $malamSystemTransfer = $malamMemberTransfer + $malamSnackTransfer;

        // Fetch Reconciliation records for this date
        $reconciliationPagi = ShiftReconciliation::with('user')
            ->where('date', $date)
            ->where('shift_type', 'pagi')
            ->first();

        $reconciliationMalam = ShiftReconciliation::with('user')
            ->where('date', $date)
            ->where('shift_type', 'malam')
            ->first();

        return view('reports.shift', compact(
            'date',
            'pagiSystemCash', 'pagiSystemTransfer', 'reconciliationPagi',
            'malamSystemCash', 'malamSystemTransfer', 'reconciliationMalam'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'          => 'required|date',
            'shift_type'    => 'required|in:pagi,malam',
            'real_cash'     => 'required|numeric|min:0',
            'real_transfer' => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        $carbonDate = Carbon::parse($request->date);

        if ($request->shift_type === 'pagi') {
            $start = $carbonDate->copy()->subDay()->setTime(23, 0, 0);
            $end   = $carbonDate->copy()->setTime(14, 59, 59);
        } else {
            $start = $carbonDate->copy()->setTime(15, 0, 0);
            $end   = $carbonDate->copy()->setTime(22, 59, 59);
        }

        // Recalculate system totals to guarantee accurate math
        $memberCash = MemberTransaction::where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$start, $end])
            ->where(function($q) {
                $q->whereNull('payment_method')->orWhere('payment_method', 'cash');
            })->sum('amount');

        $memberTransfer = MemberTransaction::where('payment_status', 'paid')
            ->whereBetween('transaction_date', [$start, $end])
            ->whereIn('payment_method', ['transfer', 'qris', 'debit'])
            ->sum('amount');

        $snackCash = SnackTransaction::whereBetween('transaction_date', [$start, $end])
            ->where(function($q) {
                $q->whereNull('payment_method')->orWhere('payment_method', 'cash');
            })->sum('total_amount');

        $snackTransfer = SnackTransaction::whereBetween('transaction_date', [$start, $end])
            ->whereIn('payment_method', ['transfer', 'qris', 'debit'])
            ->sum('total_amount');

        $systemCash = $memberCash + $snackCash;
        $systemTransfer = $memberTransfer + $snackTransfer;
        
        $diffCash = $request->real_cash - $systemCash;
        $diffTransfer = $request->real_transfer - $systemTransfer;

        ShiftReconciliation::updateOrCreate(
            [
                'date'       => $request->date,
                'shift_type' => $request->shift_type,
            ],
            [
                'system_cash'         => $systemCash,
                'system_transfer'     => $systemTransfer,
                'real_cash'           => $request->real_cash,
                'real_transfer'       => $request->real_transfer,
                'difference_cash'     => $diffCash,
                'difference_transfer' => $diffTransfer,
                'notes'               => $request->notes,
                'user_id'             => Auth::id(),
            ]
        );

        $shiftLabel = ucfirst($request->shift_type);
        ActivityLog::log(
            'UPDATE', 
            'Laporan Shift', 
            "Menginput Uang Real (Cash & Transfer) Shift {$shiftLabel} tanggal " . $carbonDate->format('d M Y') . " (Real Cash: Rp " . number_format($request->real_cash, 0, ',', '.') . ", Real Transfer: Rp " . number_format($request->real_transfer, 0, ',', '.') . ")"
        );

        return redirect()->back()->with('success', "Laporan Uang Real (Cash & Transfer) Shift {$shiftLabel} berhasil disimpan.");
    }
}
