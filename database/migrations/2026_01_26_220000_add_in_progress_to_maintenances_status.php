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
        // Modify the enum to include 'in_progress'
        Schema::table('maintenances', function (Blueprint $table) {
            // For MySQL, we need to modify the enum column
            DB::statement("ALTER TABLE maintenances MODIFY COLUMN status ENUM('planned', 'in_progress', 'cancelled', 'completed') DEFAULT 'planned'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        Schema::table('maintenances', function (Blueprint $table) {
            DB::statement("ALTER TABLE maintenances MODIFY COLUMN status ENUM('planned', 'cancelled', 'completed') DEFAULT 'planned'");
        });
    }
};
