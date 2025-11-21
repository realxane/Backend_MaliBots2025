<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau pour le JSON.
     */
    public function toArray($request)
    {
        // Images de la relation
        $images = $this->whenLoaded('images', function () {
            return $this->images->map(function ($img) {
                return [
                    'id'       => (string) $img->id,
                    'url'      => $img->url,
                    'position' => $img->position,
                ];
            })->values();
        });

        // Région
        $region = $this->whenLoaded('region', function () {
            return [
                'id'  => (string) optional($this->region)->id,
                'nom' => optional($this->region)->nom,
            ];
        });

        // Vendeur
        $vendeur = $this->whenLoaded('vendeur', function () {
            return [
                'id'            => (string) $this->vendeur->id,
                'nom'           => $this->vendeur->nom,
                'telephone'     => $this->vendeur->telephone,
                'telephone_pro' => $this->vendeur->telephone_pro,
            ];
        });

        // Première image pour l’UI (firstImageUrl)
        $firstImage = null;
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            $firstImage = $this->images->sortBy('position')->first()->url;
        } elseif (!empty($this->first_image_url)) {
            $firstImage = $this->first_image_url;
        } elseif (!empty($this->firstImageUrl)) {
            $firstImage = $this->firstImageUrl;
        } elseif (!empty($this->imageUrl)) {
            $firstImage = $this->imageUrl;
        }

        return [
            'id'          => (string) $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'prix'        => $this->prix,
            'categorie'   => $this->categorie,

            'regionId'    => optional($this->region)->id ?? $this->regionId,
            'regionName'  => optional($this->region)->nom ?? $this->regionName,
            'region'      => $region,

            'images'       => $images,
            'firstImageUrl'=> $firstImage,
            'imageUrl'     => $this->imageUrl, // compat

            'statut'       => $this->statut,
            'vendeurId'    => (string) $this->vendeurId,
            'stock'        => $this->stock,

            'rating_avg'   => $this->rating_avg,
            'rating_count' => $this->rating_count,

            'vendeur'      => $vendeur,
            'sellerName'   => optional($this->vendeur)->nom,
            'sellerPhone'  => optional($this->vendeur)->telephone_pro
                              ?? optional($this->vendeur)->telephone,
        ];
    }
}