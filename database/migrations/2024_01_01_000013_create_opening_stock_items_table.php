<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opening_stock_id')->constrained('opening_stock')->onDelete('cascade');
            $table->foreignId('cylinder_type_id')->constrained()->onDelete('cascade');
            $table->integer('full_qty')->default(0);
            $table->integer('empty_qty')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_stock_items');
    }
};