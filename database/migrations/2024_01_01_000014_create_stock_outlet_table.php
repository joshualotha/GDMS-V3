<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('cylinder_type_id')->constrained()->onDelete('cascade');
            $table->integer('full_qty')->default(0);
            $table->integer('empty_qty')->default(0);
            $table->timestamps();
            $table->unique(['outlet_id', 'cylinder_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outlet');
    }
};