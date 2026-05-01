<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empty_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empty_return_id')->constrained('empty_returns')->onDelete('cascade');
            $table->foreignId('cylinder_type_id')->constrained()->onDelete('restrict');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empty_return_items');
    }
};