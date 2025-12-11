<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class LoginController extends Controller
{
    // LOGIN NORMAL → GUARDA JWT EN COOKIE httpOnly
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');


        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // ✅ Crear cookie httpOnly con el JWT
        $cookie = Cookie::make(
            'token',        // nombre
            $token,         // valor (JWT)
            60 * 24 * 7,    // 7 días
            '/',            // path
            null,           // domain
            false,          // secure (true en HTTPS)
            true,           // ✅ httpOnly
            false,
            'Strict'
        );

        return response()->json([
            'message' => 'Usuario logueado exitosamente',
            'user'    => Auth::user()
        ])->withCookie($cookie);
    }

    // LOGOUT → INVALIDA JWT + BORRA COOKIE
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        $cookie = Cookie::forget('token');

        return response()->json([
            'message' => 'Sesión cerrada'
        ])->withCookie($cookie);
    }

    public function register(Request $request)
    {
        // ✅ Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // ✅ Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ✅ Generar token automáticamente
        $token = JWTAuth::fromUser($user);

        // ✅ Crear cookie httpOnly con el JWT
        $cookie = Cookie::make(
            'token',        // nombre
            $token,         // valor (JWT)
            60 * 24 * 7,    // 7 días
            '/',            // path
            null,           // domain
            false,          // secure (true en HTTPS)
            true,           // ✅ httpOnly
            false,
            'Strict'
        );

        return response()->json([
            'message' => 'Usuario registrado y autenticado correctamente',
            'user'    => $user
        ])->withCookie($cookie);
    }

    // PERFIL DEL USUARIO (PROTEGIDO)
    public function me(Request $request)
    {
        logger('🍪 Cookie recibida en /api/me', [
            'cookie_token' => $request->cookie('token'),
            'auth_header'  => $request->header('Authorization'),
        ]);
        return response()->json(Auth::user());
    }
}
