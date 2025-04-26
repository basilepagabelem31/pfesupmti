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
        Schema::create('demande_coequipiers', function (Blueprint $table) {
            $table->foreignId("stagiaire_id")->constrained("utilisateurs")->onDelete("cascade");
            $table->foreignId("destinateur_id")->constrained("utilisateurs")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_coequipiers');
    }
};
