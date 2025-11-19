<?php

namespace App\Http\Controllers\Api\mdpO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Générer le token
        $token = Str::random(64);

        // Sauvegarder dans password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Lien à envoyer
        $url = "https://anwkadembe.app/reset-password?token=$token&email=" . urlencode($user->email);

        // Envoi de l’email
        Mail::to($user->email)->send(new ResetPasswordMail($url));

        return response()->json(['message' => 'Lien de réinitialisation envoyé avec succès.']);
    }
}
