<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'cibleType'    => $this->cibleType?->value,
            'cibleId'      => $this->cibleId,
            'utilisateurId'=> $this->utilisateurId,
            'createdAt'    => $this->created_at,
        ];
    }
}