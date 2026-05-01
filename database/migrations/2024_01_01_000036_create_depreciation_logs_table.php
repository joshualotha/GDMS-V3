<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('book_value_before', 12, 2)->default(0);
            $table->decimal('depreciation_rate', 5, 2)->default(0);
            $table->decimal('depreciation_amount', 12, 2)->default(0);
            $table->decimal('book_value_after', 12, 2)->default(0);
            $table->string('run_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciation_logs');
    }
};