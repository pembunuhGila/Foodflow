<x-app-layout>
    @section('title', 'Pengaturan Akun & Profil')

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-bold text-ink tracking-tight">Pengaturan Akun &amp; Profil</h1>
                <p class="text-sm text-muted">Perbarui data profil staf dan amankan kata sandi akses katering.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-200/70 px-3 py-1.5 text-xs font-semibold text-blue-700 self-start sm:self-auto">
                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                @php
                    $roleVal = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
                    $roleName = ['admin' => 'Admin', 'gudang' => 'Staf Gudang', 'produksi' => 'Staf Produksi', 'owner' => 'Owner'][$roleVal] ?? ucfirst($roleVal);
                @endphp
                <span>Peran: {{ $roleName }}</span>
            </div>
        </div>

        {{-- Kartu Info Profil --}}
        <div class="rounded-xl border border-line bg-white p-6 shadow-card">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Kartu Perbarui Password --}}
        <div class="rounded-xl border border-line bg-white p-6 shadow-card">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Proteksi Database: Hanya Admin yang dapat menghapus akun --}}
        @if ($roleVal === 'admin')
        <div class="rounded-xl border border-line bg-white p-6 shadow-card">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
        @else
        <div class="rounded-xl border border-line/80 bg-slate-50 p-4 text-xs text-muted flex items-start gap-3">
            <x-icon name="info" class="h-5 w-5 text-slate-400 shrink-0 mt-0.5" />
            <div>
                <span class="font-bold text-slate-700">Keamanan Integritas Database:</span>
                Penonaktifan atau penghapusan akun staf operasional hanya dapat dilakukan oleh Administrator melalui menu 
                @if (Route::has('admin.users.index'))
                    <a href="{{ route('admin.users.index') }}" class="text-primary font-semibold hover:underline">Kelola Staf</a>
                @else
                    <span class="font-semibold text-slate-700">Kelola Staf</span>
                @endif
                guna menjaga keutuhan riwayat transaksi pesanan, mutasi kartu stok, dan jadwal masak katering.
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
