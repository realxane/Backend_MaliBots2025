<?php

namespace App\Http\Controllers\Api\Vendeur;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NouveauProduitPublie;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreProduitRequest;
use App\Http\Requests\UpdateProduitRequest;
use App\Http\Resources\ProduitResource;
use App\Models\Produit;
use App\Models\ProduitImage;
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
            ->with('images') // eager load des images
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

        if (! isset($data['statut']) && enum_exists(StatutProduit::class)) {
            $data['statut'] = StatutProduit::EnAttente;
        }

        $images = $data['images'] ?? [];
        unset($data['images']); // on gère séparément
        // imageUrl déprécié
        unset($data['imageUrl']);

        $produit = DB::transaction(function () use ($data, $images) {
            $produit = Produit::create($data);

            // Création des images
            foreach (array_values($images) as $idx => $url) {
                ProduitImage::create([
                    'produitId' => $produit->id,
                    'url'       => $url,
                    'position'  => $idx,
                ]);
            }

            // Compat: renseigner imageUrl avec la première image si la colonne existe
            if (schema()->hasColumn('produits', 'imageUrl')) {
                $first = $images[0] ?? null;
                if ($first) {
                    $produit->imageUrl = $first;
                    $produit->save();
                }
            }

            return $produit->load('images');
        });

        return (new ProduitResource($produit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    // GET /vendeur/produits/{produit}
    public function show(Request $request, Produit $produit)
    {
        $this->authorize('view', $produit);

        return new ProduitResource($produit->load('images'));
    }

    // PUT/PATCH /vendeur/produits/{produit}
    public function update(UpdateProduitRequest $request, Produit $produit)
    {
        $this->authorize('update', $produit);

        $data = $request->validated();
        unset($data['vendeurId'], $data['id']);

        $ancienStatut = $produit->statut instanceof StatutProduit
            ? $produit->statut
            : (is_string($produit->statut) ? StatutProduit::from($produit->statut) : null);

        $images = $data['images'] ?? null;
        unset($data['images'], $data['imageUrl']); // on gère les images séparément

        $produit = DB::transaction(function () use ($produit, $data, $images) {
            if (!empty($data)) {
                $produit->update($data);
            }

            // Si un tableau d’images est fourni, on remplace l’ensemble
            if (is_array($images)) {
                // delete puis recreate (simple et fiable)
                $produit->images()->delete();
                foreach (array_values($images) as $idx => $url) {
                    $produit->images()->create([
                        'url'      => $url,
                        'position' => $idx,
                    ]);
                }

                // Compat: maj imageUrl
                if (schema()->hasColumn('produits', 'imageUrl')) {
                    $produit->imageUrl = $images[0] ?? null;
                    $produit->save();
                }
            }

            return $produit->load('images');
        });

        if ($ancienStatut !== StatutProduit::Valide && $produit->statut === StatutProduit::Valide) {
            $this->sendNouveauProduitPublie($produit);
        }

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

        return new ProduitResource($produit->load('images'));
    }

    private function sendNouveauProduitPublie(Produit $produit): void
    {
        // première image (thumbnail) si dispo
        $thumb = $produit->first_image_url ?? $produit->firstImageUrl ?? null;

        User::where('role', Role::Acheteur)
            ->where('isActive', true)
            ->select('id')
            ->chunk(500, function ($acheteurs) use ($produit, $thumb) {
                Notification::send($acheteurs, new NouveauProduitPublie(
                    produitId: (string) $produit->id,
                    titre: $produit->nom,
                    imageUrl: $thumb,
                    vendeurId: (string) $produit->vendeurId
                ));
            });
    }
} 

// Helper pour vérifier la présence de colonnes sans casser en prod
if (!function_exists('schema')) {
    function schema(): \Illuminate\Database\Schema\Builder {
        return \Illuminate\Support\Facades\Schema::getFacadeRoot();
    }
}