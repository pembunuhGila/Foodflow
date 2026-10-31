<x-app-layout>
    @php
        $canManage = in_array(auth()->user()->role?->value, ['admin', 'gudang'], true);

        $search = $filters['search'] ?? '';
        $status = $filters['status'] ?? '';
        $category = $filters['category'] ?? '';
        $stockStatus = $filters['stock_status'] ?? '';

        $hasFilter = $search !== '' || $status !== '' || $category !== '' || $stockStatus !== '';
        $total = method_exists($ingredients, 'total') ? $ingredients->total() : null;

        // Tab status tetap membawa pencarian, kategori, dan kondisi stok
        $tabLink = fn ($s) => route('ingredients.index', array_filter([
            'search' => $search,
            'category' => $category,
            'stock_status' => $stockStatus,
            'status' => $s,
        ]));
        $tabs = [
            ['label' => 'Semua', 'href' => $tabLink(''), 'active' => $status === ''],
            ['label' => 'Aktif', 'href' => $tabLink('active'), 'active' => $status === 'active'],
            ['label' => 'Nonaktif', 'href' => $tabLink('inactive'), 'active' => $status === 'inactive'],
        ];
        foreach ($tabs as $i => $tab) {
            if ($tab['active'] && $total !== null) {
                $tabs[$i]['count'] = $total;
            }
        }

        $selectClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    @endphp

    <x-page-header eyebrow="Master Data" :title="__('Master Data Bahan Baku')"
                   subtitle="Kelola data bahan makanan mentah, satuan unit, dan batas minimum stok persediaan.">
        <x-slot name="actions">
            @if ($canManage)
            <x-button :href="route('ingredients.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Bahan Baku Baru
            </x-button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <!-- Session Alert -->
        @if (session('success'))
        <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary" role="status">
            <x-icon name="check" class="h-5 w-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Peringatan Stok Kritis -->
        @if (($criticalCount ?? 0) > 0)
        <div class="flex flex-col gap-3 rounded-input border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between" role="alert">
            <div>
                <p class="text-body font-semibold text-amber-900">Peringatan batas minimum stok</p>
                <p class="mt-0.5 text-small text-amber-800">
                    Terdapat <span class="font-semibold">{{ $criticalCount }}</span> bahan baku yang berada pada batas kritis atau di bawah stok minimum.
                </p>
            </div>
            @if ($stockStatus === 'low_stock')
            <a href="{{ route('ingredients.index') }}" class="text-small font-medium text-amber-900 transition hover:underline">Tampilkan semua bahan</a>
            @else
            <a href="{{ route('ingredients.index', ['stock_status' => 'low_stock']) }}" class="text-small font-medium text-amber-900 transition hover:underline">Lihat {{ $criticalCount }} bahan kritis</a>
            @endif
        </div>
        @endif

        <!-- Filter: pencarian + kategori + kondisi stok + tab status dalam satu card -->
<x-card>
    <div class="flex flex-col gap-3 xl:flex-row xl:items-center">

        {{-- Pencarian & dropdown (satu form) --}}
        <form method="GET" action="{{ route('ingredients.index') }}"
              class="flex flex-1 flex-col gap-3 md:flex-row md:items-center">
            <input type="hidden" name="status" value="{{ $status }}">

            <div class="relative min-w-0 flex-1">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama bahan atau catatan..."
                       class="block w-full rounded-full border-0 bg-primary/5 py-3 pl-12 pr-4 text-body text-ink placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>

            <select name="category" onchange="this.form.submit()" aria-label="Kategori"
                    class="rounded-full border-0 bg-primary/5 px-4 py-3 text-body text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 md:w-48">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
                @endforeach
            </select>

            <select name="stock_status" onchange="this.form.submit()" aria-label="Kondisi stok"
                    class="rounded-full border-0 bg-primary/5 px-4 py-3 text-body text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 md:w-52">
                <option value="">Semua Kondisi Stok</option>
                <option value="low_stock" @selected($stockStatus === 'low_stock')>Kritis / Menipis</option>
                <option value="out_of_stock" @selected($stockStatus === 'out_of_stock')>Stok Habis (0)</option>
                <option value="sufficient" @selected($stockStatus === 'sufficient')>Stok Aman</option>
            </select>

            <button type="submit" class="sr-only">Cari</button>
        </form>

        {{-- Tab status + reset --}}
        <div class="flex flex-wrap items-center gap-3">
            <nav class="inline-flex items-center gap-1 rounded-full bg-primary/5 p-1" aria-label="Filter status bahan baku">
                @foreach ($tabs as $tab)
                <a href="{{ $tab['href'] }}"
                   @if ($tab['active']) aria-current="page" @endif
                   class="rounded-full px-5 py-2 text-small font-semibold transition {{ $tab['active'] ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                    {{ $tab['label'] }}@if (isset($tab['count'])) ({{ $tab['count'] }})@endif
                </a>
                @endforeach
            </nav>

            @if ($hasFilter)
            <a href="{{ route('ingredients.index') }}" class="text-small font-medium text-primary hover:text-primary-dark">Reset</a>
            @endif
        </div>
    </div>
