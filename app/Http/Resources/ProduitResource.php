<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    public function toArray($request): array
    {
        // Liste d’URLs d’images (ordonnées)
        $images = $this->whenLoaded('images', fn () => $this->images->pluck('url')->all(), function () {
            // Si non chargé, fallback minimal sans double requête
            return method_exists($this->resource, 'images')
                ? $this->images()->orderBy('position')->pluck('url')->all()
                : [];
        });

        // Compat: imageUrl (déprécié) = première image si dispo, sinon la colonne
        $primaryImage = $images[0] ?? $this->imageUrl;

        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'prix'        => (float) $this->prix,
            'categorie'   => $this->categorie?->value ?? (string) $this->categorie,
            'statut'      => $this->statut?->value ?? (string) $this->statut,

            'images'      => $images,
            'imageUrl'    => $primaryImage, // déprécié, conservé pour compat UI

            'regionId'    => $this->regionId,
            'vendeurId'   => $this->vendeurId,
            'stock'       => (int) $this->stock,

            'rating'      => [
                'avg'   => (float) $this->rating_avg,
                'count' => (int) $this->rating_count,
            ],

            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
        ];
    }
}