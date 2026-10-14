<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Bahan Baku') }}: {{ $ingredient->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui data bahan baku, satuan ukuran, dan batas minimum stok (UC-04).</p>
            </div>
            <a href="{{ route('ingredients.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-8">

                <!-- Alert Validasi -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                        <p class="font-semibold mb-2">Terdapat kesalahan pada formulir:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('ingredients.update', $ingredient) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Bahan -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Bahan Baku <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $ingredient->name) }}" required class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Kategori Bahan</label>
                            <input list="category-suggestions" type="text" name="category" id="category" value="{{ old('category', $ingredient->category) }}" placeholder="Pilih atau ketik kategori..." class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <datalist id="category-suggestions">
                                @foreach ($commonCategories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            <x-input-error :messages="$errors->get('category')" class="mt-1" />
                        </div>

                        <!-- Satuan Unit -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700">Satuan Unit (Ukuran/Kemasan) <span class="text-red-500">*</span></label>
                            <input list="unit-suggestions" type="text" name="unit" id="unit" value="{{ old('unit', $ingredient->unit) }}" required class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <datalist id="unit-suggestions">
                                @foreach ($commonUnits as $u)
                                    <option value="{{ $u }}">
                                @endforeach
                            </datalist>
                            <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                        </div>

                        <!-- Stok Minimum -->
                        <div class="md:col-span-2">
                            <label for="minimum_stock" class="block text-sm font-medium text-gray-700">Batas Stok Minimum <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $ingredient->minimum_stock) }}" required class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <p class="text-xs text-gray-500 mt-1">Sistem menampilkan peringatan saat persediaan berada di bawah batas ini (BR-06).</p>
                            <x-input-error :messages="$errors->get('minimum_stock')" class="mt-1" />
                        </div>

                        <!-- Catatan -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan / Deskripsi Tambahan</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes', $ingredient->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                        </div>

                        <!-- Status Aktif -->
                        <div class="md:col-span-2 flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" {{ old('is_active', $ingredient->is_active) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-700 font-medium">Bahan Baku Aktif Digunakan dalam Operasional</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <!-- Tombol Hapus Aman -->
                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')) document.getElementById('delete-ingredient-form').submit();" class="text-sm font-medium text-red-600 hover:text-red-900 transition">
                            Hapus Bahan Baku
                        </button>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('ingredients.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md shadow-sm transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>

                </form>

                <!-- Hidden Delete Form -->
                <form id="delete-ingredient-form" method="POST" action="{{ route('ingredients.destroy', $ingredient) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
