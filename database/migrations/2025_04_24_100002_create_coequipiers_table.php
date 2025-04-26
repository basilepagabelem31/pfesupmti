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
            $table->id();
            $table->string('code_team')->unique();
            $table->dateTime('join_date');
            $table->boolean('is_creator')->default(false);
            $table->foreignId('stagiaire_id')->constrained('utilisateurs')->onDelete('cascade'); // Déclaration avant timestamps
            $table->timestamps();

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
