<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan user_id di activity_logs nullable untuk mencatat aksi sistem/pendaftaran mandiri
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // 2. Ubah kolom role di users menjadi string (varchar 50) agar fleksibel dan tidak terjadi Data truncated warning
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('penjaga')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('penjaga')->change();
        });
    }
};

