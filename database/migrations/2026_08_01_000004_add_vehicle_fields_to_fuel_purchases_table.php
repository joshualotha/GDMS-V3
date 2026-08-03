<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuel_purchases', function (Blueprint $table) {
            // Nullable: existing bulk (warehouse-pool) purchases predate this and have no single vehicle.
            $table->foreignId('outlet_id')->nullable()->after('date')->constrained('outlets')->onDelete('restrict');
            $table->integer('odometer_km')->nullable()->after('outlet_id');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_purchases', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn(['outlet_id', 'odometer_km']);
        });
    }
};
