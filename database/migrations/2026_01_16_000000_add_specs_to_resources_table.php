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
        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'cpu_cores')) {
                $table->unsignedInteger('cpu_cores')->nullable();
            }
            if (!Schema::hasColumn('resources', 'ram_gb')) {
                $table->unsignedInteger('ram_gb')->nullable();
            }
            if (!Schema::hasColumn('resources', 'storage_gb')) {
                $table->unsignedInteger('storage_gb')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['cpu_cores', 'ram_gb', 'storage_gb']);
        });
    }
};
