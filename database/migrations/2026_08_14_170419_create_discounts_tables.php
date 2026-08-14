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
        // 1. Create discounts table
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('percentage');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Create pivot table for discounts and gym_packages
        Schema::create('discount_gym_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->foreignId('gym_package_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Drop discount_percentage from gym_packages (since we added it in a previous migration)
        if (Schema::hasColumn('gym_packages', 'discount_percentage')) {
            Schema::table('gym_packages', function (Blueprint $table) {
                $table->dropColumn('discount_percentage');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('gym_packages', 'discount_percentage')) {
            Schema::table('gym_packages', function (Blueprint $table) {
                $table->integer('discount_percentage')->default(0)->after('price');
            });
        }

        Schema::dropIfExists('discount_gym_package');
        Schema::dropIfExists('discounts');
    }
};
