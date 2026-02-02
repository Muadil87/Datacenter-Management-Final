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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        // Ajout des colonnes pour ton projet
        $table->enum('role', ['admin', 'manager', 'internal'])->default('internal');
        $table->boolean('is_active')->default(false); // Pour l'activation par l'admin
        $table->string('profile_photo')->nullable(); // Photo de profil
        $table->boolean('notification_email')->default(1); // Notifications par email
        $table->boolean('notification_incidents')->default(1); // Notifications incidents
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
