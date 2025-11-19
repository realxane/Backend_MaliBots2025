<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            // PK UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // Données
            $table->string('nom', 150);

            // Index utile pour les recherches par nom
            $table->index('nom');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};