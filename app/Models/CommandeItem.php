<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CommandeItem extends Model
{
    use HasUuids;

    protected $table = 'commande_items';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'commandeId', 'produitId', 'quantite', 'prixUnitaire',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prixUnitaire' => 'decimal:2',
    ];

    //Une commandeItem concerne une commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commandeId');
    }

    //Une commandeItem concerne un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produitId');
    }
}