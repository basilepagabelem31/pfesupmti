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
        Schema::create('sujets', function (Blueprint $table) {
            $table->id();
            $table->string("titre");
            $table->text("description");
        
            $table->foreignId("promotion_id")->constrained("promotions")->onDelete("cascade");
            $table->foreignId("groupe_id")->constrained("groupes")->onDelete("cascade");
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sujets');
    }
};
