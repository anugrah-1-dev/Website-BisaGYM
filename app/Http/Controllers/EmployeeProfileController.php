<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = \App\Models\Employee::with(['shifts' => function($q) {
            $q->orderBy('date', 'desc')->take(30);
        }, 'payrolls' => function($q) {
            $q->orderBy('year', 'desc')->orderBy('month', 'desc');
        }])->where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Akun ini tidak terhubung dengan data karyawan mana pun.');
        }

        return view('employees.my_profile', compact('employee'));
    }
}
