<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PanierItem extends Model
{
    use HasUuids;

    protected $table = 'panier_items';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'panierId', 'produitId', 'quantite', 'prixUnitaire',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prixUnitaire' => 'decimal:2',
    ];

    //Un PanierItem concerne un panier
    public function panier()
    {
        return $this->belongsTo(Panier::class, 'panierId');
    }

    //Un PanierItem concerne un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produitId');
    } 
}