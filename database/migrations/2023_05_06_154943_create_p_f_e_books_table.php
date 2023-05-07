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
        Schema::create('p_f_e_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societe_id')->constrained('societes')->cascadeOnDelete();
            $table->string('titre');
            $table->string('description')->nullable();
            $table->string('fichier');
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
        Schema::dropIfExists('p_f_e_books');
    }
};
