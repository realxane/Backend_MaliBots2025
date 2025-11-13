<?php

namespace App\Models;

use App\Enums\MethodePaiement;
use App\Enums\StatutCommande;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Commande extends Model
{
    use HasUuids;

    protected $table = 'commandes';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'acheteurId', 'montantTotal', 'methodePaiement', 'statut',
    ];

    protected $casts = [
        'montantTotal' => 'decimal:2',
        'methodePaiement' => MethodePaiement::class,
        'statut' => StatutCommande::class,
    ];

    //Une commande concerne un acheteur
    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteurId');
    }

    //Une commande a plusieurs commandeitem
    public function items()
    {
        return $this->hasMany(CommandeItem::class, 'commandeId');
    }

    //Une commande a un seul paiement
    public function paiements() 
    {
        return $this->hasOne(Paiement::class, 'commandeId'); 
    }
}