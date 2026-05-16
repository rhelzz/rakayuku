<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monthly_closings', function (Blueprint $table) {
            $table->id();
            $table->date('period')->unique()->comment('First day of month: YYYY-MM-01');
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
            $table->datetime('closed_at')->nullable();
            $table->string('closed_by')->nullable()->comment('Name of user who closed');
            $table->text('notes')->nullable();
            $table->json('snapshot')->nullable()->comment('Financial summary snapshot');
            $table->timestamps();

            $table->index(['status', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_closings');
    }
};
