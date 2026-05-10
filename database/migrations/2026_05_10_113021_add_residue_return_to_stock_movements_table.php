<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite does not support modifying ENUM directly, but since we are likely on MySQL:
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('IN', 'OUT', 'ADJUSTMENT', 'RESIDUE_RETURN') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('IN', 'OUT', 'ADJUSTMENT') NOT NULL");
    }
};
