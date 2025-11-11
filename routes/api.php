<?php

use App\Http\Controllers\Api\Vendeur\ProduitController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'checkrole:vendeur'])->prefix('vendeur')->group(function () {
    Route::apiResource('produits', ProduitController::class);
    // Restauration d’un produit soft-deleted
    Route::patch('produits/{id}/restore', [ProduitController::class, 'restore'])
        ->name('produits.restore');
});

use App\Http\Controllers\PaiementController;

Route::get('/paiements', [PaiementController::class, 'index']);
Route::get('/paiements/{id}', [PaiementController::class, 'show']);
Route::post('/paiements', [PaiementController::class, 'store']);
Route::put('/paiements/{id}', [PaiementController::class, 'update']);
Route::delete('/paiements/{id}', [PaiementController::class, 'destroy']);

use App\Http\Controllers\CommandeController;

Route::prefix('commandes')->group(function () {
    Route::get('/', [CommandeController::class, 'index']);
    Route::get('/{id}', [CommandeController::class, 'show']);
    Route::post('/', [CommandeController::class, 'store']);
    Route::patch('/{id}/statut', [CommandeController::class, 'updateStatut']);
    Route::delete('/{id}', [CommandeController::class, 'destroy']);
});

use App\Http\Controllers\ProverbeController;
use App\Http\Controllers\ConteController;

//Route::middleware(['auth:sanctum'])->group(function () {
    // Proverbes
    Route::get('/proverbes', [ProverbeController::class, 'index']);
    Route::get('/proverbes/{id}', [ProverbeController::class, 'show']);
    Route::post('/proverbes', [ProverbeController::class, 'store']);
    Route::put('/proverbes/{id}', [ProverbeController::class, 'update']);
    Route::delete('/proverbes/{id}', [ProverbeController::class, 'destroy']);

    // Contes
    Route::get('/contes', [ConteController::class, 'index']);
    Route::get('/contes/{id}', [ConteController::class, 'show']);
    Route::post('/contes', [ConteController::class, 'store']);
    Route::put('/contes/{id}', [ConteController::class, 'update']);
    Route::delete('/contes/{id}', [ConteController::class, 'destroy']);

