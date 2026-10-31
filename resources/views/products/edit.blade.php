<x-app-layout>
    @php
    $isProduksi = auth()->user()->role?->value === 'produksi';
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

    $nutrients = [
    ['name' => 'calories', 'model' => 'calories', 'label' => 'Energi Kalori', 'hint' => 'Total energi per porsi', 'unit' => 'kkal', 'required' => true],
    ['name' => 'protein', 'model' => 'protein', 'label' => 'Protein', 'hint' => 'Kandungan protein', 'unit' => 'g', 'required' => true],
    ['name' => 'fat', 'model' => 'fat', 'label' => 'Lemak Total', 'hint' => 'Lemak sehat & jenuh', 'unit' => 'g', 'required' => true],
    ['name' => 'carbohydrates', 'model' => 'carbs', 'label' => 'Karbohidrat', 'hint' => 'Karbohidrat gizi', 'unit' => 'g', 'required' => true],
    ['name' => 'dietary_fiber', 'model' => 'fiber', 'label' => 'Serat Pangan', 'hint' => 'Serat sayur & buah', 'unit' => 'g', 'required' => false],
    ];
    @endphp

    {{-- Header: breadcrumb + judul, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="text-muted transition hover:text-primary">Daftar Menu</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Edit Menu</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Edit Menu: {{ $product->name }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="space-y-6" x-data="productForm({
        sellingPrice: {{ old('selling_price', $product->selling_price ?? 0) }},
        calories: {{ old('calories', $product->calories ?? 0) }},
        protein: {{ old('protein', $product->protein ?? 0) }},
        fat: {{ old('fat', $product->fat ?? 0) }},
        carbs: {{ old('carbohydrates', $product->carbohydrates ?? 0) }},
        fiber: {{ old('dietary_fiber', $product->dietary_fiber ?? 0) }}
    })">

        <!-- Alert Validasi -->
        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan pada formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bagian 1: Informasi Menu -->
            <x-card class="w-full sm:p-8">
                <div class="border-b border-line pb-4">
                    <h2 class="text-lg font-semibold text-ink">Informasi Menu Masakan</h2>
                    <p class="mt-0.5 text-small text-muted">Perbarui identitas hidangan, harga satuan porsi, dan deskripsi menu.</p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nama -->
                    <div class="md:col-span-2">
                        <x-input-label for="name">Nama Menu Masakan <span class="text-danger-text">*</span></x-input-label>
                        <x-text-input type="text" name="name" id="name" :value="old('name', $product->name)" required class="mt-1.5 block w-full" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Satuan -->
                    <div>
                        <x-input-label for="unit">Satuan Porsi <span class="text-danger-text">*</span></x-input-label>
                        <x-text-input type="text" name="unit" id="unit" :value="old('unit', $product->unit ?? 'porsi')" required placeholder="Contoh: porsi, box, paket" class="mt-1.5 block w-full" />
                        <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                    </div>

                    <!-- Harga -->
                    <div>
                        <x-input-label for="selling_price">Harga Jual per Porsi <span class="text-danger-text">*</span></x-input-label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-small font-medium text-muted">Rp</span>
                            <input type="number" step="100" min="0" name="selling_price" id="selling_price"
                                x-model="sellingPrice"
                                @focus="handleNumberFocus('sellingPrice')"
                                @input="handleNumberInput('sellingPrice')"
                                @blur="handleNumberBlur('sellingPrice')"
                                class="{{ $inputClass }} pl-10" required>
                        </div>
                        <x-input-error :messages="$errors->get('selling_price')" class="mt-1" />
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Deskripsi Menu & Saran Penyajian" />
                        <x-textarea name="description" id="description" rows="3" placeholder="Tuliskan catatan rasa, bahan utama, atau saran penyajian..." class="mt-1.5 block w-full">{{ old('description', $product->description) }}</x-textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <!-- Status Aktif -->
                    <div class="md:col-span-2">
                        <label class="inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <span class="ms-2 text-body font-medium text-ink">Menu Aktif Ditawarkan</span>
                        </label>
                        <p class="mt-1 text-small text-muted">Menu yang aktif dapat dipilih saat pembuatan pesanan katering dan jadwal masak.</p>
                    </div>
                </div>
            </x-card>

            <!-- Bagian 2: Kandungan Gizi -->
            <x-card class="w-full sm:p-8">
                <div class="flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Kandungan Gizi per Porsi</h2>
                        <p class="mt-0.5 text-small text-muted">Dikelola oleh Staff Produksi / Ahli Gizi untuk 1 satuan takaran porsi hidangan.</p>
                    </div>
                    <div class="self-start rounded-full bg-primary/10 px-3 py-1 text-small font-medium text-primary sm:self-center">
                        Total Makronutrisi: <span x-text="totalMacros()">0</span> g
                    </div>
                </div>

                @if (!$isProduksi)
                <div class="mt-6 rounded-input border border-line bg-primary/5 p-4 text-small text-muted">
                    <span class="font-semibold text-ink">Perhatian hak akses:</span>
                    Nilai gizi menu ini dikelola eksklusif oleh Staff Produksi / Ahli Gizi. Anda (sebagai Admin) dapat melihat data gizi di bawah, namun perubahannya dilindungi agar standar nutrisi dapur tetap terjaga.
                </div>
                @endif

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach ($nutrients as $n)
                    <div class="rounded-input border border-line bg-primary/5 p-4">
                        <label for="{{ $n['name'] }}" class="block text-label uppercase text-muted">
                            {{ $n['label'] }} @if ($isProduksi && $n['required'])<span class="text-danger-text">*</span>@endif
                        </label>
                        <p class="mt-0.5 text-small text-muted">{{ $n['hint'] }}</p>
                        <div class="mt-3 flex items-center gap-2">
                            <input type="number" step="0.1" min="0" name="{{ $n['name'] }}" id="{{ $n['name'] }}"
                                x-model="{{ $n['model'] }}"
                                @if (!$isProduksi) readonly @else
                                @focus="handleNumberFocus('{{ $n['model'] }}')"
                                @input="handleNumberInput('{{ $n['model'] }}')"
                                @blur="handleNumberBlur('{{ $n['model'] }}')"
                                @endif
                                class="{{ $inputClass }} text-center font-medium {{ !$isProduksi ? 'cursor-not-allowed bg-gray-100' : '' }}"
                                @if ($isProduksi && $n['required']) required @endif>
                            <span class="shrink-0 text-small font-medium text-muted">{{ $n['unit'] }}</span>
                        </div>
                        <x-input-error :messages="$errors->get($n['name'])" class="mt-1" />
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" :href="route('products.index')">Batal</x-button>
                <x-button type="submit">Perbarui Menu</x-button>
            </div>
        </form>
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