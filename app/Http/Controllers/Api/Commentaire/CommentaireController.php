<?php

namespace App\Http\Controllers\Api\Commentaire;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Produit;
use App\Models\Commentaire;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    use AuthorizesRequests; 

    // GET /produits/{produit}/commentaires
    public function index(Produit $produit)
    {
        $commentaires = $produit->commentaires()
            ->with('auteur:id,nom')   // ton User a un champ 'nom'
            ->latest()
            ->paginate(10);

        return response()->json($commentaires);
    }

    // POST /produits/{produit}/commentaires
    public function store(Request $request, Produit $produit)
    {
        $user = $request->user();

        $validated = $request->validate([
            'contenu' => ['required', 'string', 'min:2', 'max:3000'],
        ]);

        $commentaire = $produit->commentaires()->create([
            'contenu'    => $validated['contenu'],
            'acheteurId' => $user->id,       
        ]);

        return response()->json($commentaire->load('auteur:id,nom'), 201);
    } 

    // GET vendeur/produits/{produit}/commentaires/{commentaire}
    public function show(Produit $produit, Commentaire $commentaire)
    {
        $this->ensureBelongsToProduit($produit, $commentaire);

        return response()->json($commentaire->load('auteur:id,nom'));
    }

    // // PUT/PATCH /produits/{produit}/commentaires/{commentaire}
    // public function update(Request $request, Produit $produit, Commentaire $commentaire)
    // {
    //     $this->ensureBelongsToProduit($produit, $commentaire);

    //     $user = $request->user();

    //     // Authorisation: propriétaire du commentaire OU policy/gate
    //     if ($commentaire->acheteurId !== $user->id && ! $user->can('update', $commentaire)) {
    //         abort(403, 'Action non autorisée.');
    //     }

    //     $validated = $request->validate([
    //         'contenu' => ['required', 'string', 'min:2', 'max:3000'],
    //     ]);

    //     $commentaire->update(['contenu' => $validated['contenu']]);

    //     return response()->json($commentaire->fresh()->load('auteur:id,nom'));
    // }

    // DELETE /produits/{produit}/commentaires/{commentaire}
    public function destroy(Request $request, Produit $produit, Commentaire $commentaire)
    {
        $this->ensureBelongsToProduit($produit, $commentaire);

        $user = $request->user();

        if ($commentaire->acheteurId !== $user->id && ! $user->can('delete', $commentaire)) {
            abort(403, 'Action non autorisée.');
        }

        $commentaire->delete(); // SoftDeletes actif sur le modèle

        return response()->noContent();
    }

    // Sécurité: s’assure que l’URL et l’instance correspondent
    protected function ensureBelongsToProduit(Produit $produit, Commentaire $commentaire): void
    {
        if ((string) $commentaire->produitId !== (string) $produit->id) {
            abort(404); // le commentaire n'appartient pas à ce produit
        }
    }
}