<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'checkrole:vendeur'])->prefix('vendeur')->group(function () {
    Route::apiResource('produits', ProduitController::class);
    // Restauration d’un produit soft-deleted
    Route::patch('produits/{id}/restore', [ProduitController::class, 'restore'])
        ->name('produits.restore');
});

// paiements
use App\Http\Controllers\PaiementController;

Route::get('/paiements', [PaiementController::class, 'index']);
Route::get('/paiements/{id}', [PaiementController::class, 'show']);
Route::post('/paiements', [PaiementController::class, 'store']);
Route::put('/paiements/{id}', [PaiementController::class, 'update']);
Route::delete('/paiements/{id}', [PaiementController::class, 'destroy']);

// commandes
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
//});

// Vendeur
use App\Http\Controllers\Api\Vendeur\ProduitController;

// Acheteur
use App\Http\Controllers\Api\Acheteur\PanierController;
use App\Http\Controllers\Api\Acheteur\PanierItemController;
//Favoris
use App\Http\Controllers\Api\FavoriController;

Route::middleware('auth:sanctum')->group(function () {

    // Vendeur
    Route::prefix('vendeur')->as('vendeur.')->middleware('checkrole:vendeur')->group(function () {
        Route::apiResource('produits', ProduitController::class);
        // Restauration d’un produit soft-deleted
        Route::patch('produits/{id}/restore', [ProduitController::class, 'restore'])
            ->name('produits.restore');
    });

    // Acheteur
    Route::prefix('acheteur')->as('acheteur.')->middleware('checkrole:acheteur')->group(function () {
        Route::get('panier', [PanierController::class, 'show'])->name('panier.show');
        Route::delete('panier', [PanierController::class, 'clear'])->name('panier.clear');

        Route::post('panier/items', [PanierItemController::class, 'store'])->name('panier.items.store');
        Route::patch('panier/items/{item}', [PanierItemController::class, 'update'])->name('panier.items.update');
        Route::delete('panier/items/{item}', [PanierItemController::class, 'destroy'])->name('panier.items.destroy');
    });

    //Favoris
    Route::get('favoris', [FavoriController::class, 'index']);
    Route::post('favoris', [FavoriController::class, 'store']);
    Route::post('favoris/toggle', [FavoriController::class, 'toggle']);
    Route::delete('favoris/{favori}', [FavoriController::class, 'destroy']);
});

   //Signalement
   use App\Http\Controllers\SignalementController;

    Route::get('/signalements', [SignalementController::class, 'index']);
    Route::get('/signalements/{id}', [SignalementController::class, 'show']);
    Route::post('/signalements', [SignalementController::class, 'store']);
    Route::put('/signalements/{id}', [SignalementController::class, 'mettreAJourStatut']);
    Route::delete('/signalements/{id}', [SignalementController::class, 'destroy']);

