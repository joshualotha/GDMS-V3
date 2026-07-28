<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Maintenance is now recorded as a regular Expense optionally tagged with an
     * asset_id, instead of a separate (and never actually functional - the model's
     * fillable fields didn't even match these columns) maintenance log table.
     */
    public function up(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }

    public function down(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->date('service_date');
            $table->string('maintenance_type');
            $table->text('description')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('service_provider')->nullable();
            $table->date('next_service_date')->nullable();
            $table->timestamps();
        });
    }
};
