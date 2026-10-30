<x-app-layout>
    <x-page-header eyebrow="Master Data" :title="__('Master Data Supplier')">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
            <x-button :href="route('suppliers.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Supplier Baru
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

        <!-- Filter & Search Bar -->
        @php
        $search = $filters['search'] ?? '';
        $status = $filters['status'] ?? '';
        $total = method_exists($suppliers, 'total') ? $suppliers->total() : null;
        $tabLink = fn ($s) => route('suppliers.index', array_filter(['search' => $search, 'status' => $s]));
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
        @endphp
        <x-filter-bar :action="route('suppliers.index')" :search="$search"
            placeholder="Cari nama vendor, nomor telepon, atau alamat..."
            :tabs="$tabs" :hidden="['status' => $status]">
            @if ($search !== '' || $status !== '')
            <a href="{{ route('suppliers.index') }}" class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
            @endif
        </x-filter-bar>

        <!-- Suppliers Table -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Nama Supplier / Vendor</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Kontak (Telepon & Email)</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Alamat</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($suppliers as $item)
                        <tr class="transition hover:bg-primary/5">
                            <!-- Nama & Catatan -->
                            <td class="px-6 py-4">
                                <div class="font-medium text-ink">{{ $item->name }}</div>
                                @if ($item->notes)
                                <div class="mt-0.5 max-w-xs truncate text-small text-muted" title="{{ $item->notes }}">{{ $item->notes }}</div>
                                @endif
                            </td>

                            <!-- Kontak -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-small font-medium text-ink">{{ $item->phone ?: '-' }}</div>
                                <div class="text-small text-muted">{{ $item->email ?: '-' }}</div>
                            </td>

                            <!-- Alamat -->
                            <td class="px-6 py-4">
                                <div class="max-w-sm truncate text-small text-muted" title="{{ $item->address }}">
                                    {{ $item->address ?: '-' }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($item->isActive())
                                <x-badge variant="success"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Aktif</x-badge>
                                @else
                                <x-badge variant="neutral"><span class="h-1.5 w-1.5 rounded-full bg-muted"></span> Nonaktif</x-badge>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <!-- Detail (Admin, Gudang, Owner) -->
                                    <a href="{{ route('suppliers.show', $item) }}" class="text-primary transition hover:text-primary-dark">Detail</a>

                                    @if (auth()->user()->isAdmin())
                                    <!-- Edit -->
                                    <a href="{{ route('suppliers.edit', $item) }}" class="text-primary transition hover:text-primary-dark">Edit</a>

                                    <!-- Toggle Status (BR-02: Soft Toggle) -->
                                    @php
                                    $confirmData = [
                                    'formId' => 'toggle-supplier-' . $item->getKey(),
                                    'tone' => $item->isActive() ? 'danger' : 'primary',
                                    'eyebrow' => 'Ubah status supplier',
                                    'title' => $item->isActive() ? 'Nonaktifkan supplier ini?' : 'Aktifkan supplier ini?',
                                    'subjectLabel' => 'Supplier',
                                    'subject' => $item->name,
                                    'message' => 'Apakah Anda yakin ingin mengubah status supplier ini?',
                                    'detail' => 'Data dan riwayat supplier tidak dihapus.',
                                    'confirmLabel' => $item->isActive() ? 'Ya, Nonaktifkan Supplier' : 'Ya, Aktifkan Supplier',
                                    ];
                                    @endphp
                                    <form id="toggle-supplier-{{ $item->getKey() }}" method="POST" action="{{ route('suppliers.toggle-status', $item) }}" class="inline-block">
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
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="truck" class="mb-3 h-10 w-10 text-muted/50" />
                                    <p class="font-medium text-ink">Tidak ada data supplier yang ditemukan.</p>
                                    <p class="mt-1 text-small text-muted">Coba sesuaikan kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($suppliers->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $suppliers->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>