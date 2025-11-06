<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Conte extends ContenuCulturel
{
    use HasUuids;

    protected $table = 'contes';

    protected $fillable = [
        'titre', 'regionId', 'publieParAdminId',
        'histoire', 'langue',
    ];

    //Un conte a une région
    public function regions()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }
} 