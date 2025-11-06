<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MusiqueGenre extends Pivot
{
    protected $table = 'musique_genres';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['musiqueId', 'genreId'];
}