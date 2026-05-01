<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update any assets with 'under_maintenance' status to 'active'
        DB::table('assets')->where('status', 'under_maintenance')->update(['status' => 'active']);

        // Modify the ENUM column to remove 'under_maintenance'
        DB::statement("ALTER TABLE assets MODIFY COLUMN status ENUM('active', 'disposed') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE assets MODIFY COLUMN status ENUM('active', 'under_maintenance', 'disposed') NOT NULL DEFAULT 'active'");
    }
};
