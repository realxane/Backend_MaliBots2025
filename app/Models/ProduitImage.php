<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProduitImage extends Model
{
    use HasUuids;

    protected $table = 'produit_images';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'produitId', 'url', 'position',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produitId');
    }
} 