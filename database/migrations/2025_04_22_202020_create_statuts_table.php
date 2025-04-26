<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <- N'oublie pas cette ligne pour utiliser DB

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuts', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->text("description");
            $table->timestamps();
        });

        // Insertion des données par défaut
        DB::table('statuts')->insert([
            [
                'nom' => 'active',
                'description' => 'Ce statut signifie que l\'élément est actif.'
            ],
            [
                'nom' => 'desactive',
                'description' => 'Ce statut signifie que l\'élément est désactivé temporairement.'
            ],
            [
                'nom' => 'archive',
                'description' => 'Ce statut signifie que l\'élément est archivé ou obsolète.'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuts');
    }
};
