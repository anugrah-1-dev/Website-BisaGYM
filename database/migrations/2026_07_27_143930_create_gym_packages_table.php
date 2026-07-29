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
        Schema::create('gym_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration');
            $table->enum('duration_unit', ['hari', 'bulan', 'tahun']);
            $table->decimal('price', 10, 2);
            $table->enum('category', ['member', 'non-member', 'couple']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_packages');
    }
};
