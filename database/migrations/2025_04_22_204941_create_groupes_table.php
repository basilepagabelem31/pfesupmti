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
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique(); // Code unique comme demandé
            $table->string("nom");
            $table->text("description");
            $table->date("jour"); // Date du jour
            $table->time("heure_debut"); // Heure de début
            $table->time("heure_fin");   // Heure de fin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};
