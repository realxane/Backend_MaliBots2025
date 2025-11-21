<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreFavoriRequest;
use App\Http\Resources\FavoriResource;
use App\Models\Favori;
use App\Enums\TypeFavori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;

class FavoriController extends Controller
{
    use AuthorizesRequests; 
    
    // GET /favoris?type=Produit
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $query = Favori::where('utilisateurId', $user->id)
            ->when($request->filled('type'), function ($q) use ($request) {
                $typeStr = $request->input('type');
                $type = TypeFavori::tryFrom($typeStr);
                if ($type) {
                    $q->where('cibleType', $type->value);
                }
            })
            ->orderByDesc('created_at');

        $perPage = (int) $request->input('perPage', 20);
        $perPage = max(1, min($perPage, 100));

        $paginator = $query->paginate($perPage);

        return FavoriResource::collection($paginator);
    }

    // POST /favoris
    public function store(StoreFavoriRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();
        $data['utilisateurId'] = $user->id;

        // éviter les doublons (unique index)
        $favori = Favori::firstOrCreate(
            [
                'utilisateurId' => $data['utilisateurId'],
                'cibleType'     => $data['cibleType'],
                'cibleId'       => $data['cibleId'],
            ],
            []
        );

        return (new FavoriResource($favori))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    // DELETE /favoris/{favori}
    public function destroy(Request $request, Favori $favori)
    {
        $this->authorize('delete', $favori);
        $favori->delete();
        return response()->noContent();
    }

    // POST /favoris/toggle
    public function toggle(StoreFavoriRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();
        $userId = $user->id;

        $existing = Favori::where('utilisateurId', $userId)
            ->where('cibleType', $data['cibleType'])
            ->where('cibleId', $data['cibleId'])
            ->first();

        if ($existing) {
            $this->authorize('delete', $existing);
            $existing->delete();
            return response()->json(['toggled' => 'removed']);
        }

        $favori = Favori::create([
            'utilisateurId' => $userId,
            'cibleType'     => $data['cibleType'],
            'cibleId'       => $data['cibleId'],
        ]);

        return response()->json([
            'toggled' => 'added',
            'favori'  => new FavoriResource($favori),
        ], Response::HTTP_CREATED);
    }
}