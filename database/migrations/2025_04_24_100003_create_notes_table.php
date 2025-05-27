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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table -> text("valeur");
            $table->enum("visibilite",
            [
                "all",
                "donneur",
                "donneur + stagiaire",
                "superviseurs- stagiaire"
            ])->default("all");
            $table ->datetime("date_note");
           

            $table->foreignId("stagiaire_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("donneur_id")->constrained("users")->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
