<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleLoginController extends Controller
{
    // REDIRECCIÓN A GOOGLE
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // CALLBACK DE GOOGLE → GENERA JWT Y REDIRIGE CON TOKEN
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleId = $googleUser->getId();
            $email    = $googleUser->getEmail();
            $name     = $googleUser->getName();
            $avatar   = $googleUser->getAvatar();

            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $name,
                    'email'             => $email,
                    'google_id'         => $googleId,
                    'avatar'            => $avatar,
                    'password'          => bcrypt(str()->random(16)),
                    'email_verified_at' => now(),
                ]);
            } else {
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                    $user->avatar = $avatar;
                    $user->save();
                }
            }

            // ✅ GENERAMOS JWT PURO
            $token = JWTAuth::fromUser($user);

            // ✅ REDIRIGIMOS A REACT CON EL TOKEN EN LA URL (solo para pruebas)
            return redirect("http://localhost:5173/dashboard?token={$token}");
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al autenticarse con Google',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
