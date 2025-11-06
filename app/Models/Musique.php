<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Pivots\MusiqueGenre;
use App\Models\Pivots\MusiqueArtiste;

class Musique extends Model
{
    use HasUuids;

    protected $table = 'musiques';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'titre', 'fichierUrl', 'couvertureUrl', 'duree', 'dateSortie',
    ];

    protected $casts = [
        'duree' => 'integer',       // en secondes
        'dateSortie' => 'datetime',
    ];

    //Une musique appartient à plusieurs genres 
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'musique_genres', 'musiqueId', 'genreId')
            ->using(MusiqueGenre::class);
    }

    //Une musique peut être jouée par plusieurs artistes
    public function artistes()
    {
        return $this->belongsToMany(Artiste::class, 'musique_artistes', 'musiqueId', 'artisteId')
            ->using(MusiqueArtiste::class);
    }
}