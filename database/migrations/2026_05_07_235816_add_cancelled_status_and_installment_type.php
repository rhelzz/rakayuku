<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('PENDING', 'IN_PRODUCTION', 'DELIVERING', 'UNPAID_DELIVERED', 'FINISHED', 'CANCELLED') DEFAULT 'PENDING'");

        DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('DP', 'FINAL', 'INSTALLMENT') NOT NULL");

        Schema::table('orders', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('payment_status');
            $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('notes')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('PENDING', 'IN_PRODUCTION', 'DELIVERING', 'UNPAID_DELIVERED', 'FINISHED') DEFAULT 'PENDING'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('DP', 'FINAL') NOT NULL");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'cancelled_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
