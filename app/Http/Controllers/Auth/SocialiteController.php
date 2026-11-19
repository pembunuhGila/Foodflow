<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        // 1. Periksa ketersediaan kredensial Google OAuth di konfigurasi server (.env)
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret) || $clientId === 'your-google-client-id') {
            return redirect()->route('login')->withErrors([
                'email' => 'Layanan Google Sign-In belum terhubung ke Google Client ID dan Secret di server. Silakan login menggunakan email & password, atau lengkapi kredensial GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada berkas .env server.',
            ]);
        }

        // 2. Rate limiting untuk mengalihkan ke Google (maks 10 attempt per menit per IP)
        $throttleKey = 'socialite-redirect|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return redirect()->route('login')->withErrors([
                'email' => "Terlalu banyak permintaan login Google. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }
        RateLimiter::hit($throttleKey, 60);

        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $e) {
            Log::error('Gagal mengarahkan ke Google OAuth: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Tidak dapat menghubungi layanan Google OAuth ('.$e->getMessage().'). Pastikan koneksi internet server dan kredensial Google di .env valid.',
            ]);
        }
    }

    /**
     * Obtain the user information from Google and authenticate.
     * Sesuai Opsi A: Hanya pengguna yang sudah didaftarkan oleh Admin yang dapat masuk.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        // 1. Periksa jika pengguna membatalkan izin di halaman consent Google
        if ($request->has('error') || $request->input('denied')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Proses autentikasi dengan akun Google dibatalkan oleh pengguna.',
            ]);
        }

        $throttleKey = 'socialite-callback|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return redirect()->route('login')->withErrors([
                'email' => "Terlalu banyak percobaan autentikasi. Silakan tunggu {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

        try {
            // Ambil data user dari Google: coba stateful terlebih dahulu, fallback ke stateless jika session state hilang di server deploy (proxy/SameSite)
            try {
                /** @var \Laravel\Socialite\Two\User $googleUser */
                $googleUser = Socialite::driver('google')->user();
            } catch (InvalidStateException $e) {
                Log::warning('Socialite state mismatch, mencoba verifikasi stateless: '.$e->getMessage());
                /** @var \Laravel\Socialite\Two\User $googleUser */
                $googleUser = Socialite::driver('google')->stateless()->user();
            }

            // 2. Cari user di sistem FoodFlow: berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', Str::lower($googleUser->getEmail()))
                ->first();

            // 3. Sesuai Aturan Bisnis (PRD FR-04 & UC-21): Hanya akun yang sudah didaftarkan oleh Admin yang dapat masuk.
            if (! $user) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Google ('.$googleUser->getEmail().') belum didaftarkan di sistem FoodFlow. Sesuai aturan bisnis FR-04, akun harus didaftarkan terlebih dahulu oleh Admin di menu Manajemen Pengguna.',
                ]);
            }

            // 4. Validasi status akun (harus aktif)
            if (! $user->isActive()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda berstatus tidak aktif. Silakan hubungi Administrator FoodFlow.',
                ]);
            }

            // 5. Update tautan google_id dan avatar jika belum terhubung
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?? $user->avatar,
            ]);

            RateLimiter::clear($throttleKey);

            // 6. Login pengguna
            Auth::login($user, remember: true);
            $request->session()->regenerate();
            $request->session()->save();

            // 7. Mencegah redirect loop: bersihkan url.intended jika mengarah ke halaman login atau auth Google
            $intended = session()->get('url.intended');
            if ($intended && (str_contains($intended, '/login') || str_contains($intended, '/auth/google'))) {
                session()->forget('url.intended');
                $intended = null;
            }

            $targetRoute = $intended ?: route($user->getDashboardRoute());

            return redirect()->to($targetRoute)
                ->with('status', 'Berhasil masuk menggunakan akun Google ('.$user->email.').');

        } catch (Throwable $e) {
            Log::error('Gagal autentikasi Google Callback: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan autentikasi Google: '.$e->getMessage(),
            ]);
        }
    }
}
