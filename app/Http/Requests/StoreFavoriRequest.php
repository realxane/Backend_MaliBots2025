<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TypeFavori;
use App\Enums\StatutProduit;

class StoreFavoriRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $type = TypeFavori::tryFrom($this->input('cibleType'));

        $cibleIdRules = ['required', 'uuid'];

        if ($type === TypeFavori::Produit) {
            $cibleIdRules[] = Rule::exists('produits', 'id')
                ->whereNull('deleted_at')
                ->where('statut', StatutProduit::Valide->value);
        }

        // Ajoute ici d’autres types si nécessaire
        // if ($type === TypeFavori::Article) { ... }

        return [
            'cibleType' => ['required', Rule::enum(TypeFavori::class)],
            'cibleId'   => $cibleIdRules,
        ];
    }

    public function messages(): array
    {
        return [
            'cibleId.exists' => 'La cible fournie est introuvable ou non disponible pour ce type.',
        ];
    }
}