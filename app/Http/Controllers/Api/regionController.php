<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegionController extends Controller
{
    // 1. Afficher toutes les régions
    public function index()
    {
        $regions = Region::all();
        return response()->json($regions);
    }

    // 🟡 2. Créer une nouvelle région
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'nom' => 'required|string|max:150|unique:regions,nom',
        ]);

        // Création
        $region = Region::create([
            'id' => Str::uuid(),
            'nom' => $request->nom,
        ]);

        return response()->json([
            'message' => 'Région créée avec succès !',
            'region' => $region,
        ], 201);
    }

    // 🔵 3. Afficher une région par ID
    public function show($id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json(['message' => 'Région introuvable'], 404);
        }

        return response()->json($region);
    }

    //  4. Modifier une région
    public function update(Request $request, $id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json(['message' => 'Région introuvable'], 404);
        }

        $request->validate([
            'nom' => 'required|string|max:150|unique:regions,nom,' . $id,
        ]);

        $region->update(['nom' => $request->nom]);

        return response()->json([
            'message' => 'Région mise à jour avec succès',
            'region' => $region,
        ]);
    }

    //  5. Supprimer une région
    public function destroy($id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json(['message' => 'Région introuvable'], 404);
        }

        $region->delete();

        return response()->json(['message' => 'Région supprimée avec succès']);
    }
}
