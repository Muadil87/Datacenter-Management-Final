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
        // Convert ENUM column to VARCHAR to allow 'user' role without enum constraints
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) DEFAULT 'internal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM (note: may lose 'user' role data)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'internal') DEFAULT 'internal'");
    }
};
