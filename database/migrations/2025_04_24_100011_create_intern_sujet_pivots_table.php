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
        Schema::create('intern_sujet_pivots', function (Blueprint $table) {
            $table->id();
           

            $table -> foreignId("intern_id")->constrained("users")->onDelete("cascade");
            $table -> foreignId("sujet_id")->constrained("sujets")->onDelete("cascade");


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_sujet_pivots');
    }
};
