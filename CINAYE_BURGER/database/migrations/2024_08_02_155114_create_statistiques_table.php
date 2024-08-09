<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('statistiques', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_commandes')->default(0);
            $table->integer('total_commandes_en_cours')->default(0);
            $table->integer('total_commandes_validees')->default(0);
            $table->integer('total_commandes_annulees')->default(0);
            $table->decimal('recettes_journalieres', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('statistiques');
    }
};