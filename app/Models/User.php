<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable; // Pour l'authentification
use Illuminate\Notifications\Notifiable;
use App\Enums\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    /**
     * Laravel n'utilise pas d'auto-incrément ici, car l'ID est un UUID
     */
    public $incrementing = false;

    /**
     * Le type de la clé primaire
     */
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

    /**
     * Champs à cacher lors de la sérialisation
     */
    protected $hidden = [
        'motDePasse',
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
}
