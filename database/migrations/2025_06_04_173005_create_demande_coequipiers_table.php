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
        Schema::create('demande_coequipiers', function (Blueprint $table) {
            $table->id('id_demande'); // Clé primaire auto-incrémentée, nommée id_demande

            // Clé étrangère vers l'utilisateur (stagiaire) qui envoie la demande
            $table->foreignId('id_stagiaire_demandeur')
                  ->constrained('users') // S'assure que la table 'users' existe
                  ->onDelete('cascade'); // Supprime la demande si le demandeur est supprimé

            // Clé étrangère vers l'utilisateur (stagiaire) qui reçoit la demande
            $table->foreignId('id_stagiaire_receveur')
                  ->constrained('users') // S'assure que la table 'users' existe
                  ->onDelete('cascade'); // Supprime la demande si le receveur est supprimé

            // Statut de la demande avec les valeurs possibles et une valeur par défaut
            $table->enum('statut_demande', ['en_attente', 'acceptée', 'refusée'])->default('en_attente');

            // Date et heure de la demande, par défaut à l'heure actuelle de création
            $table->dateTime('date_demande')->useCurrent();

            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_coequipiers');
    }
};