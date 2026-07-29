<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $attendances = EmployeeAttendance::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->get();

        return view('employee_attendances.index', compact('attendances', 'month', 'year'));
    }

    public function create(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $employees = Employee::where('status', 'active')->get();
        
        // Cek apakah sudah ada absen di tanggal ini
        $existingAttendances = EmployeeAttendance::where('date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('employee_attendances.create', compact('employees', 'date', 'existingAttendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa,Libur',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $date = $request->date;

        foreach ($request->attendances as $employee_id => $data) {
            EmployeeAttendance::updateOrCreate(
                [
                    'employee_id' => $employee_id,
                    'date' => $date
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes']
                ]
            );
        }

        \App\Models\ActivityLog::log('UPDATE', 'Absensi Karyawan', "Memperbarui data absensi karyawan secara massal untuk tanggal " . \Carbon\Carbon::parse($date)->format('d M Y'));

        return redirect()->route('employee-attendances.index')->with('success', 'Data absensi karyawan untuk tanggal ' . $date . ' berhasil disimpan!');
    }
}
