<section class="space-y-4">
    <header>
        <h2 class="text-base font-bold text-rose-700">
            Hapus Akun Administrator
        </h2>

        <p class="mt-1 text-xs text-muted">
            Tindakan ini akan menghapus akun administrator Anda secara permanen. Mohon berhati-hati sebelum melanjutkan.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hapus Akun Ini</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-base font-bold text-ink">
                Apakah Anda yakin ingin menghapus akun ini?
            </h2>

            <p class="mt-1 text-xs text-muted">
                Setelah akun dihapus, sesi Anda akan segera berakhir dan akun tidak dapat diakses kembali. Masukkan kata sandi akun Anda untuk konfirmasi.
            </p>

            <div class="mt-4">
                <label for="password" class="sr-only">Kata Sandi</label>

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full sm:w-3/4"
                    placeholder="Masukkan kata sandi untuk konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button>
                    Ya, Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
