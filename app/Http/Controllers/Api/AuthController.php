<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login API dan penerbitan Personal Access Token Sanctum (Materi Pertemuan 10 - Langkah 2 & 9).
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Kredensial tidak valid.',
            ], 401);
        }

        if (! $user->isActive()) {
            return response()->json([
                'message' => 'Akun dinonaktifkan oleh administrator.',
            ], 403);
        }

        // Tentukan abilities berdasarkan role
        $abilities = ['ingredients:read', 'products:read', 'orders:read'];
        if ($user->isAdmin() || $user->isGudang()) {
            $abilities[] = 'ingredients:write';
            $abilities[] = 'stocks:adjust';
        }
        if ($user->isAdmin() || $user->isProduksi()) {
            $abilities[] = 'products:write';
            $abilities[] = 'orders:create';
        }

        // Terbitkan token Sanctum dengan masa berlaku 24 jam (Pertemuan 10 Langkah 9)
        $token = $user->createToken(
            'api-token',
            $abilities,
            now()->addDay()
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addDay()->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
        ]);
    }

    /**
     * Logout API dan pencabutan token (Materi Pertemuan 10 - Langkah 2).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
