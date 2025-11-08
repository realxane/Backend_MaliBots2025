<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Enums\MethodePaiement;
use App\Enums\StatutPaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    /**
     * Créer un paiement (simulation)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commandeId' => 'required|uuid',
            'montant' => 'required|numeric|min:100',
            'methode' => 'required|string|in:OrangeMoney,Wave,Mastercard',
        ]);

        // Simulation du paiement
        $reference = strtoupper(Str::random(10));
        $message = match ($validated['methode']) {
            'OrangeMoney' => 'Simulation : Paiement envoyé à Orange Money.',
            'Wave' => 'Simulation : Paiement envoyé à Wave.',
            'Mastercard' => 'Simulation : Paiement envoyé à Mastercard.',
            default => 'Méthode non reconnue.'
        };

        $paiement = Paiement::create([
            'commandeId' => $validated['commandeId'],
            'montant' => $validated['montant'],
            'methode' => MethodePaiement::from($validated['methode']),
            'statut' => StatutPaiement::EN_ATTENTE,
            'referenceFournisseur' => $reference,
        ]);

        return response()->json([
            'message' => 'Paiement créé avec succès.',
            'simulation' => $message,
            'data' => $paiement,
        ]);
    }

    /**
     * Liste tous les paiements
     */
    public function index()
    {
        return response()->json(Paiement::latest()->get());
    }

    /**
     * Afficher un paiement
     */
    public function show(string $id)
    {
        $paiement = Paiement::findOrFail($id);
        return response()->json($paiement);
    }

    /**
     * Mettre à jour le statut du paiement
     */
    public function update(Request $request, string $id)
    {
        $paiement = Paiement::findOrFail($id);
        $request->validate(['statut' => 'required|string|in:en_attente,succes,echec']);

        $paiement->update([
            'statut' => StatutPaiement::from($request->statut),
        ]);

        return response()->json([
            'message' => 'Statut mis à jour avec succès.',
            'data' => $paiement,
        ]);
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(string $id)
    {
        $paiement = Paiement::findOrFail($id);
        $paiement->delete();

        return response()->json(['message' => 'Paiement supprimé avec succès.']);
    }
}
