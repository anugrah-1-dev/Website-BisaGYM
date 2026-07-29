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
        Schema::table('member_transactions', function (Blueprint $table) {
            $table->integer('discount_percentage')->default(0)->after('amount');
            $table->decimal('admin_fee', 10, 2)->default(0)->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_transactions', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'admin_fee']);
        });
    }
};
