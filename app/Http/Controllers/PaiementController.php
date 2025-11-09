<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Paiement;
use App\Enums\MethodePaiement;
use App\Enums\StatutPaiement;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    /**
     * Créer un paiement (simulation ou réel selon mode)
     */
    public function store(Request $request)
{
    $request->validate([
        'commandeId' => 'required|uuid|exists:commandes,id',
        'methode' => 'required|string|in:OrangeMoney,Wave,Mastercard',
        'infosPaiement' => 'required|array',
        'simulate' => 'nullable|string|in:success,fail'
    ]);

    $commande = Commande::findOrFail($request->commandeId);

    // Vérifie s’il y a déjà un paiement
    if ($commande->paiements) {
        return response()->json([
            'message' => 'Un paiement existe déjà pour cette commande.',
            'data' => $commande->paiements
        ], 400);
    }

    // Ensuite continue la logique de paiement (simulation ou réel)
    $methode = $request->methode;
    $infos = $request->infosPaiement;
    $simulate = $request->simulate ?? null;

    switch ($methode) {
    case 'OrangeMoney':
        $result = $this->payerOrangeMoney($commande, $infos['numeroMobile'] ?? null, $simulate);
        break;
    case 'Wave':
        $result = $this->payerWave($commande, $infos['numeroMobile'] ?? null, $simulate);
        break;
    case 'Mastercard':
        $result = $this->payerMastercard($commande, $infos, $simulate); // ici on garde tout le tableau pour les infos de la carte
        break;
    default:
        return response()->json(['message'=>'Méthode de paiement non supportée.'], 400);
}


    $paiement = Paiement::create([
        'commandeId' => $commande->id,
        'montant' => $commande->montantTotal,
        'methode' => $methode,
        'statut' => $result['statut'],
        'referenceFournisseur' => $result['reference'] ?? null
    ]);

    return response()->json([
        'message' => 'Paiement créé avec succès.',
        'simulation' => $simulate ? "Simulation : mode '$simulate'" : null,
        'data' => $paiement
    ]);
}

    /**
     * Simulation Orange Money
     */
    private function payerOrangeMoney(Commande $commande, ?string $numeroMobile, ?string $simulate = null)
    {
        if ($simulate === 'success') {
            return ['statut' => StatutPaiement::SUCCES, 'reference' => 'OM' . Str::upper(Str::random(8))];
        }
        if ($simulate === 'fail') {
            return ['statut' => StatutPaiement::ECHEC, 'reference' => null];
        }

        $successNumbers = ['0000000000', '0999999999'];
        $failNumbers = ['1111111111'];

        if (in_array($numeroMobile, $successNumbers)) {
            return ['statut' => StatutPaiement::SUCCES, 'reference' => 'OM' . Str::upper(Str::random(8))];
        }
        if (in_array($numeroMobile, $failNumbers)) {
            return ['statut' => StatutPaiement::ECHEC, 'reference' => null];
        }

        return ['statut' => StatutPaiement::EN_ATTENTE, 'reference' => 'OM' . Str::upper(Str::random(8))];
    }

    /**
     * Simulation Wave
     */
    private function payerWave(Commande $commande, ?string $numeroMobile, ?string $simulate = null)
    {
        if ($simulate === 'success') {
            return ['statut' => StatutPaiement::SUCCES, 'reference' => 'WV' . Str::upper(Str::random(8))];
        }
        if ($simulate === 'fail') {
            return ['statut' => StatutPaiement::ECHEC, 'reference' => null];
        }

        $successNumbers = ['0000000001', '0999999998'];
        $failNumbers = ['1111111111'];

        if (in_array($numeroMobile, $successNumbers)) {
            return ['statut' => StatutPaiement::SUCCES, 'reference' => 'WV' . Str::upper(Str::random(8))];
        }
        if (in_array($numeroMobile, $failNumbers)) {
            return ['statut' => StatutPaiement::ECHEC, 'reference' => null];
        }

        return ['statut' => StatutPaiement::EN_ATTENTE, 'reference' => 'WV' . Str::upper(Str::random(8))];
    }

    /**
     * Simulation MasterCard
     */
    private function payerMastercard(Commande $commande, array $infos, ?string $simulate = null)
    {
        if ($simulate === 'success') {
            return ['statut' => StatutPaiement::SUCCES, 'reference' => 'MC' . Str::upper(Str::random(8))];
        }
        if ($simulate === 'fail') {
            return ['statut' => StatutPaiement::ECHEC, 'reference' => null];
        }

        $successCards = ['4242424242424242','5555555555554444'];
        $failCards = ['4000000000009995'];

        $num = $infos['numeroCarte'] ?? null;
        if ($num && in_array($num, $successCards)) {
            return ['statut' => StatutPaiement::SUCCES, 'reference' => 'MC' . Str::upper(Str::random(8))];
        }
        if ($num && in_array($num, $failCards)) {
            return ['statut' => StatutPaiement::ECHEC, 'reference' => null];
        }

        return ['statut' => StatutPaiement::EN_ATTENTE, 'reference' => 'MC' . Str::upper(Str::random(8))];
    }

    /**
     * Mettre à jour le statut du paiement
     */
    public function update(Request $request, string $id)
    {
        $paiement = Paiement::findOrFail($id);
        $request->validate(['statut' => 'required|string|in:en_attente,succes,echec']);

        $paiement->update(['statut' => StatutPaiement::from($request->statut)]);

        return response()->json(['message' => 'Statut mis à jour avec succès.', 'data' => $paiement]);
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
