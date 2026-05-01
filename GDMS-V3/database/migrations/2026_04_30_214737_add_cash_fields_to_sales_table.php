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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('cash_submitted', 12, 2)->nullable()->after('total_gross_profit');
            $table->date('cash_submitted_date')->nullable()->after('cash_submitted');
            $table->unsignedBigInteger('cash_submitted_by')->nullable()->after('cash_submitted_date');
            $table->decimal('cash_variance', 12, 2)->nullable()->after('cash_submitted_by');
            $table->string('cash_receipt_image')->nullable()->after('cash_variance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            //
        });
    }
};
