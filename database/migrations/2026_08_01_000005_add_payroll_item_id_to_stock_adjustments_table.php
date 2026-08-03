<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Null = an outlet "loss" not yet absorbed into any payroll item (either no
            // draft period exists for the responsible employee yet, or it isn't an
            // outlet loss at all). Set once the deduction has been applied.
            $table->foreignId('payroll_item_id')->nullable()->after('reverses_adjustment_id')
                ->constrained('payroll_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['payroll_item_id']);
            $table->dropColumn('payroll_item_id');
        });
    }
};
