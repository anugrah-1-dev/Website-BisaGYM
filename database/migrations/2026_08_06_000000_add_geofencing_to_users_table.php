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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_location_restricted')->default(false)->after('is_active');
            $table->decimal('allowed_latitude', 10, 7)->nullable()->after('is_location_restricted');
            $table->decimal('allowed_longitude', 10, 7)->nullable()->after('allowed_latitude');
            $table->integer('allowed_radius_meters')->nullable()->after('allowed_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_location_restricted',
                'allowed_latitude',
                'allowed_longitude',
                'allowed_radius_meters',
            ]);
        });
    }
};
