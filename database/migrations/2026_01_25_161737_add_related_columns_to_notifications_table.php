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
      Schema::table('notifications', function (Blueprint $table) {
        // Assuming related_id refers to an ID (usually unsignedBigInteger)
        // nullable() allows the notification to exist without a relation if needed
        $table->unsignedBigInteger('related_id')->nullable()->after('type');
        $table->string('related_type')->nullable()->after('related_id');

        // Optional: Add an index for faster lookups
        $table->index(['related_id', 'related_type']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     Schema::table('notifications', function (Blueprint $table) {
        $table->dropColumn(['related_id', 'related_type']);
    });
    }
};
