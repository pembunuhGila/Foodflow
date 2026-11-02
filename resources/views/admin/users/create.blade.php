<x-app-layout>
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('admin.users.index') }}" class="text-muted transition hover:text-primary">Daftar Pengguna</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Tambah Pengguna</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Pendaftaran Akun Pengguna Baru</h1>
            </div>
            <p class="mt-1 text-small text-muted">Registrasi akun internal oleh Admin.</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <x-card class="w-full sm:p-8">

        @if ($errors->any())
        <div class="mb-6 rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan pada formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Nama -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" class="mt-1.5 block w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Budi Santoso" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Alamat Email')" />
                    <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required placeholder="nama@foodflow.test" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Peran -->
                <div>
                    <x-input-label for="role" :value="__('Peran (Role)')" />
                    <x-select id="role" name="role" required class="mt-1.5 block w-full">
                        <option value="">-- Pilih Peran Pengguna --</option>
                        @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption->value }}" {{ old('role') === $roleOption->value ? 'selected' : '' }}>
                            {{ $roleOption->label() }}
                        </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    <p class="mt-1 text-small text-muted">Peran menentukan hak akses dashboard dan menu operasional.</p>
                </div>

                <!-- Status -->
                <div>
                    <x-input-label for="status" :value="__('Status Awal Akun')" />
                    <x-select id="status" name="status" class="mt-1.5 block w-full">
                        @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}" {{ old('status', 'aktif') === $statusOption->value ? 'selected' : '' }}>
                            {{ $statusOption->label() }}
                        </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('status')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Kata Sandi')" />
                    <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Konfirmasi -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                    <x-text-input id="password_confirmation" class="mt-1.5 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <x-button variant="outline" :href="route('admin.users.index')">Batal</x-button>
                <x-button type="submit">Daftarkan Akun</x-button>
            </div>
        </form>
    </x-card>
</x-app-layout>