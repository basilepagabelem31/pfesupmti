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
         // 1. Supprimer la contrainte de clé étrangère
        Schema::table('reunions', function (Blueprint $table) {
            $table->dropForeign(['note']);
        });

        // 2. Modifier la colonne
        Schema::table('reunions', function (Blueprint $table) {
            $table->text('note')->nullable()->change();
        });
    }

    public function down()
    {
        // 1. Revenir à l'ancien type (supposé unsignedBigInteger)
        Schema::table('reunions', function (Blueprint $table) {
            $table->unsignedBigInteger('note')->nullable()->change();
        });

        // 2. Restaurer la contrainte de clé étrangère
        Schema::table('reunions', function (Blueprint $table) {
            $table->foreign('note')->references('id')->on('notes')->onDelete('cascade');
        });
    }
};
