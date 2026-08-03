<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->date('adjustment_date')->nullable()->after('adjustment_number');
        });

        DB::table('stock_adjustments')->update(['adjustment_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn('adjustment_date');
        });
    }
};
