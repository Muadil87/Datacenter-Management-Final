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
        Schema::create('maintenances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
    $table->string('title');
    $table->dateTime('start_at');
    $table->dateTime('end_at');
    $table->unsignedBigInteger('created_by'); // logique
    $table->text('reason')->nullable();
    $table->enum('status',['scheduled','in_progress','completed','cancelled'])->default('scheduled');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
