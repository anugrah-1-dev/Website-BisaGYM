<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public Self-Registration Member (Tanpa Login / Password)
Route::get('/pendaftaran', [\App\Http\Controllers\PublicRegistrationController::class, 'index'])->name('public.registration.index');
Route::post('/pendaftaran', [\App\Http\Controllers\PublicRegistrationController::class, 'store'])->name('public.registration.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/members/register-existing', [\App\Http\Controllers\MemberController::class, 'createExisting'])->name('members.create-existing');
    Route::post('/members/store-existing', [\App\Http\Controllers\MemberController::class, 'storeExisting'])->name('members.store-existing');
    Route::resource('members', \App\Http\Controllers\MemberController::class);
    Route::get('/members/{member}/ecard', [\App\Http\Controllers\MemberController::class, 'ecard'])->name('members.ecard');
    Route::post('/members/{member}/renewal', [\App\Http\Controllers\MemberController::class, 'renewal'])->name('members.renewal');

    Route::get('/cashier/member', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.member');
    Route::post('/cashier/pay/{transaction}', [\App\Http\Controllers\CashierController::class, 'pay'])->name('cashier.pay');
    Route::post('/cashier/pay-non-member', [\App\Http\Controllers\CashierController::class, 'payNonMember'])->name('cashier.pay-non-member');

    Route::resource('gym-packages', \App\Http\Controllers\GymPackageController::class)->middleware('role:admin|developer');
    Route::resource('discounts', \App\Http\Controllers\DiscountController::class)->middleware('role:admin|developer');

    Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');

    Route::post('/snacks/incoming', [\App\Http\Controllers\SnackController::class, 'storeIncoming'])->name('snacks.incoming')->middleware('role:admin|developer|kasir|penjaga');
    Route::post('/snacks/refill-kulkas', [\App\Http\Controllers\SnackController::class, 'refillKulkas'])->name('snacks.refill-kulkas')->middleware('role:admin|developer|kasir|penjaga');
    Route::resource('snacks', \App\Http\Controllers\SnackController::class)->middleware('role:admin|developer|kasir|penjaga');

    Route::get('/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export', [\App\Http\Controllers\TransactionController::class, 'export'])->name('transactions.export');

    // Laporan Keuangan & Pengeluaran (Accessible by kasir as requested)
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class)->only(['index', 'store', 'destroy'])->middleware('role:admin|developer|kasir|penjaga');
    Route::get('/financial-report', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('financial-report.index')->middleware('role:admin|developer|kasir|penjaga');
    Route::get('/shift-reports', [\App\Http\Controllers\ShiftReportController::class, 'index'])->name('shift-reports.index')->middleware('role:admin|developer|kasir|penjaga');
    Route::post('/shift-reports', [\App\Http\Controllers\ShiftReportController::class, 'store'])->name('shift-reports.store')->middleware('role:admin|developer|kasir|penjaga');

    // Halaman Informasi Pekerjaan untuk karyawan yang login
    Route::get('/my-employee-info', [\App\Http\Controllers\EmployeeInfoController::class, 'index'])->name('employee.my-info');

    Route::middleware('role:developer')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
        Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::middleware('role:admin|developer')->group(function () {
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
        Route::get('employees/{employee}/shifts', [\App\Http\Controllers\EmployeeShiftController::class, 'index'])->name('employees.shifts.index');
        Route::post('employees/{employee}/shifts', [\App\Http\Controllers\EmployeeShiftController::class, 'store'])->name('employees.shifts.store');
        Route::delete('employees/shifts/{shift}', [\App\Http\Controllers\EmployeeShiftController::class, 'destroy'])->name('employees.shifts.destroy');
        
        Route::get('employees/{employee}/payrolls', [\App\Http\Controllers\EmployeePayrollController::class, 'index'])->name('employees.payrolls.index');
        Route::post('employees/{employee}/payrolls', [\App\Http\Controllers\EmployeePayrollController::class, 'store'])->name('employees.payrolls.store');
        Route::delete('employees/payrolls/{payroll}', [\App\Http\Controllers\EmployeePayrollController::class, 'destroy'])->name('employees.payrolls.destroy');

        // Absensi Karyawan Manual
        Route::resource('employee-attendances', \App\Http\Controllers\EmployeeAttendanceController::class)->only(['index', 'create', 'store']);
    });
});

require __DIR__.'/auth.php';
