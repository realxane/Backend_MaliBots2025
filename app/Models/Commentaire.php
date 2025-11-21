<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Commentaire extends Model
{
    use HasUuids ,HasFactory, SoftDeletes;

    protected $table = 'commentaires';
    public $incrementing = false; // Les IDs ne sont pas auto-incrémentés (puisqu’on utilise des UUID)
    protected $keyType = 'string'; 

    protected $fillable = [
        'produitId', 
        'acheteurId',
        'contenu',
    ];

    // Relations
    public function produit()
    {
        // Clé étrangère personnalisée 'produitId' (cohérent avec 'vendeurId' chez toi)
        return $this->belongsTo(Produit::class, 'produitId');
    }

    public function auteur()
    {
        // L'auteur est un utilisateur (acheteur)
        return $this->belongsTo(User::class, 'acheteurId');
    }
}