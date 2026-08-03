<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->integer('cylinders_sold')->nullable()->after('basic_salary');
            $table->decimal('commission_rate', 10, 2)->nullable()->after('cylinders_sold');
            $table->integer('commission_target')->nullable()->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn(['cylinders_sold', 'commission_rate', 'commission_target']);
        });
    }
};
