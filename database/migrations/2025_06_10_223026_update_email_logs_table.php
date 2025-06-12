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
       Schema::table('email_logs', function (Blueprint $table) {
            // Ajouter absence_id avec clé étrangère
            $table->foreignId('absence_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            // Modifier le champ status pour ajouter la valeur par défaut (queued)
            $table->string('status')->default('queued')->change();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('email_logs', function (Blueprint $table) {
            // Annuler les changements
            $table->dropForeign(['absence_id']);
            $table->dropColumn('absence_id');

            // Remettre l'ancien champ status sans valeur par défaut
            $table->string('status')->change();
        });
    }
};
