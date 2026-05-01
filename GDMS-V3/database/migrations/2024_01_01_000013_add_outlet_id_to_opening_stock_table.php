<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opening_stock', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('opening_stock', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
    }
};