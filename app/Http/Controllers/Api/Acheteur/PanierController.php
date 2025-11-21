<?php

namespace App\Http\Controllers\Api\Acheteur;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\PanierResource;
use App\Models\Panier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PanierController extends Controller
{
    use AuthorizesRequests; 
    // GET /acheteur/panier
    public function show(Request $request)
    {
        $userId = $request->user()->id;

        $panier = Panier::firstOrCreate(['acheteurId' => $userId]);

        $this->authorize('view', $panier); 

        $panier->load(['items.produit']);

        return new PanierResource($panier);
    }

    // DELETE /acheteur/panier  (vider tous les items)
    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        $panier = Panier::where('acheteurId', $userId)->first();

        if (! $panier) {
            return response()->noContent(); // rien à vider
        }

        $this->authorize('clear', $panier);

        $panier->items()->delete();

        // touche le panier pour MAJ updated_at
        $panier->touch();

        return response()->noContent();
    }
}