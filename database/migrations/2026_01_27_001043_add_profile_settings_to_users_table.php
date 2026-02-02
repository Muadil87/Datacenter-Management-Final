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
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable();
            }
            if (!Schema::hasColumn('users', 'notification_email')) {
                $table->boolean('notification_email')->default(1);
            }
            if (!Schema::hasColumn('users', 'notification_incidents')) {
                $table->boolean('notification_incidents')->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_photo')) {
                $table->dropColumn('profile_photo');
            }
            if (Schema::hasColumn('users', 'notification_email')) {
                $table->dropColumn('notification_email');
            }
            if (Schema::hasColumn('users', 'notification_incidents')) {
                $table->dropColumn('notification_incidents');
            }
        });
    }
};
