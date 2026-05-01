<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cylinder_type_id')->constrained()->onDelete('cascade');
            $table->decimal('full_sale_cost', 12, 2);
            $table->decimal('full_sale_price', 12, 2);
            $table->decimal('refill_cost', 12, 2);
            $table->decimal('refill_price', 12, 2);
            $table->timestamp('effective_from');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};