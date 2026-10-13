<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pendaftaran Akun Pengguna Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Form registrasi akun internal oleh Admin sesuai use case UC-21.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8 border border-gray-100">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Budi Santoso" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <x-input-label for="email" :value="__('Alamat Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="nama@foodflow.test" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Peran (Role) -->
                    <div>
                        <x-input-label for="role" :value="__('Peran (Role)')" />
                        <select id="role" name="role" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">-- Pilih Peran Pengguna --</option>
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->value }}" {{ old('role') === $roleOption->value ? 'selected' : '' }}>
                                    {{ $roleOption->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Peran menentukan hak akses dashboard dan menu operasional.</p>
                    </div>

                    <!-- Status Akun -->
                    <div>
                        <x-input-label for="status" :value="__('Status Awal Akun')" />
                        <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}" {{ old('status', 'aktif') === $statusOption->value ? 'selected' : '' }}>
                                    {{ $statusOption->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Kata Sandi')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Daftarkan Akun (Simpan)') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
