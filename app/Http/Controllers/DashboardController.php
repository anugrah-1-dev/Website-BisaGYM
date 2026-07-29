<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberTransaction;
use App\Models\SnackTransaction;
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

        return view('dashboard', compact(
            'activeMembersCount',
            'totalIncomeThisMonth',
            'memberVisitsToday',
            'employeePresentToday',
            'recentTransactions',
            'recentVisits'
        ));
    }
}
