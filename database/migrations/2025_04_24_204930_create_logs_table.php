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
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade"); // Utilisateur qui a effectué l'action
            $table->string('action'); // Description de l'action (e.g., 'user_created', 'user_updated', 'user_deleted')
            $table->string('log_message'); // Message descriptif de l'action (e.g., "User John Doe created")
            $table->json('object_snapshot')->nullable(); // Snapshot de l'objet avant/après modification, en JSON
            $table->timestamps(); // created_at pour la date du log
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
