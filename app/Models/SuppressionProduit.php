<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SuppressionProduit extends Model
{
    use HasUuids; 

    protected $table = 'suppressions_produits';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'produitId', 'vendeurId'
    ];

    // Une suppression concerne un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produitId');
    }

    // Une suppression est faite par un vendeur
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeurId');
    } 
}