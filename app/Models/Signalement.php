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
        'utilisateurId', 'cibleType', 'cibleId', 'statut',
        'traiteParAdminId', 'dateTraitement', 
    ]; 

    protected $casts = [
        'cibleType' => TypeSignalement::class,
        'statut' => StatutSignalement::class,
        'dateTraitement' => 'datetime',
    ];

    //Un signalement concerne un utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateurId');
    }

    //Un signalement est traité par un admin
    public function traiteParAdmin()
    {
        return $this->belongsTo(User::class, 'traiteParAdminId');
    }
}