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
        Schema::create('shift_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('shift_type', ['pagi', 'malam']);
            $table->decimal('system_cash', 12, 2)->default(0);
            $table->decimal('system_transfer', 12, 2)->default(0);
            $table->decimal('real_cash', 12, 2)->nullable();
            $table->decimal('real_transfer', 12, 2)->nullable();
            $table->decimal('difference_cash', 12, 2)->nullable();
            $table->decimal('difference_transfer', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['date', 'shift_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_reconciliations');
    }
};
