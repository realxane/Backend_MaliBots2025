<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    // POST /api/uploads  (champ form-data: "file")
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,avif,gif|max:5120', // 5 Mo
        ]);

        $file = $request->file('file');

        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $dir = 'uploads/' . now()->format('Y/m/d');
        $name = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $ext;

        // stocke sur le disk "public" => storage/app/public/...
        $path = $file->storeAs($dir, $name, 'public');

        // URL publique (nécessite php artisan storage:link)
        $publicPath = Storage::disk('public')->url($path);     // ex: /storage/uploads/2025/11/18/xxxx.jpg
        $absoluteUrl = url($publicPath);                       // ex: http://127.0.0.1:8000/storage/...

        return response()->json([
            'url' => $absoluteUrl,
            'path' => $path, // utile pour debug/suppression ultérieure
        ], 201);
    }

    // Optionnel: multiple files => POST /api/uploads/many (champ form-data: "files[]")
    public function storeMany(Request $request)
    {
        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,avif,gif|max:5120',
        ]);

        $urls = [];
        foreach ($request->file('files') as $file) {
            $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
            $dir = 'uploads/' . now()->format('Y/m/d');
            $name = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $ext;
            $path = $file->storeAs($dir, $name, 'public');
            $urls[] = url(Storage::disk('public')->url($path));
        }

        return response()->json([
            'urls' => $urls,
        ], 201);
    }
}