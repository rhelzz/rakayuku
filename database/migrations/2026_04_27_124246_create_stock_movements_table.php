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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT', 'RESIDUE_RETURN']);
            $table->decimal('piece_count', 15, 2)->default(0)->nullable();
            $table->decimal('length', 15, 2)->default(0)->nullable();
            $table->decimal('width', 15, 2)->default(0)->nullable();
            $table->decimal('qty', 15, 2);
            $table->decimal('price_snapshot', 15, 2)->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
