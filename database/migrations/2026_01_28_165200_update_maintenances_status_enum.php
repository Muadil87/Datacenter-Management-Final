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
        // Update existing 'planned' values to 'scheduled'
        DB::table('maintenances')
            ->where('status', 'planned')
            ->update(['status' => 'scheduled']);

        // Modify the enum column to accept new values
        Schema::table('maintenances', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])
                  ->default('scheduled')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes
        DB::table('maintenances')
            ->where('status', 'scheduled')
            ->update(['status' => 'planned']);

        Schema::table('maintenances', function (Blueprint $table) {
            $table->enum('status', ['planned', 'cancelled', 'completed'])
                  ->default('planned')
                  ->change();
        });
    }
};
