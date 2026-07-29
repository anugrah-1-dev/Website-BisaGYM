<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->query('date', Carbon::today()->format('Y-m-d'));
        
        // Get attendances for selected date
        $attendances = MemberAttendance::with(['member', 'user'])
            ->whereDate('attendance_time', $selectedDate)
            ->latest('attendance_time')
            ->get();
            
        // Stats
        $totalSelectedDate = $attendances->count();
        
        $totalAllAttendances = MemberAttendance::count();
        $uniqueDays = DB::table('member_attendances')
            ->selectRaw('COUNT(DISTINCT DATE(attendance_time)) as count')
            ->value('count') ?: 1;
            
        $averageDaily = round($totalAllAttendances / $uniqueDays, 1);
            
        return view('attendance.index', compact('attendances', 'selectedDate', 'totalSelectedDate', 'averageDaily'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string'
        ]);

        $member = Member::where('member_id', $request->member_id)->first();

        if (!$member) {
            return back()->with('error', 'Member tidak ditemukan. Pastikan ID / Barcode benar.');
        }

        if ($member->status === 'pending') {
            return back()->with('error', 'Member ini belum aktif. Jika baru mendaftar, pastikan sudah melunasi pembayaran di Kasir.');
        }

        // Cek apakah punya tagihan belum dibayar
        $hasUnpaid = \App\Models\MemberTransaction::where('member_id', $member->id)
            ->where('payment_status', 'unpaid')
            ->exists();
            
        if ($hasUnpaid) {
            return back()->with('error', 'Member ini memiliki tagihan yang BELUM LUNAS. Absensi ditolak sebelum pembayaran diselesaikan di menu Kasir.');
        }

        if ($member->status === 'expired' || Carbon::now()->gt($member->expiry_date)) {
            return back()->with('error', 'Keanggotaan member ini sudah KEDALUWARSA pada ' . Carbon::parse($member->expiry_date)->format('d M Y') . '. Silakan perpanjang terlebih dahulu.');
        }

        // Cek apakah sudah absen hari ini untuk mencegah double scan
        $alreadyScanned = MemberAttendance::where('member_id', $member->id)
            ->whereDate('attendance_time', Carbon::today())
            ->exists();

        if ($alreadyScanned) {
            return back()->with('warning', 'Member ' . $member->name . ' sudah melakukan absensi hari ini.');
        }

        // Record attendance
        MemberAttendance::create([
            'member_id' => $member->id,
            'user_id' => Auth::id(), // Petugas yang scan
            'attendance_time' => Carbon::now()
        ]);

        return back()->with('success', 'Absensi berhasil untuk: ' . $member->name);
    }
}
