<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->date('transfer_date')->nullable()->after('transfer_number');
        });

        DB::table('stock_transfers')->update(['transfer_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn('transfer_date');
        });
    }
};
