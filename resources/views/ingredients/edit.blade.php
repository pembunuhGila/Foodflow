<x-app-layout>
    @php
    $deleteConfirm = [
    'formId' => 'delete-ingredient-form',
    'tone' => 'danger',
    'eyebrow' => 'Hapus bahan baku',
    'title' => 'Hapus bahan baku ini?',
    'subjectLabel' => 'Bahan Baku',
    'subject' => $ingredient->name,
    'message' => 'Apakah Anda yakin ingin menghapus bahan baku ini?',
    'detail' => '',
    'confirmLabel' => 'Ya, Hapus Bahan Baku',
    ];
    @endphp

    {{-- Header: breadcrumb + judul, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('ingredients.index') }}" class="text-muted transition hover:text-primary">Daftar Bahan Baku</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Edit Bahan Baku</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Edit Bahan Baku: {{ $ingredient->name }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('ingredients.show', $ingredient) }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Detail
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

        <form method="POST" action="{{ route('ingredients.update', $ingredient) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Nama -->
                <div class="md:col-span-2">
                    <x-input-label for="name">Nama Bahan Baku <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input type="text" name="name" id="name" :value="old('name', $ingredient->name)" required class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Kategori -->
                <div>
                    <x-input-label for="category" value="Kategori Bahan" />
                    <x-text-input list="category-suggestions" type="text" name="category" id="category" :value="old('category', $ingredient->category)" placeholder="Pilih atau ketik kategori..." class="mt-1.5 block w-full" />
                    <datalist id="category-suggestions">
                        @foreach ($commonCategories as $cat)
                        <option value="{{ $cat }}">
                            @endforeach
                    </datalist>
                    <x-input-error :messages="$errors->get('category')" class="mt-1" />
                </div>

                <!-- Satuan -->
                <div>
                    <x-input-label for="unit">Satuan Unit (Ukuran/Kemasan) <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input list="unit-suggestions" type="text" name="unit" id="unit" :value="old('unit', $ingredient->unit)" required class="mt-1.5 block w-full" />
                    <datalist id="unit-suggestions">
                        @foreach ($commonUnits as $u)
                        <option value="{{ $u }}">
                            @endforeach
                    </datalist>
                    <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                </div>

                <!-- Stok minimum -->
                <div>
                    <x-input-label for="minimum_stock">Batas Stok Minimum <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input type="number" step="0.01" min="0" name="minimum_stock" id="minimum_stock" :value="old('minimum_stock', $ingredient->minimum_stock)" required class="mt-1.5 block w-full" />
                    <p class="mt-1 text-small text-muted">Batas peringatan restock persediaan.</p>
                    <x-input-error :messages="$errors->get('minimum_stock')" class="mt-1" />
                </div>

                <!-- Stok saat ini -->
                <div>
                    <x-input-label for="current_stock" value="Saldo Stok Saat Ini" />
                    <x-text-input type="number" step="0.01" min="0" name="current_stock" id="current_stock" :value="old('current_stock', $ingredient->current_stock)" class="mt-1.5 block w-full" />
                    <p class="mt-1 text-small text-muted">Jumlah stok fisik riil yang ada saat ini di gudang.</p>
                    <x-input-error :messages="$errors->get('current_stock')" class="mt-1" />
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <x-input-label for="notes" value="Catatan / Deskripsi Tambahan" />
                    <x-textarea name="notes" id="notes" rows="3" class="mt-1.5 block w-full">{{ old('notes', $ingredient->notes) }}</x-textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center pt-2 md:col-span-2">
                    <label class="inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20" {{ old('is_active', $ingredient->is_active) ? 'checked' : '' }}>
                        <span class="ms-2 text-body font-medium text-ink">Bahan Baku Aktif Digunakan dalam Operasional</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-between border-t border-line pt-6">
                <button type="button" @click="$dispatch('open-confirm', @js($deleteConfirm))" class="text-body font-medium text-danger-text transition hover:underline">
                    Hapus Bahan Baku
                </button>

                <div class="flex items-center gap-3">
                    <x-button variant="outline" :href="route('ingredients.show', $ingredient)">Batal</x-button>
                    <x-button type="submit">Simpan Perubahan</x-button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-ingredient-form" method="POST" action="{{ route('ingredients.destroy', $ingredient) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </x-card>
</x-app-layout>