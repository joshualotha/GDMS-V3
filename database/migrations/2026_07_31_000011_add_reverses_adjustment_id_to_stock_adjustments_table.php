<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreignId('reverses_adjustment_id')->nullable()->after('id')->constrained('stock_adjustments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['reverses_adjustment_id']);
            $table->dropColumn('reverses_adjustment_id');
        });
    }
};
