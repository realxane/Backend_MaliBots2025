<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('imageUrl')->nullable()->change();
        });
    }
    public function down(): void {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('imageUrl')->nullable(false)->change();
        });
    }
};