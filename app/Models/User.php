<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Trait pour générer des identifiants uniques (UUID)
use Illuminate\Notifications\Notifiable; //Pour les notifications, pas besoin de créer une table, Laravel fournit déjà un système pour gérer les notifications

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Pour l'authentification
use Illuminate\Foundation\Auth\User as Authenticatable; // Pour l'authentification


class User extends Authenticatable implements CanResetPasswordContract
{

    use HasApiTokens, HasFactory, Notifiable, HasUuids, CanResetPassword;

    protected $table = 'users';
    public $incrementing = false;
    protected $keyType = 'string';

    
    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'nom',
        'email',
        'motDePasse',
        'role',
        'telephone',

        'telephone_pro',
        'descrit_ton_savoir_faire',
        'regionId',
        'isActive',
    ];
 
    // Transformations automatiques des champs
    protected $hidden = [
        'motDePasse',
        'remember_token',
    ];



    /**
     * Casts automatiques
     */
    protected $casts = [
        'motDePasse' => 'hashed', // Laravel 10+ hash automatiquement le mot de passe
        'role' => Role::class,    // si tu utilises un Enum string-backed
        'isActive' => 'boolean',
    ];


    /**
     * Relation avec la région (chaque user appartient à une région)
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'vendeurId');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'acheteurId');
    }
 
    // Un utilisateur (acheteur) possède un seul panier

    public function panier()
    {
        return $this->hasOne(Panier::class, 'acheteurId');
    }

    public function favoris()
    {
        return $this->hasMany(Favori::class, 'utilisateurId');
    }

    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'utilisateurId');
    }

    public function validationsProduits()
    {
        return $this->hasMany(ValidationProduit::class, 'adminId');
    }

    public function musiques()
    {
        return $this->hasMany(Musique::class, 'publieParAdminId');
    }

    public function contesPublies()
    {
        return $this->hasMany(Conte::class, 'publieParAdminId');
    }

    public function proverbesPublies()
    {
        return $this->hasMany(Proverbe::class, 'publieParAdminId');
    }

    public function photosPubliees()
    {
        return $this->hasMany(Photo::class, 'publieParAdminId');
    }

    public function commentairesPublies()
    {
        return $this->hasMany(Commentaire::class, 'acheteurId');
    }

    //  Méthode pour compatibilité mot de passe Laravel
    public function getAuthPassword()
    {
        return $this->motDePasse;
    }
}