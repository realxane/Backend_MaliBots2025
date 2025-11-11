<?php

namespace App\Http\Controllers\Api\Musique;
use App\Http\Controllers\Controller;
use App\Models\Musique;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MusiqueController extends Controller
{
    // Lister toutes les musiques (utilisateur)
    public function index()
    {
        $musiques = Musique::all();
        return response()->json($musiques);
    }

    // Publier une musique (admin)
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',    // Fichier image
            //'audio' => 'required|mimes:mp3,wav,ogg|max:10240', // Fichier audio max 10MB
            'audio' => 'required|file',
        ]);

        // Upload image si existe
        $urlImage = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $urlImage = url('storage/' . $path);
        }

        // Upload audio

        $extension = $request->file('audio')->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $pathAudio = $request->file('audio')->storeAs('audios', $filename, 'public');
        $urlAudio = url('storage/' . $pathAudio);
        
        $musique = Musique::create([
            'id' => Str::uuid(),
            'titre' => $request->titre,
            'artist' => $request->artist,
            'genre' => $request->genre,
            'urlImage' => $urlImage,
            'urlAudio' => $urlAudio,
        ]);

        return response()->json($musique, 201);
    }

    // Supprimer une musique (admin)
    public function destroy($id)
    {
        $musique = Musique::findOrFail($id);

        // Supprimer les fichiers associés
        if ($musique->urlImage) {
            $imagePath = str_replace(url('storage') . '/', '', $musique->urlImage);
            Storage::disk('public')->delete($imagePath);
        }
        if ($musique->urlAudio) {
            $audioPath = str_replace(url('storage') . '/', '', $musique->urlAudio);
            Storage::disk('public')->delete($audioPath);
        }

        $musique->delete();

        return response()->json(['message' => 'Musique supprimée avec succès.']);
    }
}
