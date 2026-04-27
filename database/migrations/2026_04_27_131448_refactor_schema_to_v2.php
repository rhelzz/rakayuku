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
        // 1. Refactor Materials
        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('qty', 'current_qty');
        });

        // 2. Refactor Purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->renameColumn('receipt_number', 'invoice_number');
            $table->renameColumn('total_amount', 'total_price');
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
            $table->string('supplier_name')->nullable()->after('id');
        });

        // 3. Refactor Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('deal_price', 'selling_price');
            $table->renameColumn('production_status', 'status');
        });

        // 4. Refactor Order Materials
        Schema::table('order_materials', function (Blueprint $table) {
            $table->renameColumn('qty', 'qty_used');
            $table->renameColumn('unit_price', 'price_snapshot');
        });

        // 5. Refactor Production Costs
        Schema::table('production_costs', function (Blueprint $table) {
            $table->renameColumn('category', 'type');
        });

        // 6. Refactor Stock Movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('price_snapshot', 15, 2)->nullable()->after('qty');
        });

        // 7. Create Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['DP', 'FINAL']);
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('price_snapshot');
        });

        Schema::table('production_costs', function (Blueprint $table) {
            $table->renameColumn('type', 'category');
        });

        Schema::table('order_materials', function (Blueprint $table) {
            $table->renameColumn('qty_used', 'qty');
            $table->renameColumn('price_snapshot', 'unit_price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('selling_price', 'deal_price');
            $table->renameColumn('status', 'production_status');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->renameColumn('invoice_number', 'receipt_number');
            $table->renameColumn('total_price', 'total_amount');
            $table->dropColumn('supplier_name');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('current_qty', 'qty');
        });
    }
};
