<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_received', function (Blueprint $table) {
            $table->date('received_date')->nullable()->after('grn_number');
        });

        DB::table('goods_received')->update(['received_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('goods_received', function (Blueprint $table) {
            $table->dropColumn('received_date');
        });
    }
};
