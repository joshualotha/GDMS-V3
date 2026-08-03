<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_main_ledger', function (Blueprint $table) {
            $table->date('movement_date')->nullable()->after('cylinder_type_id');
        });

        DB::table('stock_main_ledger')->update(['movement_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('stock_main_ledger', function (Blueprint $table) {
            $table->dropColumn('movement_date');
        });
    }
};
