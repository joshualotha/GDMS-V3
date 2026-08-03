<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_employee']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreign('assigned_to_employee')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_employee']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreign('assigned_to_employee')->references('id')->on('users')->onDelete('set null');
        });
    }
};
