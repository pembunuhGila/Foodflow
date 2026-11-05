<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        // Rate limiting untuk mengalihkan ke Google (maks 10 attempt per menit per IP)
        $throttleKey = 'socialite-redirect|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return redirect()->route('login')->withErrors([
                'email' => "Terlalu banyak permintaan login Google. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }
        RateLimiter::hit($throttleKey, 60);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate.
     * Sesuai Opsi A: Hanya pengguna yang sudah didaftarkan oleh Admin yang dapat masuk.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $throttleKey = 'socialite-callback|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return redirect()->route('login')->withErrors([
                'email' => "Terlalu banyak percobaan autentikasi. Silakan tunggu {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();

            // 1. Cari user di sistem FoodFlow: berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', Str::lower($googleUser->getEmail()))
                ->first();

            // 2. Jika akun belum terdaftar oleh Admin, tolak (Opsi A: Akun hanya dibuat oleh Admin)
            if (! $user) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Google ('.$googleUser->getEmail().') belum didaftarkan oleh Administrator FoodFlow. Silakan hubungi Admin untuk dibuatkan akun.',
                ]);
            }

            // 3. Validasi status akun (harus aktif)
            if (! $user->isActive()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda berstatus tidak aktif. Silakan hubungi Administrator FoodFlow.',
                ]);
            }

            // 4. Update tautan google_id dan avatar jika belum terhubung
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?? $user->avatar,
            ]);

            RateLimiter::clear($throttleKey);

            // 5. Login pengguna
            Auth::login($user, remember: true);
            $request->session()->regenerate();

            return redirect()->intended(route($user->getDashboardRoute(), absolute: false))
                ->with('status', 'Berhasil masuk menggunakan akun Google ('.$user->email.').');

        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan autentikasi Google: '.$e->getMessage(),
            ]);
        }
    }
}
