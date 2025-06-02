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
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->string("nom");

            $table->unsignedBigInteger('pays_id');
            $table->foreign('pays_id')->references('id')->on('pays')->onDelete('cascade');

            // Contrainte d'unicité sur 'nom' et 'pays_id'
            $table->unique(['nom', 'pays_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villes');
    }
};
