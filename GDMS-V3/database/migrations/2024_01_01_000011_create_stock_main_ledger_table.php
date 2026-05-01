<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_main_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cylinder_type_id')->constrained()->onDelete('cascade');
            $table->integer('full_qty_change')->default(0);
            $table->integer('empty_qty_change')->default(0);
            $table->integer('full_qty_after')->default(0);
            $table->integer('empty_qty_after')->default(0);
            $table->string('transaction_type');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_main_ledger');
    }
};