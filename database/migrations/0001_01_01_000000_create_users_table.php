<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nom', 120);
            $table->string('email')->unique();
            $table->string('motDePasse', 255);

            $table->enum('role', ['Admin', 'Vendeur', 'Acheteur'])->default('Acheteur');

            $table->string('telephone', 30)->nullable()->unique();
            $table->string('telephone_pro', 30)->nullable();
            $table->string('descrit_ton_savoir_faire', 255)->nullable();

            $table->foreignUuid('regionId')
                ->constrained('regions', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->boolean('isActive')->default(true);

            $table->rememberToken(); // <— important pour l’auth "remember me"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};