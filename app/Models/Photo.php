<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Photo extends ContenuCulturel
{
    use HasUuids;

    protected $table = 'photos';

    protected $fillable = [
        'titre', 'regionId', 'publieParAdminId',
        'url', 'description',
    ];

    //Une photo a une région
    public function regions()
    {
        return $this->belongsTo(Region::class, 'regionId');
    } 
}