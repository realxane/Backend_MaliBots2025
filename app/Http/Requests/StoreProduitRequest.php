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
        // Rôle déjà vérifié par le middleware; on peut aussi Gate::authorize('create', Produit::class).
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prix'        => ['required', 'numeric', 'min:0'],
            'categorie'   => ['required', Rule::enum(CategorieProduit::class)],
            'statut'      => ['sometimes', Rule::enum(StatutProduit::class)], // optionnel: on peut forcer un statut par défaut
            'regionId'    => ['required', 'uuid', 'exists:regions,id'],
            'imageUrl'    => ['required', 'url', 'max:2048'],
            // vendeurId ne doit PAS venir du client (voir sécurité ci-dessous)
        ];
    }
}