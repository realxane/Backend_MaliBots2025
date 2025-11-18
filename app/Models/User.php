<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasUuids, HasApiTokens, Notifiable, CanResetPassword;

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

    protected $hidden = [
        'motDePasse',
        'remember_token',
    ];

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

    //  Méthode pour compatibilité mot de passe Laravel
    public function getAuthPassword()
    {
        return $this->motDePasse;
    }
}
