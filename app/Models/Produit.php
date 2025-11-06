<?php

namespace App\Models;

use App\Enums\CategorieProduit;
use App\Enums\StatutProduit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'produits';
    public $incrementing = false;
    protected $keyType = 'string';
 
    protected $fillable = [
        'nom', 'description', 'prix', 'categorie', 'regionId', 'imageUrl',
        'statut', 'vendeurId', 
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'categorie' => CategorieProduit::class,
        'statut' => StatutProduit::class,
    ];


    //Un produit a une région
    public function regions()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    // Un produit appartient à un vendeur
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeurId');
    }

    // Un produit peut avoir plusieurs validations
    public function validations()
    {
        return $this->hasMany(ValidationProduit::class, 'produitId');
    }

    // Un produit peut avoir une seule demande de suppression
    public function suppression()
    {
        return $this->hasOne(SuppressionProduit::class, 'produitId');
    }

    // Relation avec les items du panier
    public function panierItems()
    {
        return $this->hasMany(PanierItem::class, 'produitId');
    }

    // Relation avec les items de commandes
    public function commandeItems()
    {
        return $this->hasMany(CommandeItem::class, 'produitId');
    }
} 