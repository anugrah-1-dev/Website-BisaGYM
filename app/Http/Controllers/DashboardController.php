<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberTransaction;
use App\Models\SnackTransaction;
use App\Models\SnackTransactionDetail;
use App\Models\MemberAttendance;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // 1. Total Member Aktif (expiry_date >= hari ini)
        $activeMembersCount = Member::where('expiry_date', '>=', $today->format('Y-m-d'))
                                    ->count();

        // 2. Pendapatan Bulan Ini (Member + Snack)
        $memberIncome = MemberTransaction::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->where('payment_status', 'paid')
            ->sum('amount');
            
        $snackIncome = SnackTransaction::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->sum('total_amount');
            
        $totalIncomeThisMonth = $memberIncome + $snackIncome;

        // 3. Kunjungan Member Hari Ini
        $memberVisitsToday = MemberAttendance::whereDate('attendance_time', $today)->count();

        // 4. Kehadiran Karyawan Hari Ini
        $employeePresentToday = EmployeeAttendance::where('date', $today->format('Y-m-d'))
                                                ->where('status', 'Hadir')
                                                ->count();

        // Widget: 5 Transaksi Member Terbaru
        $recentTransactions = MemberTransaction::with(['member', 'package'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Widget: 5 Kunjungan Terakhir
        $recentVisits = MemberAttendance::with('member')
            ->orderBy('attendance_time', 'desc')
            ->take(5)
            ->get();

        // Data Grafik: Kehadiran Member per Bulan (Tahun Ini)
        $attendanceMonthly = MemberAttendance::select(
                DB::raw('MONTH(attendance_time) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('attendance_time', $thisYear)
            ->groupBy(DB::raw('MONTH(attendance_time)'))
            ->pluck('total', 'month')
            ->toArray();

        // Data Grafik: Member Baru vs Perpanjang per Bulan (Tahun Ini)
        $newMembersMonthly = MemberTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('transaction_date', $thisYear)
            ->where('transaction_type', 'new')
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->pluck('total', 'month')
            ->toArray();

        $renewalMembersMonthly = MemberTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('transaction_date', $thisYear)
            ->where('transaction_type', 'renewal')
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->pluck('total', 'month')
            ->toArray();

        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $attendanceChartData = [];
        $newMembersChartData = [];
        $renewalMembersChartData = [];

        for ($m = 1; $m <= 12; $m++) {
            $attendanceChartData[] = $attendanceMonthly[$m] ?? 0;
            $newMembersChartData[] = $newMembersMonthly[$m] ?? 0;
            $renewalMembersChartData[] = $renewalMembersMonthly[$m] ?? 0;
        }

        // Data Grafik: Penjualan Snack Terlaris
        $topSnacks = SnackTransactionDetail::join('snacks', 'snack_transaction_details.snack_id', '=', 'snacks.id')
            ->select(
                'snacks.name',
                DB::raw('SUM(snack_transaction_details.quantity) as total_qty'),
                DB::raw('SUM(snack_transaction_details.subtotal) as total_sales')
            )
            ->groupBy('snacks.id', 'snacks.name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        $topSnackLabels = $topSnacks->pluck('name')->toArray();
        $topSnackData = $topSnacks->pluck('total_qty')->map(fn($val) => (int)$val)->toArray();
        $topSnackSales = $topSnacks->pluck('total_sales')->map(fn($val) => (float)$val)->toArray();

        return view('dashboard', compact(
            'activeMembersCount',
            'totalIncomeThisMonth',
            'memberVisitsToday',
            'employeePresentToday',
            'recentTransactions',
            'recentVisits',
            'chartMonths',
            'attendanceChartData',
            'newMembersChartData',
            'renewalMembersChartData',
            'topSnackLabels',
            'topSnackData',
            'topSnackSales'
        ));
    }
}
