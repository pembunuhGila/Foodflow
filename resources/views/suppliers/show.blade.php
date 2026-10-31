<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('suppliers.index') }}" class="text-muted transition hover:text-primary">Daftar Supplier</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Supplier</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">{{ $supplier->name }}</h1>
                @if ($supplier->isActive())
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
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            @if (auth()->user()->isAdmin())
            <a href="{{ route('suppliers.edit', $supplier) }}"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Supplier
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

        <x-card class="w-full sm:p-8">
            <p class="text-small text-muted">
                Terdaftar di sistem sejak
                <span class="font-medium text-ink">{{ $supplier->created_at?->locale('id')->translatedFormat('d F Y, H:i') }}</span>
            </p>

            <dl class="mt-6 grid grid-cols-1 gap-4 border-t border-line pt-6 md:grid-cols-2">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Nomor Telepon / WhatsApp</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $supplier->phone ?: '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Alamat Email</dt>
                    <dd class="mt-1.5 break-all text-body font-medium text-ink">{{ $supplier->email ?: '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Alamat Kantor / Gudang Supplier</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $supplier->address ?: '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Catatan Kemitraan &amp; Spesifikasi Bahan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $supplier->notes ?: '-' }}</dd>
                </div>
            </dl>
        </x-card>

    </div>
</x-app-layout>