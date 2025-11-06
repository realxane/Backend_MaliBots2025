<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Panier extends Model
{
    use HasUuids;

    protected $table = 'paniers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'acheteurId',
    ];

    //Un panier appartient à un acheteur
    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteurId');
    }

    //Un panier a plusieurs panierItems
    public function items()
    {
        return $this->hasMany(PanierItem::class, 'panierId');
    }
} 