</x-card>

        <!-- Tabel Bahan Baku -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Nama Bahan Baku</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Kategori</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Stok Saat Ini</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Batas Min</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Kondisi Stok</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($ingredients as $item)
                        <tr class="transition hover:bg-primary/5">
                            <!-- Nama -->
                            <td class="px-6 py-4 font-medium text-ink">{{ $item->name }}</td>

                            <!-- Kategori -->
                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $item->category ?: 'Umum' }}</td>

                            <!-- Satuan -->
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $item->unit }}</td>

                            <!-- Stok saat ini -->
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if ($item->isOutOfStock())
                                <span class="font-medium text-danger-text">0,00</span>
                                @elseif ($item->isLowStock())
                                <span class="font-medium text-amber-700">{{ number_format($item->current_stock, 2, ',', '.') }}</span>
                                @else
                                <span class="font-medium text-ink">{{ number_format($item->current_stock, 2, ',', '.') }}</span>
                                @endif
                                <span class="text-small text-muted">{{ $item->unit }}</span>
                            </td>

                            <!-- Batas minimum -->
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <span class="font-medium text-ink">{{ number_format($item->minimum_stock, 2, ',', '.') }}</span>
                                <span class="text-small text-muted">{{ $item->unit }}</span>
                            </td>

                            <!-- Kondisi stok -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($item->isOutOfStock())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-bg px-2.5 py-1 text-small font-medium text-danger-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-danger-text"></span> Stok Habis
                                </span>
                                @elseif ($item->isLowStock())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-small font-medium text-amber-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Stok Menipis
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Stok Aman
                                </span>
                                @endif
                            </td>

                            <!-- Status: Aktif violet, Nonaktif abu-abu -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($item->isActive())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-small font-medium text-gray-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                </span>
                                @endif
                            </td>

                            <!-- Aksi (teks, bukan tombol) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('ingredients.show', $item) }}" class="text-primary transition hover:text-primary-dark">Detail</a>

                                    @if ($canManage)
                                    <a href="{{ route('ingredients.edit', $item) }}" class="text-primary transition hover:text-primary-dark">Edit</a>

                                    @php
                                    $confirmData = [
                                        'formId' => 'toggle-ingredient-' . $item->getKey(),
                                        'tone' => $item->isActive() ? 'danger' : 'primary',
                                        'eyebrow' => 'Ubah status bahan baku',
                                        'title' => $item->isActive() ? 'Nonaktifkan bahan baku ini?' : 'Aktifkan bahan baku ini?',
                                        'subjectLabel' => 'Bahan Baku',
                                        'subject' => $item->name,
                                        'message' => 'Apakah Anda yakin ingin mengubah status bahan baku ini?',
                                        'detail' => '',
                                        'confirmLabel' => $item->isActive() ? 'Ya, Nonaktifkan Bahan Baku' : 'Ya, Aktifkan Bahan Baku',
                                    ];
                                    @endphp
                                    <form id="toggle-ingredient-{{ $item->getKey() }}" method="POST" action="{{ route('ingredients.toggle-status', $item) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" @click="$dispatch('open-confirm', @js($confirmData))"
                                            class="text-small font-medium transition {{ $item->isActive() ? 'text-danger-text hover:underline' : 'text-primary hover:text-primary-dark' }}">
                                            {{ $item->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Tidak ada data bahan baku yang ditemukan.</p>
                                <p class="mt-1 text-small text-muted">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($ingredients->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $ingredients->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>