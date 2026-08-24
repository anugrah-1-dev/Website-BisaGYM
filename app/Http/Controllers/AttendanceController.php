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
            
        $uniqueWeeks = DB::table('member_attendances')
            ->selectRaw('COUNT(DISTINCT YEARWEEK(attendance_time, 1)) as count')
            ->value('count') ?: 1;
            
        $uniqueMonths = DB::table('member_attendances')
            ->selectRaw('COUNT(DISTINCT DATE_FORMAT(attendance_time, "%Y-%m")) as count')
            ->value('count') ?: 1;
            
        $averageDaily = round($totalAllAttendances / $uniqueDays, 1);
        $averageWeekly = round($totalAllAttendances / $uniqueWeeks, 1);
        $averageMonthly = round($totalAllAttendances / $uniqueMonths, 1);
            
        return view('attendance.index', compact('attendances', 'selectedDate', 'totalSelectedDate', 'averageDaily', 'averageWeekly', 'averageMonthly'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string'
        ]);

        $identifier = $request->identifier;
        $member = Member::where('member_id', $identifier)
            ->orWhere('name', 'like', "%{$identifier}%")
            ->first();

        if (!$member) {
            return back()->with('error', 'Member tidak ditemukan. Pastikan ID / Nama / Barcode benar.');
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

        $today = Carbon::today();
        $expiry = Carbon::parse($member->expiry_date)->startOfDay();
        $daysLeft = (int) $today->diffInDays($expiry, false);

        $scannedData = [
            'name' => $member->name,
            'member_id' => $member->member_id,
            'member_type' => $member->member_type,
            'photo_path' => $member->photo_path ? asset('storage/' . $member->photo_path) : null,
            'expiry_date' => Carbon::parse($member->expiry_date)->format('d M Y'),
            'days_left' => $daysLeft,
            'scan_time' => Carbon::now()->format('H:i:s'),
        ];

        if ($alreadyScanned) {
            $scannedData['already_scanned'] = true;
            return back()
                ->with('warning', 'Member ' . $member->name . ' sudah melakukan absensi hari ini.')
                ->with('scanned_member', $scannedData);
        }

        // Record attendance
        MemberAttendance::create([
            'member_id' => $member->id,
            'user_id' => Auth::id(), // Petugas yang scan
            'attendance_time' => Carbon::now()
        ]);

        $statusMsg = 'Absensi berhasil untuk: ' . $member->name . ' (Masa aktif tersisa ' . max(0, $daysLeft) . ' hari)';

        return back()
            ->with('success', $statusMsg)
            ->with('scanned_member', $scannedData);
    }
}
