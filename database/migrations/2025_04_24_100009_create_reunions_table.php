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
        Schema::create('reunions', function (Blueprint $table) {
            $table->id();
            $table ->date("date");
            $table ->time("heure_debut");
            $table->time("heure_fin");
            $table->boolean("status")->default(false);
            $table -> foreignId("note")->constrained("notes")->onDelete("cascade");
            $table -> foreignId("groupe_id")->constrained("groupes")->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reunions');
    }
};
