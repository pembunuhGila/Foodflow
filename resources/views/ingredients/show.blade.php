<x-app-layout>
    {{-- Header: breadcrumb + nama + status, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('ingredients.index') }}" class="text-muted transition hover:text-primary">Daftar Bahan Baku</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Bahan Baku</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">{{ $ingredient->name }}</h1>
                @if ($ingredient->isActive())
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
            <a href="{{ route('ingredients.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            @if (in_array(auth()->user()->role?->value, ['admin', 'gudang'], true))
            <a href="{{ route('ingredients.edit', $ingredient) }}"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Bahan Baku
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

        {{-- Peringatan batas minimum stok --}}
        @if ($ingredient->isOutOfStock())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="font-semibold">Peringatan: stok bahan baku habis</p>
            <p class="mt-1 text-small">
                Persediaan saat ini 0,00 {{ $ingredient->unit }}, sementara batas stok minimum adalah {{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}.
                Segera lakukan pengadaan atau pemesanan ke supplier.
            </p>
        </div>
        @elseif ($ingredient->isLowStock())
        <div class="rounded-input border border-amber-200 bg-amber-50 p-4 text-body text-amber-800" role="alert">
            <p class="font-semibold">Peringatan: batas minimum stok</p>
            <p class="mt-1 text-small">
                Persediaan saat ini ({{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}) berada di bawah atau menyentuh batas minimum ({{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}).
                Dibutuhkan restock minimal {{ number_format($ingredient->getStockDeficit(), 2) }} {{ $ingredient->unit }}.
            </p>
        </div>
        @endif

        <x-card class="w-full sm:p-8">
            <p class="text-small text-muted">
                Terdaftar di sistem sejak
                <span class="font-medium text-ink">{{ $ingredient->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</span>
            </p>

            <dl class="mt-6 grid grid-cols-1 gap-4 border-t border-line pt-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Kategori Bahan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $ingredient->category ?? 'Umum / Belum Ditentukan' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Satuan Baku</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $ingredient->unit }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Kondisi Stok</dt>
                    <dd class="mt-1.5">
                        @if ($ingredient->isOutOfStock())
                        <span class="inline-flex items-center rounded-full bg-danger-bg px-2.5 py-1 text-small font-medium text-danger-text">Stok Habis</span>
                        @elseif ($ingredient->isLowStock())
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-small font-medium text-amber-800">Stok Menipis</span>
                        @else
                        <span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">Stok Aman</span>
                        @endif
                    </dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-1 lg:col-span-1">
                    <dt class="text-label uppercase text-muted">Stok Saat Ini</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Batas Minimum</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2 lg:col-span-3">
                    <dt class="text-label uppercase text-muted">Catatan Spesifikasi &amp; Penanganan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">
                        {{ $ingredient->notes ?: 'Tidak ada catatan spesifikasi khusus untuk bahan baku ini.' }}
                    </dd>
                </div>
            </dl>
        </x-card>

    </div>
</x-app-layout>