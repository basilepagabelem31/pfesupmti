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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_email'); // L'email du destinataire
            $table->string('subject');  // Sujet de l'email
            $table->longText('body');  // Contenu de l'email
            $table->string('status'); // Statut de l'envoi (ex: 'sent', 'failed', 'queued')
            $table->text('error_message')->nullable(); // Message d'erreur si échec
            // $table->date("date"); // Retiré car created_at suffit
            $table->unsignedBigInteger('email_template_id')->nullable(); // Rendu nullable si non toujours lié
            $table->foreign('email_template_id')->references('id')->on('email_templates')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
