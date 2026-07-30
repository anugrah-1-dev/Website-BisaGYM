<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Carbon greaterThan with string...\n";
    $result = now()->greaterThan("2026-07-30 10:00:00");
    echo "Success: " . ($result ? 'true' : 'false') . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
