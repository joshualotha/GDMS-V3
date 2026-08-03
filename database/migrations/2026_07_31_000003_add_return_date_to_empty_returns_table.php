<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empty_returns', function (Blueprint $table) {
            $table->date('return_date')->nullable()->after('return_number');
        });

        DB::table('empty_returns')->update(['return_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('empty_returns', function (Blueprint $table) {
            $table->dropColumn('return_date');
        });
    }
};
