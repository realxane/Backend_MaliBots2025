<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MusiqueArtiste extends Pivot
{
    protected $table = 'musique_artistes';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['musiqueId', 'artisteId'];
}