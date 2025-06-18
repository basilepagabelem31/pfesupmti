<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     * Cette méthode est appelée lorsque vous exécutez `php artisan migrate`.
     */
    public function up(): void
    {
        // Utilise Schema::table pour modifier une table existante
        Schema::table('groupes', function (Blueprint $table) {
            // Supprime les colonnes spécifiées
            $table->dropColumn(['jour', 'heure_debut', 'heure_fin']);
        });
    }

    /**
     * Annule les migrations.
     * Cette méthode est appelée lorsque vous exécutez `php artisan migrate:rollback` pour cette migration spécifique.
     */
    public function down(): void
    {
        // Pour pouvoir annuler cette migration, nous devons rajouter les colonnes
        // Note: Il est important de les recréer avec les mêmes types si vous avez besoin de rollback.
        Schema::table('groupes', function (Blueprint $table) {
            $table->date("jour")->nullable(); // Ajout de nullable() car la colonne aurait pu contenir des nulls
            $table->time("heure_debut")->nullable(); // Ajout de nullable()
            $table->time("heure_fin")->nullable();   // Ajout de nullable()
        });
    }
};
