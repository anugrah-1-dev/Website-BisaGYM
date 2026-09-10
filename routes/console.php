<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduler: Auto-expire member yang sudah melewati tanggal expiry ──
// Dijalankan setiap hari pada pukul 00:05 WIB
Schedule::command('members:expire')->dailyAt('00:05');
