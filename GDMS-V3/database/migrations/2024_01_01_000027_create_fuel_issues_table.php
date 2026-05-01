<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_issues', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('asset_id')->constrained('fuel_assets')->onDelete('restrict');
            $table->enum('fuel_type', ['diesel', 'petrol']);
            $table->decimal('litres', 10, 2)->default(0);
            $table->integer('odometer_km')->nullable();
            $table->string('issued_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_issues');
    }
};