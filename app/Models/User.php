<?php

namespace App\Models; // Le modèle se trouve dans l’espace de noms App\Models

use App\Enums\Role; // Import de l’énumération Role (pour gérer les rôles utilisateur)
use Illuminate\Database\Eloquent\Model; // Classe de base d’un modèle Eloquent
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Trait pour générer des identifiants uniques (UUID)

class User extends Model
{
    use HasUuids, HasApiTokens; // Utilise le trait pour générer automatiquement un UUID pour chaque utilisateur

    protected $table = 'users'; // Nom de la table associée dans la base de données
    public $incrementing = false; // Les IDs ne sont pas auto-incrémentés (puisqu’on utilise des UUID)
    protected $keyType = 'string'; // Type de la clé primaire (UUID = chaîne de caractères)

    // Champs autorisés pour la création/mise à jour en masse
    protected $fillable = [
        'nom', 'email', 'motDePasse', 'role', 'telephone', 'regionId', 'isActive',
    ];

    protected $hidden = ['motDePasse']; // Cache le mot de passe lors de la sérialisation en JSON
 
    // Transformations automatiques des champs
    protected $casts = [
        'role' => Role::class,
        'isActive' => 'boolean',
        'motDePasse' => 'hashed',
    ]; 

    // Relations

    //Un utilisateur a une région
    public function regions()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    // Un utilisateur (vendeur) peut avoir plusieurs produits
    public function produits()
    {
        return $this->hasMany(Produit::class, 'vendeurId');
    }

    // Un utilisateur (acheteur) peut avoir plusieurs commandes
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'acheteurId');
    }

    // Un utilisateur (acheteur) possède un seul panier
    public function panier()
    {
        return $this->hasOne(Panier::class, 'acheteurId');
    }

    // Un utilisateur peut avoir plusieurs favoris
    public function favoris()
    {
        return $this->hasMany(Favori::class, 'utilisateurId');
    }

    // Un utilisateur peut signaler plusieurs éléments
    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'utilisateurId');
    }

    // Un administrateur peut valider plusieurs produits
    public function validationsProduits()
    {
        return $this->hasMany(ValidationProduit::class, 'adminId');
    }

    // Un administrateur peut publier plusieurs musiques
    public function musiques()
    {
        return $this->hasMany(Musique::class, 'publieParAdminId');
    }

    // Contenus culturels publiés (admin)

    // Un administrateur peut publier plusieurs contes
    public function contesPublies()
    {
        return $this->hasMany(Conte::class, 'publieParAdminId');
    }

    // Un administrateur peut publier plusieurs proverbes
    public function proverbesPublies()
    {
        return $this->hasMany(Proverbe::class, 'publieParAdminId');
    }

    // Un administrateur peut publier plusieurs photos dans la Galerie
    public function photosPubliees()
    {
        return $this->hasMany(Photo::class, 'publieParAdminId');
    }
}