<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Enums\StatutCommande;

class CommandeController extends Controller
{
    /**
     * Liste toutes les commandes
     */
    public function index()
    {
        $commandes = Commande::with(['acheteur', 'items.produit', 'paiements'])->get();
        return response()->json($commandes);
    }

    /**
     * Affiche une commande spécifique
     */
    public function show(string $id)
    {
        $commande = Commande::with(['acheteur', 'items.produit', 'paiements'])->findOrFail($id);
        return response()->json($commande);
    }

    /**
     * Crée une nouvelle commande avec items
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'acheteurId' => 'required|uuid|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.produitId' => 'required|uuid|exists:produits,id',
            'items.*.quantite' => 'required|integer|min:1',
            'items.*.prixUnitaire' => 'required|numeric|min:0',
            'methodePaiement' => 'required|string',
            'statut' => 'required|string|in:'.implode(',', array_column(StatutCommande::cases(), 'value'))
        ]);

        // Transaction pour éviter les insertions partielles
        DB::beginTransaction();
        try {
            $commande = Commande::create([
                'acheteurId' => $request->acheteurId,
                'montantTotal' => array_reduce($request->items, fn($carry, $item) => $carry + ($item['quantite'] * $item['prixUnitaire']), 0),
                'methodePaiement' => $request->methodePaiement,
                'statut' => StatutCommande::from($request->statut),
            ]);

            foreach ($request->items as $item) {
                CommandeItem::create([
                    'commandeId' => $commande->id,
                    'produitId' => $item['produitId'],
                    'quantite' => $item['quantite'],
                    'prixUnitaire' => $item['prixUnitaire'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Commande créée avec succès.',
                'data' => $commande->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la création de la commande.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function update(Request $request, string $id)
    {
        $commande = Commande::findOrFail($id);

        $request->validate([
            'statut' => 'required|string|in:'.implode(',', array_column(StatutCommande::cases(), 'value'))
        ]);

        $commande->update([
            'statut' => StatutCommande::from($request->statut),
        ]);

        return response()->json([
            'message' => 'Statut de la commande mis à jour avec succès.',
            'data' => $commande
        ]);
    }

    /**
     * Supprimer une commande
     */
    public function destroy(string $id)
    {
        $commande = Commande::findOrFail($id);
        $commande->items()->delete(); // supprime les items liés
        $commande->delete();

        return response()->json([
            'message' => 'Commande supprimée avec succès.'
        ]);
    }
}
