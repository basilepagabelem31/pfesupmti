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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string("nom") ;
            $table->text("prenom") ;
            $table->string("email")->unique() ;
            $table->text("password") ;
            $table->string("cin")->unique() ;
            $table->text("code")->unique() ;
            $table->string("telephone") ;
            $table->text("adresse") ;
            $table->string("universite") ;
            $table->text("faculte") ;
            $table->text("titre_formation") ;





            $table->unsignedBigInteger('pays_id');
            $table->foreign('pays_id')->references('id')->on('pays')->onDelete('cascade');


            $table->unsignedBigInteger('ville_id');
            $table->foreign('ville_id')->references('id')->on('villes')->onDelete('cascade');


            $table->unsignedBigInteger('groupe_id');
            $table->foreign('groupe_id')->references('id')->on('groupes')->onDelete('cascade');


            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');


            $table->unsignedBigInteger('statut_id');
            $table->foreign('statut_id')->references('id')->on('statuts')->onDelete('cascade');


            $table->unsignedBigInteger('email_log_id');
            $table->foreign('email_log_id')->references('id')->on('email_logs')->onDelete('cascade');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
