<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('approved_at');
        });

        DB::table('payroll_periods')->where('status', 'paid')->update(['paid_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });
    }
};
