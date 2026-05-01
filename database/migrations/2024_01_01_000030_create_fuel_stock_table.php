<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_stock', function (Blueprint $table) {
            $table->id();
            $table->enum('fuel_type', ['diesel', 'petrol'])->unique();
            $table->decimal('litres', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_stock');
    }
};