<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_main_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessory_id')->constrained()->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->timestamps();
            $table->unique('accessory_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_main_accessories');
    }
};
