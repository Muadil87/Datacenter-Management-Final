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
    Schema::create('logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained(); 
        $table->string('action');      // <--- VÉRIFIE QUE CETTE LIGNE EST BIEN LÀ
        $table->text('details');     // <--- ET CELLE-LÀ
        $table->string('ip_address')->nullable(); 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
