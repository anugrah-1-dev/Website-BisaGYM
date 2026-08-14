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
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('locked_package_id')->nullable()->constrained('gym_packages')->nullOnDelete()->after('status');
            $table->decimal('locked_price', 12, 2)->nullable()->after('locked_package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['locked_package_id']);
            $table->dropColumn(['locked_package_id', 'locked_price']);
        });
    }
};
