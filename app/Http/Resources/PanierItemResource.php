<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PanierItemResource extends JsonResource
{
    public function toArray($request)
    {
        $totalLigne = (float) $this->quantite * (float) $this->prixUnitaire;

        // Construire une imageUrl complète
        $image = $this->produit->imageUrl ?? null;
        if ($image && !Str::startsWith($image, ['http://', 'https://'])) {
            // si c’est un chemin relatif, on le transforme en URL publique
            $image = url($image);
        }

        return [
            'id'           => $this->id,
            'produitId'    => $this->produitId,
            'quantite'     => (int) $this->quantite,
            'prixUnitaire' => number_format((float) $this->prixUnitaire, 2, '.', ''),
            'totalLigne'   => number_format($totalLigne, 2, '.', ''),
            'produit'      => $this->whenLoaded('produit', function () use ($image) {
                return [
                    'id'       => $this->produit->id,
                    'nom'      => $this->produit->nom,
                    'imageUrl' => $image,
                ];
            }),
        ];
    }
}