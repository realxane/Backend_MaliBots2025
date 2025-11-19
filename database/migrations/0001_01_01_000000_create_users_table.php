<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // PK en UUID pour coller au trait HasUuids et à $incrementing = false
            $table->uuid('id')->primary();

            // Colonnes métier
            $table->string('nom', 120);
            $table->string('email')->unique();
            $table->string('motDePasse', 255); // compatible avec le cast 'hashed'
            
            // Si votre enum App\Enums\Role est "string-backed"
            $table->enum('role', ['Admin', 'Vendeur', 'Acheteur'])->default('Acheteur');

            // Téléphone optionnel mais unique si renseigné
            $table->string('telephone', 30)->nullable()->unique();
             // Colonnes spécifiques pour vendeur/admin
             $table->string('telephone_pro', 30)->nullable();
            $table->string('descrit_ton_savoir_faire', 255)->nullable();

            // FK vers regions.id (supposé UUID). Non nul = un utilisateur a une région.
            $table->foreignUuid('regionId')
                  ->constrained('regions', 'id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete(); // empêche de supprimer une région référencée

            // Actif par défaut
            $table->boolean('isActive')->default(true);

            // Timestamps Eloquent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};