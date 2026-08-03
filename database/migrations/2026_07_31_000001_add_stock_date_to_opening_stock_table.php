<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opening_stock', function (Blueprint $table) {
            $table->date('stock_date')->nullable()->after('reference');
        });

        DB::table('opening_stock')->update(['stock_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('opening_stock', function (Blueprint $table) {
            $table->dropColumn('stock_date');
        });
    }
};
