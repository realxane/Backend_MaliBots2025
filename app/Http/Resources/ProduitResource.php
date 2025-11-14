<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'prix'        => (float) $this->prix,
            'categorie'   => $this->categorie?->value ?? (string) $this->categorie,
            'statut'      => $this->statut?->value ?? (string) $this->statut,
            'imageUrl'    => $this->imageUrl,
            'regionId'    => $this->regionId,
            'vendeurId'   => $this->vendeurId,
            'stock'       => (int) $this->stock,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
        ];
    }
}