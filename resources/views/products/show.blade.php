<x-app-layout>
    @php
    $canManage = in_array(auth()->user()->role?->value, ['admin', 'produksi'], true);
    $unitLabel = $product->unit ?? 'porsi';
    @endphp

    {{-- Header: breadcrumb + nama + status, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="text-muted transition hover:text-primary">Daftar Menu</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Menu</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">{{ $product->name }}</h1>
                @if ($product->isActive())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-small font-medium text-gray-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                </span>
                @endif
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

            @if ($canManage)
            <a href="{{ route('products.edit', $product) }}"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Menu
            </a>
            @endif
        </div>
    </div>

    <div class="space-y-6">

        @if (session('success'))
        <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary" role="status">
            <x-icon name="check" class="h-5 w-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <!-- Identitas Hidangan -->
            <x-card class="w-full sm:p-8 lg:col-span-2">
                <p class="text-small text-muted">
                    Terdaftar di sistem sejak
                    <span class="font-medium text-ink">{{ $product->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                </p>

                <dl class="mt-6 grid grid-cols-1 gap-4 border-t border-line pt-6 md:grid-cols-2">
                    <div class="rounded-input border border-line bg-primary/5 p-4">
                        <dt class="text-label uppercase text-muted">Harga Jual Menu</dt>
                        <dd class="mt-1.5 text-body font-medium text-ink">{{ $product->formattedPrice() }} / {{ $unitLabel }}</dd>
                    </div>

                    <div class="rounded-input border border-line bg-primary/5 p-4">
                        <dt class="text-label uppercase text-muted">Satuan Penyajian</dt>
                        <dd class="mt-1.5 text-body font-medium capitalize text-ink">{{ $unitLabel }}</dd>
                    </div>

                    <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                        <dt class="text-label uppercase text-muted">Deskripsi Menu &amp; Saran Penyajian</dt>
                        <dd class="mt-1.5 text-body font-medium text-ink">
                            {{ $product->description ?: 'Tidak ada deskripsi atau catatan khusus untuk menu ini.' }}
                        </dd>
                    </div>

                    <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                        <dt class="text-label uppercase text-muted">Terakhir Diubah</dt>
                        <dd class="mt-1.5 text-body font-medium text-ink">{{ $product->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </x-card>

            <!-- Informasi Nilai Gizi -->
            <x-card class="w-full sm:p-8">
                <div class="border-b border-line pb-4">
                    <h2 class="text-lg font-semibold text-ink">Informasi Nilai Gizi</h2>
                    <p class="mt-0.5 text-small text-muted">Per 1 {{ $unitLabel }} &middot; dikelola Ahli Gizi</p>
                </div>

                <dl class="divide-y divide-line text-body">
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted">Energi Kalori</dt>
                        <dd class="font-medium text-ink">{{ number_format((float) ($product->calories ?? 0), 0) }} kkal</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted">Protein</dt>
                        <dd class="font-medium text-ink">{{ number_format((float) ($product->protein ?? 0), 1) }} g</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted">Lemak Total</dt>
                        <dd class="font-medium text-ink">{{ number_format((float) ($product->fat ?? 0), 1) }} g</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted">Karbohidrat Total</dt>
                        <dd class="font-medium text-ink">{{ number_format((float) ($product->carbohydrates ?? 0), 1) }} g</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted">Serat Pangan</dt>
                        <dd class="font-medium text-ink">{{ number_format((float) ($product->dietary_fiber ?? 0), 1) }} g</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <!-- Komposisi Resep -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="flex flex-col gap-4 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-ink">Komposisi Resep Bahan Baku</h2>
                    <p class="mt-0.5 text-small text-muted">Rincian takaran bahan mentah untuk memproduksi 1 {{ $unitLabel }} hidangan ini.</p>
                </div>

                @if ($canManage)
                <a href="{{ route('products.recipe.edit', $product) }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                    Kelola Resep
                </a>
                @endif
            </div>

            @if ($product->recipeItems->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">#</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Nama Bahan Baku</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Takaran / 1 {{ $unitLabel }}</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Satuan Resep</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Satuan Baku Gudang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach ($product->recipeItems as $index => $item)
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-6 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('ingredients.show', $item->ingredient) }}" class="font-medium text-ink transition hover:text-primary">
                                    {{ $item->ingredient->name }}
                                </a>
                                @if ($item->ingredient->category)
                                <div class="text-small text-muted">{{ $item->ingredient->category }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-ink">
                                {{ rtrim(rtrim(number_format((float) $item->quantity_per_unit, 3, ',', '.'), '0'), ',') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $item->unit }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $item->ingredient->unit }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-line bg-primary/5 px-6 py-4 text-small text-muted sm:flex-row sm:items-center sm:justify-between">
                <span>Total: <span class="font-medium text-ink">{{ $product->recipeItems->count() }}</span> jenis bahan baku mentah terdaftar.</span>
                <span>Resep ini siap digunakan oleh kalkulator bahan produksi.</span>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <p class="font-medium text-ink">Menu ini belum memiliki komposisi resep bahan baku.</p>
                <p class="mx-auto mt-1 max-w-md text-small text-muted">
                    Tentukan takaran bahan baku mentah agar sistem dapat menghitung kebutuhan logistik secara otomatis pada rencana produksi.
                </p>
                @if ($canManage)
                <div class="mt-4">
                    <a href="{{ route('products.recipe.edit', $product) }}"
                        class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                        Susun Resep Sekarang
                    </a>
                </div>
                @endif
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>