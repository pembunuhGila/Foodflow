<x-app-layout>
    {{-- Header: breadcrumb + judul, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('suppliers.index') }}" class="text-muted transition hover:text-primary">Daftar Supplier</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Tambah Supplier</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Tambah Supplier Baru</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <x-card class="w-full sm:p-8">

        <!-- Alert Validasi -->
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

        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Nama Supplier -->
                <div class="md:col-span-2">
                    <x-input-label for="name">Nama Supplier / PT / CV / Toko <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input type="text" name="name" id="name" :value="old('name')" required placeholder="Contoh: PT Unggas Sejahtera, CV Beras Tani Makmur" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Telepon -->
                <div>
                    <x-input-label for="phone" value="Nomor Telepon / WhatsApp" />
                    <x-text-input type="text" name="phone" id="phone" :value="old('phone')" placeholder="Contoh: 081234567890" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Alamat Email" />
                    <x-text-input type="email" name="email" id="email" :value="old('email')" placeholder="Contoh: kontak@supplier.id" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <x-input-label for="address" value="Alamat Lengkap Kantor / Gudang Supplier" />
                    <x-textarea name="address" id="address" rows="3" placeholder="Ketik alamat lengkap jalan, nomor, kecamatan, kota..." class="mt-1.5 block w-full">{{ old('address') }}</x-textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <x-input-label for="notes" value="Catatan Pemasokan / Spesialisasi Bahan" />
                    <x-textarea name="notes" id="notes" rows="3" placeholder="Keterangan spesialisasi pasokan bahan, syarat pembayaran, jadwal pengiriman..." class="mt-1.5 block w-full">{{ old('notes') }}</x-textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center pt-2 md:col-span-2">
                    <label class="inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="ms-2 text-body font-medium text-ink">Supplier Aktif Bekerjasama dalam Pengadaan</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <x-button variant="outline" :href="route('suppliers.index')">Batal</x-button>
                <x-button type="submit">Simpan Supplier</x-button>
            </div>
        </form>
    </x-card>
</x-app-layout>