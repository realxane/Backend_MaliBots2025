<?php

namespace App\Http\Requests;

use App\Enums\CategorieProduit;
use App\Enums\StatutProduit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Compat: si imageUrl est fourni mais pas images, on crée images = [imageUrl]
        if (!$this->has('images') && $this->filled('imageUrl')) {
            $this->merge([
                'images' => [$this->input('imageUrl')],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prix'        => ['required', 'numeric', 'min:0'],
            'categorie'   => ['required', Rule::enum(CategorieProduit::class)],
            'statut'      => ['sometimes', Rule::enum(StatutProduit::class)],
            'regionId'    => ['required', 'uuid', 'exists:regions,id'],
            'stock'       => ['required','integer','min:0'],

            // Nouvelles règles
            'images'      => ['required', 'array', 'min:1'],
            'images.*'    => ['url', 'max:2048'],

            // Ancien champ (déprécié) encore accepté mais non utilisé directement
            'imageUrl'    => ['sometimes', 'url', 'max:2048'],
        ];
    }
}