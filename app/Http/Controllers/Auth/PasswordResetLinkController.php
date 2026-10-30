<?php

namespace App\Http\Controllers\Auth;

use App\Enums\PasswordResetStatus;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Tampilkan halaman formulir pengajuan lupa password.
     * Sesuai UC-02: Lupa Password Langkah 1 & 2.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Tangani pengajuan pemulihan password oleh pengguna.
     * Sesuai UC-02: Lupa Password Langkah 3, 4, 5 & Alur Alternatif 4a.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Alur Alternatif 4a: Email tidak terdaftar
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Alamat email tidak ditemukan dalam sistem.',
            ]);
        }

        // Catat permohonan pemulihan password untuk diperiksa oleh Admin
        PasswordResetRequest::updateOrCreate(
            [
                'user_id' => $user->id,
                'status' => PasswordResetStatus::Diajukan,
            ],
            [
                'email' => $user->email,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return back()->with('status', 'Permintaan pemulihan kata sandi telah dikirimkan kepada Admin. Admin akan memeriksa dan menyiapkan kata sandi baru untuk akun Anda.');
    }
}
