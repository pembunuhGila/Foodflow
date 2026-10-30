<x-app-layout>
    <x-page-header eyebrow="Pengguna" :title="__('Ubah Data Pengguna & Hak Akses')"
        subtitle="Kelola data akun, perbarui peran, atau atur status akun.">
        <x-slot name="actions">
            <x-button variant="outline" :href="route('admin.users.index')">&larr; Kembali ke Daftar</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="mx-auto max-w-3xl sm:p-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="mt-1.5 block w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Alamat Email -->
            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Peran (Role) -->
            <div>
                <x-input-label for="role" :value="__('Peran (Role)')" />
                <x-select id="role" name="role" required class="mt-1.5 block">
                    @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption->value }}" {{ old('role', $user->role?->value) === $roleOption->value ? 'selected' : '' }}>
                        {{ $roleOption->label() }}
                    </option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Status Akun -->
            <div>
                <x-input-label for="status" :value="__('Status Akun')" />
                <x-select id="status" name="status" required class="mt-1.5 block">
                    @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}" {{ old('status', $user->status?->value) === $statusOption->value ? 'selected' : '' }}>
                        {{ $statusOption->label() }}
                    </option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                <p class="mt-1.5 text-small text-muted">Jika status disetel ke <em>Tidak Aktif</em>, pengguna tidak akan dapat login ke sistem.</p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 border-t border-line pt-5">
                <x-button variant="outline" :href="route('admin.users.index')">Batal</x-button>
                <x-primary-button>
                    {{ __('Simpan Perubahan') }}
                </x-primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>