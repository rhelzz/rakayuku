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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique()->comment('Auto-generated: SO-YYYY-MM-NNN');
            $table->date('opname_date');
            $table->enum('status', ['DRAFT', 'COMPLETED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->index('opname_date');
            $table->index(['status', 'created_at']);
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->decimal('system_qty', 15, 2)->default(0)->comment('Qty in system at opname time');
            $table->decimal('actual_qty', 15, 2)->default(0)->comment('Qty physically counted');
            $table->decimal('difference', 15, 2)->default(0)->comment('actual - system');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['stock_opname_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};
