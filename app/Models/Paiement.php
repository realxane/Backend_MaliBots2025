<?php

namespace App\Models;

use App\Enums\MethodePaiement;
use App\Enums\StatutPaiement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Paiement extends Model
{
    use HasUuids;

    protected $table = 'paiements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'commandeId', 'montant', 'methode', 'statut', 'referenceFournisseur', 
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'methode' => MethodePaiement::class,
        'statut' => StatutPaiement::class, 
    ];

    //Un paiement concerne une commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commandeId');
    }
}