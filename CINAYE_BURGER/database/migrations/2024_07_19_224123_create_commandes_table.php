<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('client_nom');
            $table->string('client_prenom');
            $table->string('client_telephone');
            $table->foreignId('burger_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 8, 2);
            $table->string('etat');
            $table->timestamp('date_commande');
            $table->timestamp('date_paiement')->nullable(); // Champ optionnel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
