<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Proverbe extends ContenuCulturel
{
    use HasUuids;

    protected $table = 'proverbes';

    protected $fillable = [
        'regionId', 'publieParAdminId',
        'texte', 'langue',
    ];

    //Un proverbe a une région
    public function regions()
    {
        return $this->belongsTo(Region::class, 'regionId');
    } 
}