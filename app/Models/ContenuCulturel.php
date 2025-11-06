<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ContenuCulturel extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    //Un contenu culturel est publié par un admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'publieParAdminId');
    }
} 