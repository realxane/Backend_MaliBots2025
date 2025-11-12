<?php

namespace App\Http\Controllers\Api\galerie;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\PhotoImage;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PhotoController extends Controller
{
    // Lister toutes les publications (avec images et region)
    public function index()
    {
        $photos = Photo::with(['images', 'region', 'admin'])->latest()->get();
        return response()->json($photos);
    }

    // Lister une publication
    public function show($id)
    {
        $photo = Photo::with(['images', 'region', 'admin'])->findOrFail($id);
        return response()->json($photo);
    }

    // Créer une publication avec plusieurs images
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region' => 'required|string|max:150', // nom region
            'images' => 'required',
            'images.*' => 'image|max:5120' // chaque image max 5MB
        ]);

        return DB::transaction(function() use ($request) {
            // 1) trouver ou créer la région par nom (lowercase trim)
            $regionNom = trim($request->input('region'));
            $region = Region::firstOrCreate(
                ['nom' => $regionNom],
                ['id' => Str::uuid(), 'nom' => $regionNom]
            );

            // 2) créer la publication (photo)
            $photo = Photo::create([
                'id' => Str::uuid(),
                'titre' => $request->input('titre'),
                'description' => $request->input('description'),
                'regionId' => $region->id,
                // publieParAdminId: si tu as un admin connecté, mettre $request->user()->id
                'publieParAdminId' => $request->input('publieParAdminId') ?? null
            ]);

            // 3) stocker les images
            $files = $request->file('images');
            // si Postman envoie un seul fichier, $files peut être UploadedFile unique
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                $ext = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $ext;
                $path = $file->storeAs('photos', $filename, 'public'); // disque public
                $url = url('storage/' . $path);

                PhotoImage::create([
                    'id' => Str::uuid(),
                    'photoId' => $photo->id,
                    'filename' => $filename,
                    'url' => $url,
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'order' => $index,
                ]);
            }

            return response()->json($photo->load('images','region'), 201);
        });
    }

    // Mettre à jour titre/description/region + ajouter/suppr images optionnel
    public function update(Request $request, $id)
    {
        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:150',
            'images.*' => 'image|max:5120'
        ]);

        $photo = Photo::findOrFail($id);

        return DB::transaction(function() use ($request, $photo) {
            if ($request->has('titre')) $photo->titre = $request->input('titre');
            if ($request->has('description')) $photo->description = $request->input('description');

            if ($request->filled('region')) {
                $regionNom = trim($request->input('region'));
                $region = Region::firstOrCreate(
                    ['nom' => $regionNom],
                    ['id' => Str::uuid(), 'nom' => $regionNom]
                );
                $photo->regionId = $region->id;
            }

            $photo->save();

            // Si on a des images à ajouter
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                if (!is_array($files)) $files = [$files];
                $lastOrder = $photo->images()->max('order') ?? 0;
                foreach ($files as $file) {
                    $ext = $file->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $ext;
                    $path = $file->storeAs('photos', $filename, 'public');
                    $url = url('storage/' . $path);

                    PhotoImage::create([
                        'id' => Str::uuid(),
                        'photoId' => $photo->id,
                        'filename' => $filename,
                        'url' => $url,
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'order' => ++$lastOrder,
                    ]);
                }
            }

            return response()->json($photo->load('images','region'));
        });
    }

    // Supprimer la publication et ses images (et fichiers disque)
    public function destroy($id)
    {
        $photo = Photo::with('images')->findOrFail($id);

        foreach ($photo->images as $img) {
            // supprime le fichier réel
            $path = 'photos/' . $img->filename;
            Storage::disk('public')->delete($path);
            $img->delete();
        }

        $photo->delete();

        return response()->json(['message' => 'Publication supprimée avec succès.']);
    }

    // Supprimer une image précise si besoin
    public function deleteImage($imageId)
    {
        $img = PhotoImage::findOrFail($imageId);
        Storage::disk('public')->delete('photos/' . $img->filename);
        $img->delete();
        return response()->json(['message' => 'Image supprimée']);
    }
}
