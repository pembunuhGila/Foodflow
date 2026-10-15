<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Tambah Menu Katering Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daftarkan hidangan katering sehat baru beserta harga jual dan rincian kandungan nilai gizi per porsi.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-md transition shadow-sm">
                Kembali ke Daftar Menu
            </a>
        </div>
    </x-slot>

    @php
        $isProduksi = auth()->user()->role?->value === 'produksi';
    @endphp

    <div class="py-12" x-data="{
        calories: {{ old('calories', 0) }},
        protein: {{ old('protein', 0) }},
        fat: {{ old('fat', 0) }},
        carbs: {{ old('carbohydrates', 0) }},
        fiber: {{ old('dietary_fiber', 0) }},
        totalMacros() {
            return (parseFloat(this.protein || 0) + parseFloat(this.fat || 0) + parseFloat(this.carbs || 0)).toFixed(1);
        }
    }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm shadow-sm">
                    <div class="font-semibold mb-1">Terdapat kesalahan pengisian formulir:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Informasi Menu Dasar -->
                <div class="bg-white p-6 sm:p-8 rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>🍽️</span> 1. Informasi Menu Masakan Katering
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Identitas menu makanan sehat yang akan ditawarkan kepada pelanggan katering.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Menu -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama Menu Masakan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Nasi Merah Dada Ayam Panggang Rosemary" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Satuan Takaran -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700">
                                Satuan Porsi <span class="text-red-500">*</span>
                            </label>
                            <input list="unit-list" name="unit" id="unit" value="{{ old('unit', 'porsi') }}" placeholder="porsi, box, pax..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                            <datalist id="unit-list">
                                @foreach ($commonUnits as $unit)
                                    <option value="{{ $unit }}"></option>
                                @endforeach
                            </datalist>
                            @error('unit')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harga Jual -->
                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700">
                                Harga Jual per Porsi (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" step="100" min="0" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" class="block w-full rounded-md border-gray-300 pl-10 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                            </div>
                            @error('selling_price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Ketersediaan -->
                        <div class="md:col-span-2 pt-2">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none ring-offset-2 ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700">Menu Aktif Ditawarkan</span>
                            </label>
                        </div>

                        <!-- Deskripsi Menu -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Deskripsi Menu & Saran Penyajian
                            </label>
                            <textarea name="description" id="description" rows="3" placeholder="Jelaskan karakteristik menu, varian rasa, atau preferensi pantangan (misal: bebas gula rafinasi, tinggi serat)..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Informasi Kandungan Gizi Menu -->
                <div class="bg-white p-6 sm:p-8 rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <span>🥗</span> 2. Informasi & Rekap Kandungan Gizi (Nutrition Facts)
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Dikelola oleh Staff Produksi / Ahli Gizi untuk 1 satuan takaran porsi hidangan (FR-11 & FR-12).</p>
                        </div>
                        <div class="bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-800">
                            Total Makronutrisi: <span x-text="totalMacros()">0</span> gram
                        </div>
                    </div>

                    @if (!$isProduksi)
                        <div class="p-4 mb-6 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-xs text-amber-800 leading-relaxed">
                                <span class="font-semibold">Perhatian Akses Khusus:</span> Rincian nilai gizi menu dikelola secara eksklusif oleh <strong>Staff Produksi / Ahli Gizi</strong>. Sebagai Admin, Anda dapat melihat informasi gizi, namun pengisian dan penyesuaian gizi akan dilengkapi oleh tim produksi.
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Kalori -->
                        <div class="bg-amber-50/60 p-4 rounded-lg border border-amber-200">
                            <label for="calories" class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                                🔥 Energi Kalori @if($isProduksi) <span class="text-red-500">*</span> @endif
                            </label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" min="0" name="calories" id="calories" x-model="calories" @if(!$isProduksi) readonly @endif class="block w-full rounded-md border-amber-300 pr-12 focus:border-amber-500 focus:ring-amber-500 font-semibold text-sm @if(!$isProduksi) bg-amber-100/50 cursor-not-allowed @endif" @if($isProduksi) required @endif>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-xs text-amber-700 font-medium">kkal</span>
                                </div>
                            </div>
                            @error('calories')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-amber-700 mt-1">Total energi per porsi</p>
                        </div>

                        <!-- Protein -->
                        <div class="bg-blue-50/60 p-4 rounded-lg border border-blue-200">
                            <label for="protein" class="block text-xs font-bold text-blue-900 uppercase tracking-wider">
                                🍗 Protein @if($isProduksi) <span class="text-red-500">*</span> @endif
                            </label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" min="0" name="protein" id="protein" x-model="protein" @if(!$isProduksi) readonly @endif class="block w-full rounded-md border-blue-300 pr-10 focus:border-blue-500 focus:ring-blue-500 font-semibold text-sm @if(!$isProduksi) bg-blue-100/50 cursor-not-allowed @endif" @if($isProduksi) required @endif>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-xs text-blue-700 font-medium">g</span>
                                </div>
                            </div>
                            @error('protein')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-blue-700 mt-1">Protein murni</p>
                        </div>

                        <!-- Lemak -->
                        <div class="bg-rose-50/60 p-4 rounded-lg border border-rose-200">
                            <label for="fat" class="block text-xs font-bold text-rose-900 uppercase tracking-wider">
                                🥑 Lemak Total @if($isProduksi) <span class="text-red-500">*</span> @endif
                            </label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" min="0" name="fat" id="fat" x-model="fat" @if(!$isProduksi) readonly @endif class="block w-full rounded-md border-rose-300 pr-10 focus:border-rose-500 focus:ring-rose-500 font-semibold text-sm @if(!$isProduksi) bg-rose-100/50 cursor-not-allowed @endif" @if($isProduksi) required @endif>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-xs text-rose-700 font-medium">g</span>
                                </div>
                            </div>
                            @error('fat')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-rose-700 mt-1">Lemak sehat & jenuh</p>
                        </div>

                        <!-- Karbohidrat -->
                        <div class="bg-amber-50/60 p-4 rounded-lg border border-amber-200">
                            <label for="carbohydrates" class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                                🍚 Karbohidrat @if($isProduksi) <span class="text-red-500">*</span> @endif
                            </label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" min="0" name="carbohydrates" id="carbohydrates" x-model="carbs" @if(!$isProduksi) readonly @endif class="block w-full rounded-md border-amber-300 pr-10 focus:border-amber-500 focus:ring-amber-500 font-semibold text-sm @if(!$isProduksi) bg-amber-100/50 cursor-not-allowed @endif" @if($isProduksi) required @endif>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-xs text-amber-700 font-medium">g</span>
                                </div>
                            </div>
                            @error('carbohydrates')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-amber-700 mt-1">Karbohidrat kompleks</p>
                        </div>

                        <!-- Serat Pangan -->
                        <div class="bg-emerald-50/60 p-4 rounded-lg border border-emerald-200">
                            <label for="dietary_fiber" class="block text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                🥦 Serat Pangan
                            </label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" min="0" name="dietary_fiber" id="dietary_fiber" x-model="fiber" @if(!$isProduksi) readonly @endif class="block w-full rounded-md border-emerald-300 pr-10 focus:border-emerald-500 focus:ring-emerald-500 font-semibold text-sm @if(!$isProduksi) bg-emerald-100/50 cursor-not-allowed @endif">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-xs text-emerald-700 font-medium">g</span>
                                </div>
                            </div>
                            @error('dietary_fiber')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-emerald-700 mt-1">Serat sayur & buah</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm">
                        Simpan Menu Masakan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
