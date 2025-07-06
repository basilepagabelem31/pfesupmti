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
        Schema::table('users', function (Blueprint $table) {
            // Supprimez d'abord la contrainte de clé étrangère
            $table->dropForeign(['email_log_id']);
            // Puis supprimez la colonne
            $table->dropColumn('email_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recréez la colonne et la contrainte si vous devez annuler la migration
            $table->unsignedBigInteger('email_log_id')->nullable();
            $table->foreign('email_log_id')->references('id')->on('email_logs')->onDelete('cascade');
        });
    }
};