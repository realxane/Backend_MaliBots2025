<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Trait pour générer des identifiants uniques (UUID)
use Illuminate\Notifications\Notifiable; //Pour les notifications, pas besoin de créer une table, Laravel fournit déjà un système pour gérer les notifications
=======
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;
>>>>>>> f8815316218828b93ef82217d1fc4a0c270a2d73

class User extends Authenticatable implements CanResetPasswordContract
{
<<<<<<< HEAD
    use HasUuids, HasApiTokens, Notifiable; // Utilise le trait pour générer automatiquement un UUID pour chaque utilisateur
=======
    use HasUuids, HasApiTokens, Notifiable, CanResetPassword;
>>>>>>> f8815316218828b93ef82217d1fc4a0c270a2d73

    protected $table = 'users';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nom',
        'email',
        'motDePasse',
        'role',
        'telephone',
        'regionId',
        'isActive',
    ];

<<<<<<< HEAD
    protected $hidden = ['motDePasse']; // Cache le mot de passe lors de la sérialisation en JSON
 
    // Transformations automatiques des champs
=======
    protected $hidden = [
        'motDePasse',
        'remember_token',
    ];

>>>>>>> f8815316218828b93ef82217d1fc4a0c270a2d73
    protected $casts = [
        'role' => Role::class,
        'isActive' => 'boolean',
        'motDePasse' => 'hashed',
    ];

    // Relations
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
<<<<<<< HEAD
 
    // Un utilisateur (acheteur) possède un seul panier
=======

>>>>>>> f8815316218828b93ef82217d1fc4a0c270a2d73
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
<<<<<<< HEAD
    public function commentairesPublies()
    {
        return $this->hasMany(Commentaire::class, 'acheteurId');
    }
}
=======

    //  Méthode pour compatibilité mot de passe Laravel
    public function getAuthPassword()
    {
        return $this->motDePasse;
    }
}
>>>>>>> f8815316218828b93ef82217d1fc4a0c270a2d73
