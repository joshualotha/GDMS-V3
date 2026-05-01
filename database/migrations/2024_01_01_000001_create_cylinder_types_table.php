<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cylinder_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('size_kg');
            $table->decimal('full_sale_cost', 12, 2)->default(0);
            $table->decimal('full_sale_price', 12, 2)->default(0);
            $table->decimal('refill_cost', 12, 2)->default(0);
            $table->decimal('refill_price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cylinder_types');
    }
};