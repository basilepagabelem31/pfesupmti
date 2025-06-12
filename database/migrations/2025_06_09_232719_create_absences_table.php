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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->enum('statut', ['Présent', 'Assisté', 'Absent'])->default('absent');
            $table->text('note')->nullable();//par le superviseur 
            $table->foreignId('stagiaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reunion_id')->constrained('reunions')->onDelete('cascade');
            $table->foreignId('valide_par')->nullable()->constrained('users'); // superviseur ayant validé
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
