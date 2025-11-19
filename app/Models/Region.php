<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Region extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'regions';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['nom'];

    public function users()    { return $this->hasMany(User::class, 'regionId'); }
    public function produits() { return $this->hasMany(Produit::class, 'regionId'); }
    public function artistes() { return $this->hasMany(Artiste::class, 'regionId'); }
    public function contes()   { return $this->hasMany(Conte::class, 'regionId'); }
    public function proverbes(){ return $this->hasMany(Proverbe::class, 'regionId'); }
    public function photos()   { return $this->hasMany(Photo::class, 'regionId'); }
} 