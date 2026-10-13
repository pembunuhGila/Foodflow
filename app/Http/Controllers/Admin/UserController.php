<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna dengan filter dan pencarian.
     * Sesuai UC-22: Mengelola Data Pengguna dan Hak Akses.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Tampilkan form registrasi akun baru oleh Admin.
     * Sesuai UC-21: Register Akun.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    /**
     * Simpan akun pengguna baru ke database.
     * Sesuai UC-21: Register Akun.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'] ?? UserStatus::Aktif,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Akun pengguna berhasil didaftarkan.');
    }

    /**
     * Tampilkan form edit pengguna.
     * Sesuai UC-22: Mengelola Data Pengguna dan Hak Akses.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    /**
     * Perbarui data pengguna.
     * Sesuai UC-22: Mengelola Data Pengguna dan Hak Akses.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Mencegah admin menonaktifkan dirinya sendiri
        if ($user->id === $request->user()->id && $validated['status'] === UserStatus::TidakAktif->value) {
            return back()->withErrors([
                'status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.',
            ]);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / tidak aktif pengguna.
     * Sesuai UC-22 Alur Alternatif 7a: Akun dinonaktifkan / diaktifkan.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors([
                'status' => 'Anda tidak dapat mengubah status akun Anda sendiri.',
            ]);
        }

        $newStatus = $user->isActive() ? UserStatus::TidakAktif : UserStatus::Aktif;
        $user->update(['status' => $newStatus]);

        $statusText = $newStatus === UserStatus::Aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Akun {$user->name} berhasil {$statusText}.");
    }

    /**
     * Reset password pengguna oleh Admin.
     * Sesuai UC-22 Alur Alternatif 5a & UC-02 Alur Normal 7.
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Kata sandi untuk pengguna {$user->name} berhasil direset.");
    }
}
