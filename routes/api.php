<?php

use Illuminate\Support\Facades\Route;


// paiements
use App\Http\Controllers\PaiementController;

use App\Http\Controllers\ProverbeController;
use App\Http\Controllers\ConteController;

// Vendeur
use App\Http\Controllers\Api\Vendeur\ProduitController;

// Acheteur
use App\Http\Controllers\Api\Acheteur\PanierController;
use App\Http\Controllers\Api\Acheteur\PanierItemController;
//Favoris
use App\Http\Controllers\Api\FavoriController;
use App\Http\Controllers\Api\Musique\MusiqueController;
use App\Http\Controllers\Api\galerie\PhotoController;

// commandes
use App\Http\Controllers\CommandeController;

//Commentaires
use App\Http\Controllers\Api\Commentaire\CommentaireController;

Route::get('/paiements', [PaiementController::class, 'index']);
Route::get('/paiements/{id}', [PaiementController::class, 'show']);
Route::post('/paiements', [PaiementController::class, 'store']);
Route::put('/paiements/{id}', [PaiementController::class, 'update']);
Route::delete('/paiements/{id}', [PaiementController::class, 'destroy']);

Route::prefix('commandes')->group(function () {
    Route::get('/', [CommandeController::class, 'index']);
    Route::get('/{id}', [CommandeController::class, 'show']);
    Route::post('/', [CommandeController::class, 'store']);
    Route::patch('/{id}/statut', [CommandeController::class, 'updateStatut']);
    Route::delete('/{id}', [CommandeController::class, 'destroy']);
});

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
// Lecture publique
Route::apiResource('produits.commentaires', CommentaireController::class)
    ->shallow()
    ->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {

    // Vendeur
    Route::prefix('vendeur')->as('vendeur.')->middleware('checkrole:vendeur')->group(function () {
        Route::apiResource('produits', ProduitController::class);
        // Restauration d’un produit soft-deleted
        Route::patch('produits/{id}/restore', [ProduitController::class, 'restore'])
            ->name('produits.restore');
    });

    //Commentaires 

    // Écriture protégée
    Route::apiResource('produits.commentaires', CommentaireController::class)
        ->only(['store', 'update', 'destroy']);

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

//admins
Route::prefix('musiques')->group(function () {
    Route::get('/', [MusiqueController::class, 'index']);  // Public : lister toutes les musiques
    Route::post('/admin', [MusiqueController::class, 'store']);// Admin : publier une musique
    Route::delete('/admin/{id}', [MusiqueController::class, 'destroy']);// Admin : supprimer une musique
});

//NB:La bonne manière mais comme j'a pas un token pour admin je vais laisser apres si je récupere pour GD
// Route::prefix('musiques')->group(function () {
//     Route::get('/', [MusiqueController::class, 'index']);           // liste publique
//     Route::middleware('auth:sanctum')->group(function () {
//         Route::post('/admin', [MusiqueController::class, 'store']);   // publier
//         Route::delete('/admin/{id}', [MusiqueController::class, 'destroy']); // supprimer
//     });
// });

//photos
Route::prefix('photos')->group(function () {
    Route::get('/', [PhotoController::class, 'index']);         // lister
    Route::get('/{id}', [PhotoController::class, 'show']);      // voir une publication

    // admin (on peux ajouter middleware auth plus tard)
    Route::post('/admin', [PhotoController::class, 'store']);   // publier (multipart form-data)
    Route::post('/admin/{id}', [PhotoController::class, 'update']); 
    Route::delete('/admin/{id}', [PhotoController::class, 'destroy']);
    Route::delete('/admin/image/{imageId}', [PhotoController::class, 'deleteImage']); // supprimer image
});

// endpoint regions
Route::get('/regions', function() {
    return \App\Models\Region::all();
});