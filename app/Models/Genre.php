<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Pivots\MusiqueGenre;

class Genre extends Model
{
    use HasUuids;

    protected $table = 'genres';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['nom'];

    //plusieurs genres peuvent être associées à plusieurs musiques
    public function musiques()
    {
        return $this->belongsToMany(Musique::class, 'musique_genres', 'genreId', 'musiqueId')
            ->using(MusiqueGenre::class);
    }
}