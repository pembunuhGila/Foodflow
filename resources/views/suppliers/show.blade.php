<x-app-layout>
    <x-page-header eyebrow="Master Data" :title="__('Detail Supplier') . ': ' . $supplier->name">
        <x-slot name="actions">
            <x-button variant="outline" :href="route('suppliers.index')">&larr; Kembali</x-button>
            @if (auth()->user()->isAdmin())
            <x-button :href="route('suppliers.edit', $supplier)">Edit Supplier</x-button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <!-- Card Utama Profil Supplier -->
        <x-card class="sm:p-8">
            <div class="flex flex-col justify-between gap-4 border-b border-line pb-6 md:flex-row md:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="font-display text-h1 text-ink">{{ $supplier->name }}</h3>
                        @if ($supplier->isActive())
                        <x-badge variant="success"><span class="h-2 w-2 rounded-full bg-primary"></span> Mitra Aktif</x-badge>
                        @else
                        <x-badge variant="neutral"><span class="h-2 w-2 rounded-full bg-muted"></span> Kemitraan Nonaktif</x-badge>
                        @endif
                    </div>
                    <p class="mt-1 text-body text-muted">
                        Terdaftar di sistem sejak: {{ $supplier->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Informasi Kontak & Alamat -->
            <div class="grid grid-cols-1 gap-4 pt-6 md:grid-cols-2">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <span class="block text-label uppercase text-muted">Nomor Telepon / WhatsApp</span>
                    <div class="mt-2 font-display text-h3 text-ink">
                        {{ $supplier->phone ?: 'Tidak dicantumkan' }}
                    </div>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <span class="block text-label uppercase text-muted">Alamat Email</span>
                    <div class="mt-2 font-display text-h3 text-ink">
                        {{ $supplier->email ?: 'Tidak dicantumkan' }}
                    </div>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <span class="block text-label uppercase text-muted">Alamat Kantor / Gudang Supplier</span>
                    <p class="mt-2 text-body leading-relaxed text-ink">
                        {{ $supplier->address ?: 'Alamat belum dilengkapi.' }}
                    </p>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <span class="block text-label uppercase text-muted">Catatan Kemitraan & Spesifikasi Bahan</span>
                    <p class="mt-2 text-body leading-relaxed text-ink">
                        {{ $supplier->notes ?: 'Tidak ada catatan khusus untuk supplier ini.' }}
                    </p>
                </div>
            </div>
        </x-card>

    </div>
</x-app-layout>