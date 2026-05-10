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
        Schema::create('order_residues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_material_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['REUSABLE', 'RECYCLE', 'WASTE']);
            $table->decimal('qty', 15, 2);
            $table->decimal('price_snapshot', 15, 2);
            $table->decimal('reduction_value', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_residues');
    }
};
