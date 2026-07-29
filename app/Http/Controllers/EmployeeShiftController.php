<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;

class EmployeeShiftController extends Controller
{
    public function index(Employee $employee)
    {
        $shifts = $employee->shifts()->orderBy('date', 'desc')->get();
        return view('employees.shifts.index', compact('employee', 'shifts'));
    }

    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'date' => 'required|date',
            'shift_type' => 'required|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'is_day_off' => 'nullable|boolean',
        ]);

        // If it's a day off, override start/end times
        if ($request->has('is_day_off') && $request->is_day_off) {
            $request->merge([
                'shift_type' => 'Libur',
                'start_time' => null,
                'end_time' => null,
            ]);
        }

        EmployeeShift::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $request->date],
            [
                'shift_type' => $request->shift_type,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_day_off' => $request->has('is_day_off'),
            ]
        );

        $shiftType = $request->has('is_day_off') ? 'Libur' : "{$request->shift_type} ({$request->start_time} - {$request->end_time})";
        \App\Models\ActivityLog::log('UPDATE', 'Jadwal Shift', "Mengatur jadwal shift karyawan: {$employee->name} untuk tanggal " . \Carbon\Carbon::parse($request->date)->format('d M Y') . " [$shiftType]");

        return redirect()->back()->with('success', 'Jadwal shift berhasil disimpan!');
    }

    public function destroy(EmployeeShift $shift)
    {
        $desc = "Menghapus jadwal shift karyawan: {$shift->employee->name} tanggal " . \Carbon\Carbon::parse($shift->date)->format('d M Y');
        $shift->delete();
        
        \App\Models\ActivityLog::log('DELETE', 'Jadwal Shift', $desc);

        return redirect()->back()->with('success', 'Jadwal shift berhasil dihapus!');
    }
}
