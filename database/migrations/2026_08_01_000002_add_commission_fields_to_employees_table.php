<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('pay_type', ['salary', 'commission'])->default('salary')->after('basic_salary');
            $table->decimal('commission_rate', 10, 2)->nullable()->after('pay_type');
            $table->integer('commission_target')->nullable()->default(1250)->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['pay_type', 'commission_rate', 'commission_target']);
        });
    }
};
