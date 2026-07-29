<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'base_salary' => 'required|numeric|min:0',
            'join_date' => 'nullable|date',
            
            // Login account fields
            'create_login' => 'nullable|boolean',
            'email' => 'required_with:create_login|nullable|email|unique:users,email',
            'password' => 'required_with:create_login|nullable|min:6',
        ]);

        $userId = null;
        if ($request->has('create_login') && $request->create_login) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => explode('@', $request->email)[0],
                'password' => Hash::make($request->password),
                'role' => 'kasir', // Penjaga / Kasir
            ]);
            $user->assignRole('kasir');
            $userId = $user->id;
        }

        $employee = Employee::create([
            'user_id' => $userId,
            'name' => $request->name,
            'phone' => $request->phone,
            'position' => $request->position,
            'base_salary' => $request->base_salary,
            'join_date' => $request->join_date,
            'status' => 'active',
        ]);

        $logDesc = "Menambahkan karyawan baru: {$employee->name} ({$employee->position})";
        if ($userId) $logDesc .= " beserta akses login (Email: {$request->email})";
        \App\Models\ActivityLog::log('CREATE', 'Manajemen Karyawan', $logDesc);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'base_salary' => 'required|numeric|min:0',
            'join_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        $oldStatus = $employee->status;
        $oldSalary = $employee->base_salary;

        $employee->update($request->only([
            'name', 'phone', 'position', 'base_salary', 'join_date', 'status'
        ]));

        if ($employee->user) {
            $employee->user->update([
                'name' => $request->name,
                // Assuming is_active might be in user table or just handle role
            ]);
        }

        $desc = "Memperbarui data karyawan {$employee->name}";
        if ($oldStatus != $employee->status) $desc .= " (Status dari $oldStatus ke {$employee->status})";
        if ($oldSalary != $employee->base_salary) $desc .= " (Gaji dari Rp " . number_format($oldSalary, 0, ',', '.') . " ke Rp " . number_format($employee->base_salary, 0, ',', '.') . ")";
        \App\Models\ActivityLog::log('UPDATE', 'Manajemen Karyawan', $desc);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        if ($employee->user) {
            $employee->user->delete();
        }
        $employee->delete();

        \App\Models\ActivityLog::log('DELETE', 'Manajemen Karyawan', "Menghapus karyawan: {$name}");

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus!');
    }
}
