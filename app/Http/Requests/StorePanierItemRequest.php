<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePanierItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'produitId' => ['required', 'uuid', 'exists:produits,id'],
            'quantite'  => ['sometimes', 'integer', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'produitId.required' => 'Le produit est obligatoire.',
            'produitId.exists'   => 'Le produit est introuvable.',
        ];
    }
    public function validationData(): array
    {
        // Log “brut” utile pour diagnostiquer ce que Laravel reçoit
        logger()->debug('payload_brut', [
            'headers' => $this->headers->all(),
            'input'   => $this->all(),          // ce que Laravel voit (après parse)
            'rawBody' => $this->getContent(),   // corps JSON brut (string)
        ]);

        return parent::validationData();
    }
}