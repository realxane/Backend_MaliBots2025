<?php

namespace App\Models;

use App\Enums\TypeFavori;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Favori extends Model
{
    use HasUuids;

    protected $table = 'favoris';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'utilisateurId', 'cibleType', 'cibleId', 
    ];

    protected $casts = [
        'cibleType' => TypeFavori::class, 
    ];

    //Un favori concerne un utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateurId');
    }
}