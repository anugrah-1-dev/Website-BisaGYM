<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('snacks', function (Blueprint $table) {
            if (!Schema::hasColumn('snacks', 'stock_gudang')) {
                $table->integer('stock_gudang')->default(0)->after('category');
            }
            if (!Schema::hasColumn('snacks', 'stock_kulkas')) {
                $table->integer('stock_kulkas')->default(0)->after('stock_gudang');
            }
        });

        // Copy existing stock value to stock_gudang if stock_gudang is 0
        DB::statement("UPDATE snacks SET stock_gudang = stock WHERE stock_gudang = 0 AND stock > 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snacks', function (Blueprint $table) {
            $table->dropColumn(['stock_gudang', 'stock_kulkas']);
        });
    }
};
