<?php

namespace App\Http\Controllers;

use App\Models\Conte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConteController extends Controller
{
    /**
     * Liste tous les contes, avec possibilité de filtrer par région ou langue.
     */
    public function index(Request $request)
    {
        $query = Conte::with('regions', 'admin');

        if ($request->has('regionId')) {
            $query->where('regionId', $request->region_id);
        }

        if ($request->has('langue')) {
            $query->where('langue', $request->langue);
        }

        $contes = $query->orderByDesc('created_at')->get();

        return response()->json($contes);
    }

    /**
     * Ajouter un nouveau conte (admin uniquement).
     */
    public function store(Request $request)
    {
        // $user = Auth::user();
        // if (!$user || !$user->is_admin) {
        //     return response()->json(['message' => 'Accès refusé. Administrateur requis.'], 403);
        // }

        $adminId = '00000000-0000-0000-0000-000000000000'; // valeur fixe pour test

        $validated = $request->validate([
            'titre' => 'required|string',
            'histoire' => 'required|string',
            'langue' => 'required|string',
            'regionId' => 'required|exists:regions,id',
        ]);

        // $conte = Conte::create([
        //     'titre' => $validated['titre'],
        //     'contenu' => $validated['contenu'],
        //     'langue' => $validated['langue'],
        //     'region_id' => $validated['region_id'],
        //     'publie_par_admin_id' => $user->id,
        // ]);

         $conte = Conte::create([
            'titre' => $validated['titre'],
            'histoire' => $validated['histoire'],
            'langue' => $validated['langue'],
            'regionId' => $validated['regionId'],
            'publieParAdminId' => $adminId,
        ]);

        return response()->json($conte, 201);
    }

    /**
     * Afficher un conte précis.
     */
    public function show($id)
    {
        $conte = Conte::with('regions', 'admin')->find($id);

        if (!$conte) {
            return response()->json(['message' => 'Conte non trouvé.'], 404);
        }

        return response()->json($conte);
    }

    /**
     * Modifier un conte (admin uniquement).
     */
    public function update(Request $request, $id)
    {
        // $user = Auth::user();
        // if (!$user || !$user->is_admin) {
        //     return response()->json(['message' => 'Accès refusé. Administrateur requis.'], 403);
        // }


        $conte = Conte::find($id);
        if (!$conte) {
            return response()->json(['message' => 'Conte non trouvé.'], 404);
        }

        $validated = $request->validate([
            'titre' => 'sometimes|string',
            'histoire' => 'sometimes|string',
            'langue' => 'sometimes|string',
            'regionId' => 'sometimes|exists:regions,id',
        ]);

        $conte->update($validated);

        return response()->json(['message' => 'Conte mis à jour.', 'data' => $conte]);
    }

    /**
     * Supprimer un conte (admin uniquement).
     */
    public function destroy($id)
    {
        // $user = Auth::user();
        // if (!$user || !$user->is_admin) {
        //     return response()->json(['message' => 'Accès refusé. Administrateur requis.'], 403);
        // }

        $conte = Conte::find($id);
        if (!$conte) {
            return response()->json(['message' => 'Conte non trouvé.'], 404);
        }

        $conte->delete();
        return response()->json(['message' => 'Conte supprimé avec succès.']);
    }
}
