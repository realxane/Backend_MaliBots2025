<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Enums\TypeSignalement;
use App\Enums\StatutSignalement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SignalementController extends Controller
{
    // Lister tous les signalements
    public function index()
    {
        return Signalement::with(['utilisateur', 'traiteParAdmin'])->get();
    }

    // Créer un signalement
   public function store(Request $request)
{
    $data = $request->validate([
        'utilisateurId' => 'required|uuid|exists:users,id',
        'cibleType' => 'required|string|in:Produit,Musique,Photo,Conte,Proverbe,Utilisateur',
        'cibleId' => 'required|uuid',
    ]);


    $signalement = Signalement::create([
        'utilisateurId' => $data['utilisateurId'],
        'cibleType' => $data['cibleType'],
        'cibleId' => $data['cibleId'],
        'statut' => StatutSignalement::Ouvert->value
    ]);

    return response()->json($signalement, 201);
}

    // Voir un signalement
    public function show($id)
    {
        $signalement = Signalement::with(['utilisateur', 'traiteParAdmin'])->find($id);

        if (!$signalement) {
            return response()->json(['message' => 'Signalement introuvable'], 404);
        }

        return $signalement;
    }

    // Mettre à jour le statut (par admin)
    public function mettreAJourStatut(Request $request, $id)
    {
        $validated = $request->validate([
            'statut' => ['required', Rule::in(array_column(\App\Enums\StatutSignalement::cases(), 'value'))],
            'traiteParAdminId' => 'required|exists:users,id',
        ]);

        $signalement = Signalement::find($id);
        if (!$signalement) {
            return response()->json(['message' => 'Signalement introuvable'], 404);
        }

        $signalement->update([
            'statut' => $request->statut,
            'traiteParAdminId' => $request->traiteParAdminId,
            'dateTraitement' => now(),
        ]);

        return response()->json([
            'message' => 'Signalement mis à jour avec succès',
            'signalement' => $signalement
        ]);
    }

    // Supprimer un signalement
    public function destroy($id)
    {
        $signalement = Signalement::find($id);
        if (!$signalement) {
            return response()->json(['message' => 'Signalement introuvable'], 404);
        }

        $signalement->delete();
        return response()->json(['message' => 'Signalement supprimé avec succès']);
    }
}
