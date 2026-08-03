<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            // Kept separate from the manually-editable `deductions` field so that editing
            // one never silently overwrites cylinder-loss deductions applied by the system.
            $table->decimal('loss_deductions', 10, 2)->default(0)->after('deductions');
            $table->text('loss_deduction_note')->nullable()->after('deduction_note');
        });

        // Existing loss deductions were stored in the plain `deductions` field before this
        // split existed — move them over so they stay protected from future manual edits.
        DB::table('payroll_items')
            ->where('deduction_note', 'like', 'Cylinder loss:%')
            ->update([
                'loss_deductions' => DB::raw('deductions'),
                'loss_deduction_note' => DB::raw('deduction_note'),
                'deductions' => 0,
                'deduction_note' => null,
            ]);
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn(['loss_deductions', 'loss_deduction_note']);
        });
    }
};
