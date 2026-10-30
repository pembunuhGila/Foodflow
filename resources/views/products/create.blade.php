<x-app-layout>
    <x-slot name="header">
        <div>
            {{-- Breadcrumb Interaktif --}}
            <nav class="flex items-center gap-2 text-xs font-medium text-gray-400 mb-1.5" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="hover:text-purple-600 transition-colors">Daftar Menu</a>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-purple-700 font-semibold">Tambah Menu Baru</span>
            </nav>

            {{-- Judul Halaman --}}
            <h2 class="font-bold text-xl sm:text-2xl text-gray-900 leading-tight">
                {{ __('Tambah Menu Katering Baru') }}
            </h2>
            <p class="text-xs text-gray-500 mt-1">
                Daftarkan hidangan katering sehat baru beserta harga satuan porsi dan rincian kandungan nilai gizi per porsi.
            </p>
        </div>
    </x-slot>

    @php
        $isProduksi = auth()->user()->role?->value === 'produksi';
    @endphp

    <div class="py-8" x-data="productForm({
        sellingPrice: {{ old('selling_price', 0) }},
        calories: {{ old('calories', 0) }},
        protein: {{ old('protein', 0) }},
        fat: {{ old('fat', 0) }},
        carbs: {{ old('carbohydrates', 0) }},
        fiber: {{ old('dietary_fiber', 0) }}
    })">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Error Alert -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-xl shadow-sm">
                    <div class="font-semibold text-sm mb-1">Terdapat kesalahan pengisian formulir:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Informasi Menu Dasar -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0 border border-purple-100/60 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">1. Informasi Menu Masakan Katering</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Identitas hidangan sehat yang akan ditawarkan kepada pelanggan katering.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nama Menu -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Nama Menu Masakan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Nasi Merah Dada Ayam Panggang Rosemary" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all" required>
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Satuan Takaran -->
                        <div>
                            <label for="unit" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Satuan Porsi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit', 'porsi') }}" placeholder="Contoh: porsi, box, paket" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-800 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all" required>
                            @error('unit')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harga Jual (Natural Zero Input Handler) -->
                        <div>
                            <label for="selling_price" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Harga Jual per Porsi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-2xs">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <span class="text-xs font-bold text-gray-400 bg-gray-100/70 px-1.5 py-0.5 rounded">Rp</span>
                                </div>
                                <input type="number" step="100" min="0" name="selling_price" id="selling_price"
                                       x-model="sellingPrice"
                                       @focus="handleNumberFocus('sellingPrice')"
                                       @input="handleNumberInput('sellingPrice')"
                                       @blur="handleNumberBlur('sellingPrice')"
                                       class="w-full pl-12 bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all" required>
                            </div>
                            @error('selling_price')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Ketersediaan (Custom Toggle Switch) -->
                        <div class="md:col-span-2 pt-1">
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 shadow-2xs"></div>
                                <span class="ml-3 text-sm font-semibold text-gray-700">Menu Aktif Ditawarkan</span>
                            </label>
                            <p class="text-xs text-gray-400 mt-1">Menu yang aktif dapat dipilih saat pembuatan pesanan katering dan jadwal masak.</p>
                        </div>

                        <!-- Deskripsi Menu -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Deskripsi Menu &amp; Saran Penyajian
                            </label>
                            <textarea name="description" id="description" rows="3" placeholder="Tuliskan catatan rasa, bahan utama, atau saran penyajian..." class="w-full bg-gray-50/50 border border-gray-200 rounded-xl p-3.5 text-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Informasi Kandungan Gizi Menu -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7">
                    <div class="border-b border-gray-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0 border border-purple-100/60 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">2. Informasi &amp; Rekap Kandungan Gizi (Nutrition Facts)</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Dikelola oleh Staff Produksi / Ahli Gizi untuk 1 satuan takaran porsi hidangan.</p>
                            </div>
                        </div>
                        <div class="bg-purple-50 text-purple-700 border border-purple-100 px-3 py-1 rounded-full text-xs font-semibold shrink-0 self-start sm:self-center">
                            Total Makronutrisi: <span x-text="totalMacros()">0</span> gram
                        </div>
                    </div>

                    @if (!$isProduksi)
                        <div class="p-4 mb-6 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-xs text-amber-800 leading-relaxed">
                                <span class="font-bold">Perhatian Hak Akses:</span> Informasi nilai gizi menu baru diisi secara resmi oleh <strong>Staff Produksi / Ahli Gizi</strong>. Anda dapat mendaftarkan identitas menu sekarang dan nilai gizi dapat dilengkapi kemudian oleh staf dapur.
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Kalori (Amber Soft - No Emoji) -->
                        <div class="bg-amber-50/40 border border-amber-200/60 rounded-xl p-4 flex flex-col justify-between">
                            <div>
                                <label for="calories" class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                                    ENERGI KALORI @if($isProduksi) <span class="text-rose-500">*</span> @endif
                                </label>
                                <p class="text-[11px] text-amber-700/80 mt-0.5">Total energi per porsi</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <input type="number" step="0.1" min="0" name="calories" id="calories"
                                       x-model="calories"
                                       @focus="handleNumberFocus('calories')"
                                       @input="handleNumberInput('calories')"
                                       @blur="handleNumberBlur('calories')"
                                       class="w-full font-bold text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 text-center" @if($isProduksi) required @endif>
                                <span class="text-xs font-semibold text-amber-800 shrink-0">kkal</span>
                            </div>
                            @error('calories')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Protein (Blue Soft - No Emoji) -->
                        <div class="bg-blue-50/40 border border-blue-200/60 rounded-xl p-4 flex flex-col justify-between">
                            <div>
                                <label for="protein" class="block text-xs font-bold text-blue-900 uppercase tracking-wider">
                                    PROTEIN @if($isProduksi) <span class="text-rose-500">*</span> @endif
                                </label>
                                <p class="text-[11px] text-blue-700/80 mt-0.5">Kandungan protein</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <input type="number" step="0.1" min="0" name="protein" id="protein"
                                       x-model="protein"
                                       @focus="handleNumberFocus('protein')"
                                       @input="handleNumberInput('protein')"
                                       @blur="handleNumberBlur('protein')"
                                       class="w-full font-bold text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 text-center" @if($isProduksi) required @endif>
                                <span class="text-xs font-semibold text-blue-800 shrink-0">g</span>
                            </div>
                            @error('protein')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lemak Total (Rose Soft - No Emoji) -->
                        <div class="bg-rose-50/40 border border-rose-200/60 rounded-xl p-4 flex flex-col justify-between">
                            <div>
                                <label for="fat" class="block text-xs font-bold text-rose-900 uppercase tracking-wider">
                                    LEMAK TOTAL @if($isProduksi) <span class="text-rose-500">*</span> @endif
                                </label>
                                <p class="text-[11px] text-rose-700/80 mt-0.5">Lemak sehat &amp; jenuh</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <input type="number" step="0.1" min="0" name="fat" id="fat"
                                       x-model="fat"
                                       @focus="handleNumberFocus('fat')"
                                       @input="handleNumberInput('fat')"
                                       @blur="handleNumberBlur('fat')"
                                       class="w-full font-bold text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 text-center" @if($isProduksi) required @endif>
                                <span class="text-xs font-semibold text-rose-800 shrink-0">g</span>
                            </div>
                            @error('fat')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Karbohidrat (Purple Soft - No Emoji) -->
                        <div class="bg-purple-50/40 border border-purple-200/60 rounded-xl p-4 flex flex-col justify-between">
                            <div>
                                <label for="carbohydrates" class="block text-xs font-bold text-purple-900 uppercase tracking-wider">
                                    KARBOHIDRAT @if($isProduksi) <span class="text-rose-500">*</span> @endif
                                </label>
                                <p class="text-[11px] text-purple-700/80 mt-0.5">Karbohidrat gizi</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <input type="number" step="0.1" min="0" name="carbohydrates" id="carbohydrates"
                                       x-model="carbs"
                                       @focus="handleNumberFocus('carbs')"
                                       @input="handleNumberInput('carbs')"
                                       @blur="handleNumberBlur('carbs')"
                                       class="w-full font-bold text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 text-center" @if($isProduksi) required @endif>
                                <span class="text-xs font-semibold text-purple-800 shrink-0">g</span>
                            </div>
                            @error('carbohydrates')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Serat Pangan (Emerald Soft - No Emoji) -->
                        <div class="bg-emerald-50/40 border border-emerald-200/60 rounded-xl p-4 flex flex-col justify-between">
                            <div>
                                <label for="dietary_fiber" class="block text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                    SERAT PANGAN
                                </label>
                                <p class="text-[11px] text-emerald-700/80 mt-0.5">Serat sayur &amp; buah</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <input type="number" step="0.1" min="0" name="dietary_fiber" id="dietary_fiber"
                                       x-model="fiber"
                                       @focus="handleNumberFocus('fiber')"
                                       @input="handleNumberInput('fiber')"
                                       @blur="handleNumberBlur('fiber')"
                                       class="w-full font-bold text-gray-800 bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 text-center">
                                <span class="text-xs font-semibold text-emerald-800 shrink-0">g</span>
                            </div>
                            @error('dietary_fiber')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons (Batal & Simpan) -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl px-6 py-2.5 transition-all uppercase tracking-wider">
                        BATAL
                    </a>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-semibold text-xs rounded-xl px-6 py-2.5 shadow-md shadow-purple-200 transition-all flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>SIMPAN MENU BARU</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Script Helper Form Angka Natural --}}
    <script>
        function productForm(initData) {
            return {
                sellingPrice: initData.sellingPrice ?? 0,
                calories: initData.calories ?? 0,
                protein: initData.protein ?? 0,
                fat: initData.fat ?? 0,
                carbs: initData.carbs ?? 0,
                fiber: initData.fiber ?? 0,

                totalMacros() {
                    return (parseFloat(this.protein || 0) + parseFloat(this.fat || 0) + parseFloat(this.carbs || 0)).toFixed(1);
                },

                handleNumberFocus(field) {
                    if (this[field] === 0 || this[field] === '0') {
                        this[field] = '';
                    }
                },

                handleNumberInput(field) {
                    if (this[field] !== null && this[field] !== undefined) {
                        let str = this[field].toString();
                        if (/^0+[1-9]/.test(str)) {
                            this[field] = str.replace(/^0+(?=\d)/, '');
                        }
                    }
                },

                handleNumberBlur(field) {
                    if (this[field] === '' || this[field] === null || this[field] === undefined) {
                        this[field] = 0;
                    }
                }
            };
        }
    </script>
</x-app-layout>
