<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\EmployeePayroll;
use App\Models\MemberTransaction;
use App\Models\SnackTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        // Income from Member Transactions (Gym Packages)
        $memberIncome = MemberTransaction::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('payment_status', 'paid')
            ->sum('amount');

        // Income from Snack POS
        $snackIncome = SnackTransaction::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total_amount');

        $totalIncome = $memberIncome + $snackIncome;

        // Expenses from Employee Payrolls
        $payrollExpense = EmployeePayroll::where('year', $year)
            ->where('month', $month)
            ->where('status', 'paid')
            ->sum('total_salary');

        // General Operational Expenses
        $generalExpense = Expense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        $totalExpense = $payrollExpense + $generalExpense;

        $netProfit = $totalIncome - $totalExpense;

        // Get detailed operational expenses for table
        $expensesDetail = Expense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->get();

        // Get detailed payrolls for table
        $payrollsDetail = EmployeePayroll::with('employee')
            ->where('year', $year)
            ->where('month', $month)
            ->where('status', 'paid')
            ->get();

        return view('reports.finance', compact(
            'month', 'year', 
            'memberIncome', 'snackIncome', 'totalIncome',
            'payrollExpense', 'generalExpense', 'totalExpense',
            'netProfit',
            'expensesDetail', 'payrollsDetail'
        ));
    }
}
