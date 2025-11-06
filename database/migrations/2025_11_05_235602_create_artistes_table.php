<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artistes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nom', 150);
            $table->text('bio')->nullable();
            $table->string('region', 100)->nullable();

            // Pas de timestamps car $timestamps = false dans le modèle
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artistes');
    }
};