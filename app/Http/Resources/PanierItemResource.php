<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanierItemResource extends JsonResource
{
    public function toArray($request)
    {
        $totalLigne = (float) $this->quantite * (float) $this->prixUnitaire;

        return [
            'id'           => $this->id,
            'produitId'    => $this->produitId,
            'quantite'     => (int) $this->quantite,
            'prixUnitaire' => number_format((float) $this->prixUnitaire, 2, '.', ''),
            'totalLigne'   => number_format($totalLigne, 2, '.', ''),
            'produit' => $this->whenLoaded('produit', function () {
                return [
                    'id'       => $this->produit->id,
                    'nom'      => $this->produit->nom,
                    'imageUrl' => $this->produit->imageUrl,
                ];
            }), 
        ];
    }
}