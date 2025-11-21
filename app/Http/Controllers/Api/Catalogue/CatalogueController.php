<?php

namespace App\Http\Controllers\Api\Catalogue;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProduitResource;
use App\Models\Produit;
use App\Enums\StatutProduit;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        // supporte per_page et perPage
        $perPage = (int) ($request->input('per_page') ?? $request->input('perPage') ?? 20);
        $perPage = max(1, min($perPage, 100));

        $q         = trim((string) $request->input('q', ''));
        $regionId  = $request->input('regionId');
        $categorie = $request->input('categorie'); // ex: "Plats", "Tenues", "Accessoires"
        $sort      = $request->input('sort');      // ex: "prix:asc" | "prix:desc"

        $query = Produit::query()
            ->where('statut', StatutProduit::Valide)   // catalogue public = seulement validés
            ->with('images');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('nom', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%")
                   ->orWhere('categorie', 'like', "%{$q}%");
            });
        }
        if (!empty($regionId)) {
            $query->where('regionId', $regionId);
        }
        if (!empty($categorie)) {
            $query->where('categorie', $categorie);
        }

        // tri
        if ($sort) {
            [$field, $dir] = array_pad(explode(':', $sort, 2), 2, 'asc');
            $field = $field === 'prix' ? 'prix' : 'created_at';
            $dir   = strtolower($dir) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($field, $dir);
        } else {
            $query->orderByDesc('created_at');
        }

        return ProduitResource::collection(
            $query->paginate($perPage)->appends($request->query())
        );
    }

    public function show(string $id)
    {
        $produit = Produit::where('id', $id)
            ->where('statut', StatutProduit::Valide)
            ->with('images')
            ->firstOrFail();

        return new ProduitResource($produit);
    }
}