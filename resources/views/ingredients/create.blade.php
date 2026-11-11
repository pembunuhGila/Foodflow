<x-app-layout>
    {{-- Header: breadcrumb + judul, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('ingredients.index') }}" class="text-muted transition hover:text-primary">Daftar Bahan Baku</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Tambah Bahan Baku</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Tambah Bahan Baku Baru</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('ingredients.index') }}"
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

        <form method="POST" action="{{ route('ingredients.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Nama -->
                <div class="md:col-span-2">
                    <x-input-label for="name">Nama Bahan Baku <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input type="text" name="name" id="name" :value="old('name')" required placeholder="Contoh: Dada Ayam Fillet Segar, Beras Merah, Telur Ayam" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Kategori -->
                <div>
                    <x-input-label for="category" value="Kategori Bahan" />
                    <x-text-input list="category-suggestions" type="text" name="category" id="category" :value="old('category')" placeholder="Pilih atau ketik kategori..." class="mt-1.5 block w-full" />
                    <datalist id="category-suggestions">
                        @foreach ($commonCategories as $cat)
                        <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    <x-input-error :messages="$errors->get('category')" class="mt-1" />
                </div>

                <!-- Satuan Berjenjang (Kelompok -> Satuan Detail) -->
                <div x-data="{
                    group: 'berat',
                    unit: '{{ old('unit', 'kg') }}',
                    unitsByGroup: {
                        'berat': [
                            { value: 'kg', label: 'kg (Kilogram)' },
                            { value: 'gram', label: 'gram (Gram)' },
                            { value: 'ons', label: 'ons (100 gram)' }
                        ],
                        'volume': [
                            { value: 'liter', label: 'liter (Liter)' },
                            { value: 'ml', label: 'ml (Mililiter)' }
                        ],
                        'satuan': [
                            { value: 'pcs', label: 'pcs (Pieces / Biji)' },
                            { value: 'butir', label: 'butir (Butir Telur/Buah)' },
                            { value: 'lembar', label: 'lembar (Lembar Daun/Kulit)' }
                        ],
                        'kemasan': [
                            { value: 'bungkus', label: 'bungkus (Bungkus / Pack)' },
                            { value: 'ikat', label: 'ikat (Ikat Sayur)' },
                            { value: 'kaleng', label: 'kaleng (Kaleng)' },
                            { value: 'botol', label: 'botol (Botol)' },
                            { value: 'karung', label: 'karung (Sak / Karung)' }
                        ]
                    },
                    init() {
                        for (const [grp, list] of Object.entries(this.unitsByGroup)) {
                            if (list.some(u => u.value === this.unit)) {
                                this.group = grp;
                                break;
                            }
                        }
                    },
                    onGroupChange() {
                        if (this.unitsByGroup[this.group] && this.unitsByGroup[this.group].length > 0) {
                            this.unit = this.unitsByGroup[this.group][0].value;
                        }
                    }
                }" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dropdown 1: Tipe / Kelompok Satuan -->
                    <div>
                        <x-input-label for="unit_group">Kelompok Takaran / Satuan <span class="text-danger-text">*</span></x-input-label>
                        <select id="unit_group" x-model="group" @change="onGroupChange()"
                            class="mt-1.5 block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="berat">Berat / Timbangan (kg, gram, ons)</option>
                            <option value="volume">Volume / Cairan (liter, ml)</option>
                            <option value="satuan">Satuan Hitung (pcs, butir, lembar)</option>
                            <option value="kemasan">Kemasan & Wadah (bungkus, ikat, kaleng, botol, karung)</option>
                        </select>
                        <p class="mt-1 text-small text-muted">Pilih kategori takaran (misal: Berat atau Volume).</p>
                    </div>

                    <!-- Dropdown 2: Satuan Detail yang Disimpan -->
                    <div>
                        <x-input-label for="unit">Satuan Ukuran <span class="text-danger-text">*</span></x-input-label>
                        <select name="unit" id="unit" x-model="unit" required
                            class="mt-1.5 block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            <template x-for="item in unitsByGroup[group]" :key="item.value">
                                <option :value="item.value" x-text="item.label" :selected="item.value === unit"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-small text-muted">Satuan ini yang digunakan pada resep menu & kartu stok.</p>
                        <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                    </div>
                </div>

                <!-- Stok minimum -->
                <div>
                    <x-input-label for="minimum_stock">Batas Stok Minimum <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input type="number" step="0.01" min="0" name="minimum_stock" id="minimum_stock" :value="old('minimum_stock', '10.00')" required class="mt-1.5 block w-full" />
                    <p class="mt-1 text-small text-muted">Batas peringatan restock persediaan.</p>
                    <x-input-error :messages="$errors->get('minimum_stock')" class="mt-1" />
                </div>

                <!-- Stok awal -->
                <div>
                    <x-input-label for="current_stock" value="Saldo Stok Awal Saat Ini" />
                    <x-text-input type="number" step="0.01" min="0" name="current_stock" id="current_stock" :value="old('current_stock', '0.00')" class="mt-1.5 block w-full" />
                    <p class="mt-1 text-small text-muted">Jumlah stok fisik riil yang ada saat ini di gudang.</p>
                    <x-input-error :messages="$errors->get('current_stock')" class="mt-1" />
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <x-input-label for="notes" value="Catatan / Deskripsi Tambahan" />
                    <x-textarea name="notes" id="notes" rows="3" placeholder="Keterangan spesifikasi bahan baku, standar mutu, atau penanganan penyimpanan..." class="mt-1.5 block w-full">{{ old('notes') }}</x-textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center pt-2 md:col-span-2">
                    <label class="inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="ms-2 text-body font-medium text-ink">Bahan Baku Aktif Digunakan dalam Operasional</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                <x-button variant="outline" :href="route('ingredients.index')">Batal</x-button>
                <x-button type="submit">Simpan Bahan Baku</x-button>
            </div>
        </form>
    </x-card>
</x-app-layout>