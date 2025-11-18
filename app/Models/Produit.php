<?php

namespace App\Models;

use App\Enums\CategorieProduit;
use App\Enums\StatutProduit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'produits';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nom', 'description', 'prix', 'categorie', 'regionId',
        // imageUrl est déprécié, conservé pour compat
        'imageUrl',
        'statut', 'vendeurId', 'stock',
        // ratings sont maintenus par le système, pas par l’API publique
        'rating_avg', 'rating_count',
    ];

    protected $casts = [
        'prix'        => 'decimal:2',
        'categorie'   => CategorieProduit::class,
        'statut'      => StatutProduit::class,
        'stock'       => 'integer',
        'rating_avg'  => 'float',
        'rating_count'=> 'integer',
    ];

    // Un produit a une région (nom singulier pour belongsTo)
    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    // Un produit appartient à un vendeur
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeurId');
    }

    // Images multiples
    public function images()
    {
        return $this->hasMany(ProduitImage::class, 'produitId')->orderBy('position');
    }

    // Accessor pratique pour la première image (thumbnail)
    public function getFirstImageUrlAttribute(): ?string
    {
        // Si relation chargée, pas de requête supplémentaire
        if ($this->relationLoaded('images')) {
            return optional($this->images->sortBy('position')->first())->url
                ?? $this->imageUrl; // compat: fallback
        }

        return $this->images()->orderBy('position')->value('url') ?? $this->imageUrl;
    }

    // Un produit peut avoir plusieurs validations
    public function validations()
    {
        return $this->hasMany(ValidationProduit::class, 'produitId');
    }

    public function suppression()
    {
        return $this->hasOne(SuppressionProduit::class, 'produitId');
    }

    public function panierItems()
    {
        return $this->hasMany(PanierItem::class, 'produitId');
    }

    public function commandeItems()
    {
        return $this->hasMany(CommandeItem::class, 'produitId');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'produitId');
    }

    // Méthode utilitaire si tu veux recalculer les ratings à partir des commentaires (champ "note" requis)
    public function recalcRatingsFromCommentaires(): void
    {
        $avg = (float) $this->commentaires()->avg('note');
        $count = (int) $this->commentaires()->whereNotNull('note')->count();

        $this->forceFill([
            'rating_avg'   => round($avg, 2) ?: 0,
            'rating_count' => $count,
        ])->save();
    }
}