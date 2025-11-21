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
    /**
     * Lister toutes les publications (avec images et region)
     * 🚀 Correction de l'URL d'image en lecture.
     */
    public function index()
    {
        $photos = Photo::with(['images', 'region', 'admin'])->latest()->get();
        
        // CORRECTION DE L'URL DANS LA RÉPONSE JSON
        // Utilise config('app.url') pour obtenir l'adresse IP correcte du .env
        $baseUrl = config('app.url') . '/storage/photos/';

        $photos->map(function ($photo) use ($baseUrl) {
            $photo->images->map(function ($image) use ($baseUrl) {
                // Surcharge de l'attribut 'url' de l'objet image avant l'envoi
                $image->url = $baseUrl . $image->filename;
                return $image;
            });
            return $photo;
        });

        return response()->json($photos);
    }

    /**
     * Lister une publication spécifique
     * 🚀 Correction de l'URL d'image en lecture.
     */
    public function show($id)
    {
        $photo = Photo::with(['images', 'region', 'admin'])->findOrFail($id);
        
        // CORRECTION DE L'URL POUR UNE SEULE PUBLICATION
        $baseUrl = config('app.url') . '/storage/photos/';
        $photo->images->map(function ($image) use ($baseUrl) {
            $image->url = $baseUrl . $image->filename;
            return $image;
        });

        return response()->json($photo);
    }

    /**
     * Créer une publication avec plusieurs images (Multipart Form Data)
     * L'URL stockée peut être erronée, mais elle est corrigée en lecture (index/show/update).
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region' => 'required|string|max:150', // nom region
            'images' => 'required|array',
            'images.*' => 'image|max:5120' // chaque image max 5MB
        ]);

        return DB::transaction(function() use ($request) {
            // 1) Trouver ou créer la région
            $regionNom = trim($request->input('region'));
            $region = Region::firstOrCreate(
                ['nom' => $regionNom],
                ['id' => Str::uuid(), 'nom' => $regionNom]
            );

            // 2) Créer la publication (photo)
            $photo = Photo::create([
                'id' => Str::uuid(),
                'titre' => $request->input('titre'),
                'description' => $request->input('description'),
                'regionId' => $region->id,
                'publieParAdminId' => $request->input('publieParAdminId') ?? null
            ]);

            // 3) Stocker les images
            $files = $request->file('images');
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                $ext = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $ext;
                $path = $file->storeAs('photos', $filename, 'public'); // disque public
                
                // On utilise url() qui pourrait donner l'ancienne IP, mais on corrigera en lecture
                $url = url('storage/' . $path); 

                PhotoImage::create([
                    'id' => Str::uuid(),
                    'photoId' => $photo->id,
                    'filename' => $filename,
                    'url' => $url, // Peut stocker 127.0.0.1:8000
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'order' => $index,
                ]);
            }

            // Correction de l'URL pour la réponse 201
            $baseUrl = config('app.url') . '/storage/photos/';
            $photo->load('images','region')->images->map(function ($image) use ($baseUrl) {
                $image->url = $baseUrl . $image->filename;
                return $image;
            });

            return response()->json($photo, 201);
        });
    }

    /**
     * Mettre à jour titre/description/region + ajouter images optionnel
     * 🚀 Correction de l'URL d'image en lecture.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:150',
            'images' => 'nullable|array', // Autoriser un tableau d'images
            'images.*' => 'image|max:5120'
        ]);

        $photo = Photo::findOrFail($id);

        return DB::transaction(function() use ($request, $photo) {
            
            // 1) Mise à jour des champs texte et région
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

            // 2) Ajouter de nouvelles images si présentes
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                if (!is_array($files)) $files = [$files];
                $lastOrder = $photo->images()->max('order') ?? 0;
                
                foreach ($files as $file) {
                    $ext = $file->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $ext;
                    $path = $file->storeAs('photos', $filename, 'public');
                    $url = url('storage/' . $path); // Peut stocker 127.0.0.1:8000

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
            
            // CORRECTION DE L'URL APRÈS MISE À JOUR pour la réponse JSON
            $photo->load('images','region');
            $baseUrl = config('app.url') . '/storage/photos/';
            $photo->images->map(function ($image) use ($baseUrl) {
                $image->url = $baseUrl . $image->filename;
                return $image;
            });

            return response()->json($photo);
        });
    }

    /**
     * Supprimer la publication et ses images (et fichiers disque)
     */
    public function destroy($id)
    {
        $photo = Photo::with('images')->findOrFail($id);

        // Suppression des fichiers physiques et des entrées DB PhotoImage
        foreach ($photo->images as $img) {
            $path = 'photos/' . $img->filename;
            Storage::disk('public')->delete($path);
            $img->delete();
        }

        $photo->delete();

        return response()->json(['message' => 'Publication supprimée avec succès.']);
    }

    /**
     * Supprimer une image précise si besoin
     */
    public function deleteImage($imageId)
    {
        $img = PhotoImage::findOrFail($imageId);
        Storage::disk('public')->delete('photos/' . $img->filename);
        $img->delete();
        return response()->json(['message' => 'Image supprimée']);
    }
}