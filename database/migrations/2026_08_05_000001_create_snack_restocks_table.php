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
        Schema::create('snack_restocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('snack_id')->constrained('snacks')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('set null');
            $table->string('type')->default('incoming_supplier'); // 'incoming_supplier' or 'refill_kulkas'
            $table->integer('qty_gudang')->default(0);
            $table->integer('qty_kulkas')->default(0);
            $table->decimal('capital_price', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('restock_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snack_restocks');
    }
};
