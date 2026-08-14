<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum role: tambah 'developer' dan 'kasir'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'penjaga', 'developer', 'kasir') DEFAULT 'penjaga'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'penjaga') DEFAULT 'penjaga'");
    }
};
