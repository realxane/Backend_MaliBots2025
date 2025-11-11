<?php

namespace App\Http\Controllers;

use App\Models\Proverbe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProverbeController extends Controller
{
    /**
     * Liste tous les proverbes, avec possibilité de filtrer par région ou langue.
     */
    public function index(Request $request)
    {
        $query = Proverbe::with('regions', 'admin');

        if ($request->has('regionId')) {
            $query->where('regionId', $request->region_id);
        }

        if ($request->has('langue')) {
            $query->where('langue', $request->langue);
        }

        $proverbes = $query->orderByDesc('created_at')->get();

        return response()->json($proverbes);
    }

    /**
     * Ajouter un nouveau proverbe (admin uniquement).
     */
    public function store(Request $request)
    {
        // $user = Auth::user();
        // if (!$user || !$user->is_admin) {
        //     return response()->json(['message' => 'Accès refusé. Administrateur requis.'], 403);
        // }

        $adminId = '00000000-0000-0000-0000-000000000000'; // valeur fixe pour test

        $validated = $request->validate([
            'texte' => 'required|string',
            'langue' => 'required|string',
            'regionId' => 'required|exists:regions,id',
        ]);

        // $proverbe = Proverbe::create([
        //     'texte' => $validated['texte'],
        //     'langue' => $validated['langue'],
        //     'region_id' => $validated['region_id'],
        //     'publie_par_admin_id' => $user->id,
        // ]);

        $proverbe = Proverbe::create([
            'texte' => $validated['texte'],
            'langue' => $validated['langue'],
            'regionId' => $validated['regionId'],
            'publieParAdminId' => $adminId,
        ]);


        return response()->json($proverbe, 201);
    }

    /**
     * Afficher un proverbe précis.
     */
    public function show($id)
    {
        $proverbe = Proverbe::with('region', 'admin')->find($id);

        if (!$proverbe) {
            return response()->json(['message' => 'Proverbe non trouvé.'], 404);
        }

        return response()->json($proverbe);
    }

    /**
     * Modifier un proverbe (admin uniquement).
     */
    public function update(Request $request, $id)
    {
        // $user = Auth::user();

        // if (!$user || !$user->is_admin) {
        //     return response()->json(['message' => 'Accès refusé. Administrateur requis.'], 403);
        // }


        $proverbe = Proverbe::find($id);
        if (!$proverbe) {
            return response()->json(['message' => 'Proverbe non trouvé.'], 404);
        }

        $validated = $request->validate([
            'texte' => 'sometimes|string',
            'langue' => 'sometimes|string',
            'regionId' => 'sometimes|exists:regions,id',
        ]);

        $proverbe->update($validated);

        return response()->json(['message' => 'Proverbe mis à jour.', 'data' => $proverbe]);
    }

    /**
     * Supprimer un proverbe (admin uniquement).
     */
    public function destroy($id)
    {
        // $user = Auth::user();

        // if (!$user || !$user->is_admin) {
        //     return response()->json(['message' => 'Accès refusé. Administrateur requis.'], 403);
        // }

        $proverbe = Proverbe::find($id);
        if (!$proverbe) {
            return response()->json(['message' => 'Proverbe non trouvé.'], 404);
        }

        $proverbe->delete();
        return response()->json(['message' => 'Proverbe supprimé avec succès.']);
    }
}
