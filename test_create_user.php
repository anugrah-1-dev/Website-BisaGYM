<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'username' => 'test' . rand(100, 999),
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
    ]);
    
    echo "User created successfully: " . $user->id . "\n";
    $user->assignRole('admin');
    echo "Role assigned successfully\n";
    $user->forceDelete();
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
