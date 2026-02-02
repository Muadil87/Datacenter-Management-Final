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
    Schema::table('users', function (Blueprint $table) {
        // Make password optional
        $table->string('password')->nullable()->change();
        // Add columns for Google/GitHub IDs
        $table->string('google_id')->nullable()->after('email');
        $table->string('github_id')->nullable()->after('google_id');
        // Save the social token (optional but useful)
        $table->string('social_token')->nullable()->after('github_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
