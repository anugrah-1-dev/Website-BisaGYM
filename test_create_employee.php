<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

try {
    $userId = null;
    $create_login = true;
    $name = 'Test Employee ' . rand(1, 100);
    $email = 'testemployee'.rand(1, 100).'@example.com';
    $password = 'password123';
    $phone = '1234567890';
    $position = 'Staff';
    $base_salary = 1000000;
    $join_date = '2026-07-30';

    if ($create_login) {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => explode('@', $email)[0],
            'password' => Hash::make($password),
            'role' => 'kasir',
        ]);
        $user->assignRole('kasir');
        $userId = $user->id;
    }

    $employee = Employee::create([
        'user_id' => $userId,
        'name' => $name,
        'phone' => $phone,
        'position' => $position,
        'base_salary' => $base_salary,
        'join_date' => $join_date,
        'status' => 'active',
    ]);

    $logDesc = "Menambahkan karyawan baru: {$employee->name} ({$employee->position})";
    if ($userId) $logDesc .= " beserta akses login (Email: {$email})";
    
    // Fake login
    auth()->loginUsingId(1); // Assuming user 1 exists

    ActivityLog::log('CREATE', 'Manajemen Karyawan', $logDesc);

    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
