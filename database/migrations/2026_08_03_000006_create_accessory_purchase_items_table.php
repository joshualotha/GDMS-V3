<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessory_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessory_purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('accessory_id')->constrained()->onDelete('restrict');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessory_purchase_items');
    }
};
