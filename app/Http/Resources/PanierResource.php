<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanierResource extends JsonResource
{
    public function toArray($request)
    {
        $items = $this->whenLoaded('items', fn () => $this->items);

        $subtotal = $items
            ? $items->reduce(fn($c, $i) => $c + ((float) $i->quantite * (float) $i->prixUnitaire), 0.0)
            : 0.0;

        return [
            'id'         => $this->id,
            'acheteurId' => $this->acheteurId,
            'items'      => PanierItemResource::collection($items ?? $this->items),
            'nbItems'    => $items ? (int) $items->sum('quantite') : (int) $this->items()->sum('quantite'),
            'subtotal'   => number_format($subtotal, 2, '.', ''),
            'createdAt'  => $this->created_at?->toISOString(),
            'updatedAt'  => $this->updated_at?->toISOString(),
        ];
    }
}