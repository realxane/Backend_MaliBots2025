<?php

namespace App\Http\Requests;

use App\Enums\CategorieProduit;
use App\Enums\StatutProduit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('images') && $this->filled('imageUrl')) {
            $this->merge([
                'images' => [$this->input('imageUrl')],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nom'         => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'prix'        => ['sometimes', 'numeric', 'min:0'],
            'categorie'   => ['sometimes', Rule::enum(CategorieProduit::class)],
            'statut'      => ['sometimes', Rule::enum(StatutProduit::class)],
            'regionId'    => ['sometimes', 'uuid', 'exists:regions,id'],
            'stock'       => ['sometimes','integer','min:0'],

            // Mise à jour des images: si présent, on remplace l’ensemble
            'images'      => ['sometimes', 'array', 'min:1'],
            'images.*'    => ['url', 'max:2048'],

            'imageUrl'    => ['sometimes', 'url', 'max:2048'], // compat
        ];
    }
}