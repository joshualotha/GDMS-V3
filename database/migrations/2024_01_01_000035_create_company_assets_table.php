<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->unique();
            $table->string('name');
            $table->foreignId('asset_category_id')->constrained()->onDelete('restrict');
            $table->string('serial_number')->nullable();
            $table->string('plate_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->default(0);
            $table->decimal('accumulated_depreciation', 12, 2)->default(0);
            $table->decimal('current_book_value', 12, 2)->default(0);
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->foreignId('assigned_to_outlet')->nullable()->constrained('outlets')->onDelete('set null');
            $table->foreignId('assigned_to_employee')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'under_maintenance', 'disposed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};