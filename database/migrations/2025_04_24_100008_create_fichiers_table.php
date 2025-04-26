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
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->string('chemin');
            
            // Déclaration des clés étrangères avec contraintes
            $table->foreignId('user_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('sujet_id')->constrained('sujets')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('utilisateurs')->onDelete('cascade');

            $table->timestamps();
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
