<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            // Ajoute la colonne user_id
            // Utilisez ->nullable() si un log d'e-mail peut ne pas être lié à un utilisateur
            // Utilisez ->after('nom_colonne_existante') pour spécifier la position (facultatif)
            $table->unsignedBigInteger('user_id')->nullable()->after('error_message'); 
            
            // Ajoute la contrainte de clé étrangère
            // Assurez-vous que la table 'users' existe avant d'exécuter cette migration
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            // Supprime la contrainte de clé étrangère d'abord
            $table->dropForeign(['user_id']);
            // Puis supprime la colonne user_id
            $table->dropColumn('user_id');
        });
    }
};