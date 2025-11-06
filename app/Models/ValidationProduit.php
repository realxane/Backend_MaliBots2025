<?php

namespace App\Models;

use App\Enums\StatutProduit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ValidationProduit extends Model
{
    use HasUuids;

    protected $table = 'validations_produits';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'produitId', 'adminId', 'decision', 
    ];

    protected $casts = [
        'decision' => StatutProduit::class,
    ];

    // Une ValidationProduit concerne un vendeur
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produitId');
    }

    // Une ValidationProduit concerne un admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'adminId');
    } 
}