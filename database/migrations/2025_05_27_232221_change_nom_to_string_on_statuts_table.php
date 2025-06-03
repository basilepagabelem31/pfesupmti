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
        Schema::table('statuts', function (Blueprint $table) {
            // Supprimez l'ancienne colonne ENUM
            $table->dropColumn('nom');
        });

        Schema::table('statuts', function (Blueprint $table) {
            // Ajoutez une nouvelle colonne 'nom' de type STRING (VARCHAR)
            // avec une longueur suffisante (par exemple, 50 caractères)
            $table->string('nom', 50)->nullable(false)->after('id'); // 'after('id')' pour la positionner
            // Si vous voulez conserver la description, vous devez la recréer ou faire attention à l'ordre.
            // Une approche plus simple pour juste changer le type :
            // $table->string('nom', 50)->change(); // Utilisez change() si vous êtes sûr que la BD le supporte pour ENUM -> STRING
            // Cependant, drop puis add est souvent plus sûr pour les changements de type ENUM.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuts', function (Blueprint $table) {
            // Pour annuler, supprimez la colonne string
            $table->dropColumn('nom');
        });

        Schema::table('statuts', function (Blueprint $table) {
            // Et recréez la colonne ENUM originale
            $table->enum("nom", [
                'Actif',
                'Abandonné',
                'Archivé',
                'Terminé'
            ])->default('Actif')->after('id'); // Assurez la même position
        });
    }
};