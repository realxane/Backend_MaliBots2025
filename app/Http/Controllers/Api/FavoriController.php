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
        $userId = $request->user()->id;

        $query = Favori::where('utilisateurId', $userId)
            ->when($request->filled('type'), function ($q) use ($request) {
                $type = TypeFavori::tryFrom($request->input('type'));
                if ($type) $q->where('cibleType', $type->value);
            })
            ->orderByDesc('created_at');

        $perPage = min(max((int)$request->input('perPage', 20),1),100);

        return FavoriResource::collection($query->paginate($perPage));
    }

    // POST /favoris
    public function store(StoreFavoriRequest $request)
    {
        $data = $request->validated();
        $data['utilisateurId'] = $request->user()->id;

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
        $data = $request->validated();
        $userId = $request->user()->id;

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