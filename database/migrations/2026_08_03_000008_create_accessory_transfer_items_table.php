<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessory_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessory_transfer_id')->constrained()->onDelete('cascade');
            $table->foreignId('accessory_id')->constrained()->onDelete('restrict');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessory_transfer_items');
    }
};
