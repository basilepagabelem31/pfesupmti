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
        Schema::create('coequipiers', function (Blueprint $table) {
            // Clé étrangère vers le premier stagiaire
            $table->foreignId('id_stagiaire_1')
                  ->constrained('users') // S'assure que la table 'users' existe
                  ->onDelete('cascade'); // Supprime l'association si le stagiaire est supprimé

            // Clé étrangère vers le deuxième stagiaire
            $table->foreignId('id_stagiaire_2')
                  ->constrained('users') // S'assure que la table 'users' existe
                  ->onDelete('cascade'); // Supprime l'association si le stagiaire est supprimé

            $table->date('date_association'); // Date à laquelle les deux stagiaires sont devenus coéquipiers

            // Définit une clé primaire composite sur les deux IDs.
            // Cela garantit qu'une paire de stagiaires ne peut être coéquipière qu'une seule fois.
            $table->primary(['id_stagiaire_1', 'id_stagiaire_2']);

            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coequipiers');
    }
};