<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Pivots\MusiqueArtiste;

class Artiste extends Model
{
    use HasUuids;

    protected $table = 'artistes';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['nom', 'bio', 'region'];

    //plusieurs artistes peuvent faire plusieurs musiques
    public function musiques()
    {
        return $this->belongsToMany(Musique::class, 'musique_artistes', 'artisteId', 'musiqueId')
            ->using(MusiqueArtiste::class);
    }
}