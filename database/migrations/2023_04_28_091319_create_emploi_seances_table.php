<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emploi_seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emploi_temps_id')->constrained('emploi_temps')->cascadeOnDelete();
            $table->foreignId('session_matiere_id')->constrained('session_matieres')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emploi_seances');
    }
};
