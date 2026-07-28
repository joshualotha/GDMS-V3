<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fuel used to be issued to a standalone "fuel asset" record. Vehicles are now
     * represented as car-type Outlets (each linked to its own depreciable CompanyAsset),
     * so fuel issues should reference the outlet directly instead of a separate table.
     */
    public function up(): void
    {
        Schema::table('fuel_issues', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
        });

        DB::statement('ALTER TABLE fuel_issues CHANGE asset_id outlet_id BIGINT UNSIGNED NOT NULL');

        Schema::table('fuel_issues', function (Blueprint $table) {
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_issues', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
        });

        DB::statement('ALTER TABLE fuel_issues CHANGE outlet_id asset_id BIGINT UNSIGNED NOT NULL');

        Schema::table('fuel_issues', function (Blueprint $table) {
            $table->foreign('asset_id')->references('id')->on('fuel_assets')->onDelete('restrict');
        });
    }
};
