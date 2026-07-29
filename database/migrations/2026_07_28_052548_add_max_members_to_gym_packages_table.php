<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_packages', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_members')->default(1)->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('gym_packages', function (Blueprint $table) {
            $table->dropColumn('max_members');
        });
    }
};
