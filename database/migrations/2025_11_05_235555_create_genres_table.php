<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            // PK UUID (HasUuids + $incrementing=false + $keyType='string')
            $table->uuid('id')->primary();

            // Nom du genre
            $table->string('nom', 100)->unique();

            // Pas de timestamps (conforme au modèle)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};