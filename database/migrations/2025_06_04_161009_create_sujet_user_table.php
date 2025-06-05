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
        Schema::create('sujet_user', function (Blueprint $table) {
            $table->id(); // Une clé primaire auto-incrémentée pour la table pivot
            // Clé étrangère vers la table 'sujets'
            $table->foreignId('sujet_id')->constrained()->onDelete('cascade');
            // Clé étrangère vers la table 'users'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Ajoutez des colonnes supplémentaires si vous avez besoin d'informations
            // sur l'attribution (ex: date_attribution, status, etc.)
            $table->timestamps(); // Pour 'created_at' et 'updated_at' de l'attribution

            // Optionnel: Empêche un même utilisateur d'être attribué plusieurs fois au même sujet
            $table->unique(['sujet_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sujet_user');
    }
};