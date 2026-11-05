<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoRoleSwitcherController extends Controller
{
    /**
     * Beralih profil role secara instan untuk kebutuhan presentasi & demo dosen/penguji (FR-Demo).
     * Mencegah presenter harus repot logout dan login berulang kali.
     */
    public function switchRole(Request $request, string $role): RedirectResponse
    {
        $roleMap = [
            'admin' => [
                'email' => 'admin@foodflow.test',
                'enum' => UserRole::Admin,
                'label' => 'Administrator',
            ],
            'gudang' => [
                'email' => 'gudang@foodflow.test',
                'enum' => UserRole::Gudang,
                'label' => 'Staf Gudang & Logistik',
            ],
            'produksi' => [
                'email' => 'produksi@foodflow.test',
                'enum' => UserRole::Produksi,
                'label' => 'Staf Produksi & Dapur',
            ],
            'owner' => [
                'email' => 'owner@foodflow.test',
                'enum' => UserRole::Owner,
                'label' => 'Owner / Direktur',
            ],
        ];

        $roleKey = strtolower($role);

        if (! array_key_exists($roleKey, $roleMap)) {
            return back()->withErrors(['demo' => 'Peran demo yang diminta tidak valid.']);
        }

        $targetConfig = $roleMap[$roleKey];

        // 1. Cari berdasarkan email resmi demo seeder terlebih dahulu
        $user = User::where('email', $targetConfig['email'])->first();

        // 2. Fallback: cari user aktif dengan role yang sesuai jika email diubah
        if (! $user) {
            $user = User::where('role', $targetConfig['enum'])
                ->where('status', UserStatus::Aktif)
                ->first();
        }

        if (! $user) {
            return back()->withErrors(['demo' => "Akun untuk peran {$targetConfig['label']} belum terdaftar di database."]);
        }

        // Beralih sesi login
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->route($user->getDashboardRoute())
            ->with('status', "Beralih ke profil demo: {$user->name} ({$targetConfig['label']})");
    }
}
