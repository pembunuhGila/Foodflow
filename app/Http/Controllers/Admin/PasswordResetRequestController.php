<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PasswordResetStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessPasswordResetRequest;
use App\Models\PasswordResetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetRequestController extends Controller
{
    /**
     * Tampilkan daftar seluruh permohonan pemulihan kata sandi.
     * Sesuai UC-02 Langkah 6.
     */
    public function index(Request $request): View
    {
        $query = PasswordResetRequest::with(['user', 'processor'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        $pendingCount = PasswordResetRequest::where('status', PasswordResetStatus::Diajukan)->count();

        return view('admin.password-resets.index', [
            'requests' => $requests,
            'statuses' => PasswordResetStatus::cases(),
            'pendingCount' => $pendingCount,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Admin menyetujui dan membuat kata sandi baru untuk pengguna.
     * Sesuai UC-02 Langkah 7 & 8.
     */
    public function approve(ProcessPasswordResetRequest $request, PasswordResetRequest $passwordReset): RedirectResponse
    {
        $validated = $request->validated();
        $user = $passwordReset->user;

        // Perbarui password user
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Perbarui status permohonan menjadi Selesai
        $passwordReset->update([
            'status' => PasswordResetStatus::Selesai,
            'processed_by' => $request->user()->id,
            'admin_notes' => $validated['notes'] ?? 'Kata sandi baru telah dibuatkan oleh Admin.',
            'processed_at' => now(),
        ]);

        return redirect()
            ->route('admin.password-resets.index')
            ->with('status', "Permintaan pemulihan untuk pengguna {$user->name} berhasil diselesaikan. Kata sandi baru telah aktif.");
    }

    /**
     * Admin menolak permohonan pemulihan kata sandi.
     * Sesuai UC-02 Alur Alternatif 7a.
     */
    public function reject(Request $request, PasswordResetRequest $passwordReset): RedirectResponse
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $passwordReset->update([
            'status' => PasswordResetStatus::Gagal,
            'processed_by' => $request->user()->id,
            'admin_notes' => $request->input('notes', 'Permintaan belum dapat diproses oleh Admin.'),
            'processed_at' => now(),
        ]);

        return redirect()
            ->route('admin.password-resets.index')
            ->with('status', "Permintaan pemulihan untuk email {$passwordReset->email} telah ditolak/dibatalkan.");
    }
}
