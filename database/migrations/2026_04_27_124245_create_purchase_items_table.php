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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->decimal('piece_count', 15, 2)->default(0)->comment('Quantity in pieces/sheets');
            $table->decimal('length', 15, 2)->default(0)->nullable();
            $table->decimal('width', 15, 2)->default(0)->nullable();
            $table->decimal('thickness', 15, 2)->default(0)->nullable();
            $table->decimal('qty', 15, 2)->comment('Total quantity (e.g. Total Meters or Total Pcs if no dimension)');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
