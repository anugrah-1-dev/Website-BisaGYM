<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeInfoController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->with(['shifts', 'payrolls'])->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki data karyawan.');
        }

        return view('employee-info.index', compact('employee'));
    }
}
