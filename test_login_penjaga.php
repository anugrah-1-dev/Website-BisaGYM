<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::where('email', 'kasir@bisagym.com')->first();
    if (!$user) {
        echo "User penjaga not found\n";
        exit;
    }

    echo "Found user: " . $user->name . "\n";
    
    // Simulate what TwoFactorController@store does
    Illuminate\Support\Facades\Auth::login($user);
    echo "Logged in\n";

    $user->update([
        'two_factor_code' => null,
        'two_factor_expires_at' => null,
        'two_factor_verified_at' => now(),
    ]);
    echo "Updated user\n";
    
    echo "Success!\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
