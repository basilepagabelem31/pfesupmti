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
    Schema::table('groupes', function (Blueprint $table) {
     $table->id();
     $table->string('description')->nullable();
     $table->string('nom')->nullable();
    $table->string('jour')->nullable();
    $table->time('heure_debut')->nullable();
    $table->time('heure_fin')->nullable();
    $table->string('code')->unique();
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
