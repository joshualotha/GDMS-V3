<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->decimal('expected_amount', 12, 2)->default(0);
            $table->decimal('submitted_amount', 12, 2)->default(0);
            $table->decimal('variance', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'queried', 'rejected'])->default('pending');
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_submissions');
    }
};