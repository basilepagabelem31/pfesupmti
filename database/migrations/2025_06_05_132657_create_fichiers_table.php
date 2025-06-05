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
        Schema::create('fichiers', function (Blueprint $table) {
            $table->id(); // id_fichier

            $table->string('nom_fichier'); // Correspond à 'titre' dans votre version
            $table->text('description')->nullable(); // Rendre nullable si la description n'est pas toujours obligatoire
            $table->string('url_fichier'); // Correspond à 'chemin' dans votre version

            // Relations avec les utilisateurs
            $table->foreignId('id_stagiaire')->constrained('users')->onDelete('cascade'); // L'utilisateur propriétaire du fichier (le stagiaire)
            $table->foreignId('id_superviseur_televerseur')->nullable()->constrained('users')->onDelete('set null'); // L'utilisateur qui a téléversé (peut être nul si auto-upload ou superviseur supprimé)

            // NOUVEAUX CHAMPS BASÉS SUR LE CAHIER DES CHARGES
            $table->boolean('peut_modifier')->default(true); // Par défaut, le stagiaire peut modifier ses fichiers
            $table->boolean('peut_supprimer')->default(true); // Par défaut, le stagiaire peut supprimer ses fichiers
            $table->string('type_fichier'); // 'convention', 'rapport', 'attestation', etc.

            // Votre champ ajouté (maintenu car potentiellement pertinent pour un rapport de stage)
            $table->foreignId('sujet_id')->nullable()->constrained('sujets')->onDelete('set null'); // Rendre nullable et onDelete('set null') si le sujet peut être dissocié ou supprimé

            $table->timestamps(); // Pour created_at (date_televersement) et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichiers');
    }
};