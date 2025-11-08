<?php

namespace App\Http\Controllers\Api\Vendeur;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreProduitRequest;
use App\Http\Requests\UpdateProduitRequest;
use App\Http\Resources\ProduitResource;
use App\Models\Produit;
use App\Enums\StatutProduit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProduitController extends Controller
{   
    use AuthorizesRequests; 
    // GET /vendeur/produits
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Produit::query()
            ->where('vendeurId', $userId)
            ->when($request->filled('categorie'), fn($q) => $q->where('categorie', $request->input('categorie')))
            ->when($request->filled('statut'), fn($q) => $q->where('statut', $request->input('statut')))
            ->when($request->boolean('withTrashed'), fn($q) => $q->withTrashed())
            ->orderByDesc('created_at');

        $perPage = min(max((int) $request->input('perPage', 15), 1), 100);

        return ProduitResource::collection($query->paginate($perPage));
    }

    // POST /vendeur/produits
    public function store(StoreProduitRequest $request)
    {
        $this->authorize('create', Produit::class);

        $data = $request->validated();
        $data['vendeurId'] = $request->user()->id;

        // Définir un statut par défaut si non fourni (ajustez selon vos enums)
        if (! isset($data['statut']) && enum_exists(StatutProduit::class)) {
            $data['statut'] = StatutProduit::Brouillon->value ?? 'brouillon';
        }

        $produit = Produit::create($data);

        return (new ProduitResource($produit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    // GET /vendeur/produits/{produit}
    public function show(Request $request, Produit $produit)
    {
        $this->authorize('view', $produit);

        return new ProduitResource($produit);
    }

    // PUT/PATCH /vendeur/produits/{produit}
    public function update(UpdateProduitRequest $request, Produit $produit)
    {
        $this->authorize('update', $produit);

        $data = $request->validated();
        // Sécurité: empêcher tout override malicieux
        unset($data['vendeurId'], $data['id']);

        $produit->update($data);

        return new ProduitResource($produit);
    }

    // DELETE /vendeur/produits/{produit}
    public function destroy(Request $request, Produit $produit)
    {
        $this->authorize('delete', $produit);

        $produit->delete();

        return response()->noContent();
    }

    // PATCH /vendeur/produits/{id}/restore
    public function restore(Request $request, string $id)
    {
        $userId = $request->user()->id;

        $produit = Produit::withTrashed()
            ->where('id', $id)
            ->where('vendeurId', $userId)
            ->firstOrFail();

        $this->authorize('restore', $produit);

        $produit->restore();

        return new ProduitResource($produit);
    }
}