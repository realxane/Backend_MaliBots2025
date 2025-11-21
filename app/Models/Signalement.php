<?php

namespace App\Models;

use App\Enums\StatutSignalement;
use App\Enums\TypeSignalement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Signalement extends Model
{
    use HasUuids;

    protected $table = 'signalements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'utilisateurId',
        'cibleType',
        'cibleId',
        'statut',
        'traiteParAdminId',
        'dateTraitement',
    ];

    protected $casts = [
        'cibleType' => TypeSignalement::class,
        'statut' => StatutSignalement::class,
        'dateTraitement' => 'datetime',
    ];

    // Valeur par défaut du statut
    protected $attributes = [
        'statut' => StatutSignalement::Ouvert,
    ];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateurId');
    }

    public function traiteParAdmin()
    {
        return $this->belongsTo(User::class, 'traiteParAdminId');
    }
}
