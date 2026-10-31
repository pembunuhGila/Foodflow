<x-app-layout>
    @php
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    $unitLabel = $product->unit ?? 'porsi';
    @endphp

    {{-- Header: breadcrumb + judul, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex flex-wrap items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="text-muted transition hover:text-primary">Daftar Menu</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('products.show', $product) }}" class="text-muted transition hover:text-primary">Detail Menu</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Peracik Resep</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">Peracik Komposisi Resep</h1>
            </div>
            <p class="mt-1 text-small text-muted">
                Tentukan takaran bahan baku mentah untuk 1 {{ $unitLabel }} menu <span class="font-medium text-ink">{{ $product->name }}</span>.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.show', $product) }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Detail
            </a>
        </div>
    </div>

    <div class="space-y-6" x-data="recipeBuilder({
        availableIngredients: @js($availableIngredients),
        initialItems: @js(old('items', $initialItems))
    })">

        <!-- Alert Validasi -->
        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan pada formulir resep:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Ringkasan Menu -->
        <x-card class="w-full">
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Menu Catering</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $product->name }}</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Energi</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ number_format((float) ($product->calories ?? 0), 0) }} kkal</dd>
                </div>
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Harga Satuan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $product->formattedPrice() }} / {{ $unitLabel }}</dd>
                </div>
            </dl>
        </x-card>

        <!-- Form Komposisi -->
        <form method="POST" action="{{ route('products.recipe.update', $product) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card class="w-full sm:p-8">
                <div class="flex flex-col gap-3 border-b border-line pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Komposisi Bahan Baku Mentah</h2>
                        <p class="mt-0.5 text-small text-muted">Tambahkan bahan baku beserta takaran presisi untuk menghasilkan satu porsi hidangan.</p>
                    </div>
                    <div class="self-start rounded-full bg-primary/10 px-3 py-1 text-small font-medium text-primary sm:self-center">
                        Total: <span x-text="items.length"></span> jenis bahan
                    </div>
                </div>

                <!-- Kosong -->
                <template x-if="items.length === 0">
                    <div class="mt-6 rounded-input border border-dashed border-line px-6 py-12 text-center">
                        <p class="font-medium text-ink">Belum ada bahan baku dalam resep ini</p>
                        <p class="mt-1 text-small text-muted">Klik tombol "Tambah Bahan Baku" di bawah untuk mulai menyusun resep.</p>
                    </div>
                </template>

                <!-- Tabel bahan -->
                <template x-if="items.length > 0">
                    <div class="mt-6 overflow-x-auto rounded-input border border-line">
                        <table class="min-w-full text-body">
                            <thead class="bg-primary/5">
                                <tr>
                                    <th scope="col" class="w-12 px-4 py-3.5 text-center text-label uppercase text-muted">#</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-label uppercase text-muted">Bahan Baku Mentah</th>
                                    <th scope="col" class="w-48 px-4 py-3.5 text-left text-label uppercase text-muted">Takaran / 1 {{ $unitLabel }}</th>
                                    <th scope="col" class="w-40 px-4 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                                    <th scope="col" class="w-24 px-4 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="transition hover:bg-primary/5">
                                        <td class="px-4 py-3 text-center text-small text-muted" x-text="index + 1"></td>

                                        <td class="px-4 py-3">
                                            <select :name="`items[${index}][ingredient_id]`" x-model="item.ingredient_id" @change="onIngredientChange(index)" class="{{ $inputClass }}" required>
                                                <option value="">-- Pilih Bahan Baku Mentah --</option>
                                                <template x-for="ing in availableIngredients" :key="ing.id">
                                                    <option :value="ing.id" :selected="ing.id == item.ingredient_id" x-text="`${ing.name} (Satuan gudang: ${ing.unit})`"></option>
                                                </template>
                                            </select>
                                        </td>

                                        <td class="px-4 py-3">
                                            <input type="number" step="0.001" min="0.001" :name="`items[${index}][quantity_per_unit]`" x-model="item.quantity_per_unit" placeholder="Misal: 150" class="{{ $inputClass }}" required>
                                        </td>

                                        <td class="px-4 py-3">
                                            <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" placeholder="gram, kg, ml..." class="{{ $inputClass }}" required>
                                        </td>

                                        <td class="px-4 py-3 text-right text-small font-medium">
                                            <button type="button" @click="removeItem(index)" class="text-danger-text transition hover:underline">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <!-- Tambah baris -->
                <div class="mt-6 flex flex-col gap-3 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-2 self-start rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-primary transition hover:bg-primary/5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Bahan Baku
                    </button>
                    <span class="text-small text-muted">Pastikan takaran bahan baku akurat untuk kebutuhan kalkulator bahan produksi.</span>
                </div>
            </x-card>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" :href="route('products.show', $product)">Batal</x-button>
                <x-button type="submit">Simpan Komposisi Resep</x-button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Component Script (tidak diubah) -->
    <script>
        function recipeBuilder(config) {
            return {
                availableIngredients: config.availableIngredients || [],
                items: (config.initialItems && config.initialItems.length > 0) ?
                    config.initialItems :
                    [],
                addItem() {
                    this.items.push({
                        ingredient_id: '',
                        quantity_per_unit: '',
                        unit: 'gram'
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                onIngredientChange(index) {
                    const selectedId = this.items[index].ingredient_id;
                    const found = this.availableIngredients.find(ing => ing.id == selectedId);
                    if (found && (!this.items[index].unit || this.items[index].unit === 'gram')) {
                        this.items[index].unit = found.unit || 'gram';
                    }
                }
            };
        }
    </script>
</x-app-layout>