<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePanierItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'quantite' => ['required', 'integer', 'min:0', 'max:1000'], // 0 => suppression
        ];
    }
}