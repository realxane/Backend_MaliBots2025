<?php

namespace App\Http\Controllers\Api\Acheteur;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StorePanierItemRequest;
use App\Http\Requests\UpdatePanierItemRequest;
use App\Http\Resources\PanierResource;
use App\Models\Panier;
use App\Models\PanierItem;
use App\Models\Produit;
use App\Enums\StatutProduit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PanierItemController extends Controller
{
    use AuthorizesRequests; 
    // POST /acheteur/panier/items
    public function store(StorePanierItemRequest $request)
    {
        $userId = $request->user()->id;
        $data = $request->validated();
        $quantite = (int)($data['quantite'] ?? 1);

        return DB::transaction(function () use ($userId, $data, $quantite) {

            $panier = Panier::firstOrCreate(['acheteurId' => $userId]);
            $this->authorize('update', $panier);

            $produit = Produit::query()
                ->where('id', $data['produitId'])
                ->firstOrFail();

            // Vérif "vendable"
            $statut = $produit->statut instanceof StatutProduit
                ? $produit->statut
                : StatutProduit::tryFrom((string)$produit->statut);

            if ($statut !== StatutProduit::Valide) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Ce produit n’est pas disponible à la vente.');
            }

            // Optionnel: check stock
            // if (isset($produit->stock) && $quantite > $produit->stock) {
            //     abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Quantité demandée supérieure au stock disponible.');
            // }

            // Prix unitaire "snapshot" pris sur le produit
            $prixUnitaire = $produit->prix ?? $produit->prixActuel ?? null; // AJUSTE ICI
            if ($prixUnitaire === null) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Le produit ne possède pas de prix.');
            }

            // lock pour éviter les doublons concurrentiels
            $item = PanierItem::where('panierId', $panier->id)
                ->where('produitId', $produit->id)
                ->lockForUpdate()
                ->first();

            if ($item) {
                $item->quantite = $item->quantite + $quantite;
                $item->save();
            } else {
                $item = PanierItem::create([
                    'panierId'     => $panier->id,
                    'produitId'    => $produit->id,
                    'quantite'     => $quantite,
                    'prixUnitaire' => $prixUnitaire,
                ]);
            }

            // Recharger le panier avec items + produits
            $panier->load(['items.produit']);

            return (new PanierResource($panier))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        });
    }

    // PATCH /acheteur/panier/items/{item}
    public function update(UpdatePanierItemRequest $request, string $itemId)
    {
        $userId = $request->user()->id;
        $quantite = (int) $request->validated()['quantite'];

        return DB::transaction(function () use ($userId, $itemId, $quantite) {

            $panier = Panier::firstOrCreate(['acheteurId' => $userId]);
            $this->authorize('update', $panier);

            $item = PanierItem::where('id', $itemId)
                ->where('panierId', $panier->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($quantite === 0) {
                $item->delete();
            } else {
                // Optionnel: vérifier stock ici aussi
                $item->update(['quantite' => $quantite]);
            }

            $panier->load(['items.produit']);

            return new PanierResource($panier);
        });
    }

    // DELETE /acheteur/panier/items/{item}
    public function destroy(Request $request, string $itemId)
    {
        $userId = $request->user()->id;

        $panier = Panier::where('acheteurId', $userId)->first();
        if (! $panier) {
            return response()->noContent();
        }

        $this->authorize('update', $panier);

        $deleted = PanierItem::where('id', $itemId)
            ->where('panierId', $panier->id)
            ->delete();

        if (! $deleted) {
            return response()->noContent();
        }

        $panier->touch();

        return response()->noContent();
    }
}