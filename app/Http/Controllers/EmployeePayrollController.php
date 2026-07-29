<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use Illuminate\Http\Request;

class EmployeePayrollController extends Controller
{
    public function index(Employee $employee)
    {
        $payrolls = $employee->payrolls()->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        return view('employees.payrolls.index', compact('employee', 'payrolls'));
    }

    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|in:unpaid,paid',
            'notes' => 'nullable|string',
        ]);

        $allowances = $request->allowances ?? 0;
        $deductions = $request->deductions ?? 0;
        $total_salary = $request->base_salary + $allowances - $deductions;

        EmployeePayroll::updateOrCreate(
            ['employee_id' => $employee->id, 'month' => $request->month, 'year' => $request->year],
            [
                'base_salary' => $request->base_salary,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'total_salary' => $total_salary,
                'status' => $request->status,
                'notes' => $request->notes,
            ]
        );

        $statusText = $request->status === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR';
        \App\Models\ActivityLog::log('UPDATE', 'Penggajian', "Mencatat gaji karyawan: {$employee->name} (Bulan {$request->month}/{$request->year}) - Total Rp " . number_format($total_salary, 0, ',', '.') . " [$statusText]");

        return redirect()->back()->with('success', 'Data gaji berhasil disimpan!');
    }

    public function destroy(EmployeePayroll $payroll)
    {
        $desc = "Menghapus data gaji karyawan: {$payroll->employee->name} (Bulan {$payroll->month}/{$payroll->year})";
        $payroll->delete();

        \App\Models\ActivityLog::log('DELETE', 'Penggajian', $desc);

        return redirect()->back()->with('success', 'Data gaji berhasil dihapus!');
    }
}
