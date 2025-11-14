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
        return true; // la Policy 'update' sera appelée dans le controller
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
            'imageUrl'    => ['sometimes', 'url', 'max:2048'],
            'stock'       => ['sometimes','integer','min:0'],
        ];
    }
}