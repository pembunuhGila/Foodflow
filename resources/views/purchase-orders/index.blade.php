<x-app-layout>
    @php
    $isAdmin = Auth::user()->isAdmin();
    $hasFilter = $currentSearch || $currentStatus || $currentDate;

    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

    // Tab status membawa pencarian dan tanggal pesan
    $tabLink = fn ($s) => route('purchase-orders.index', array_filter([
    'search' => $currentSearch,
    'order_date' => $currentDate,
    'status' => $s,
    ]));
    $total = method_exists($orders, 'total') ? $orders->total() : null;

    // Daftar tab: "Semua" + semua status dari controller
    $tabs = [['label' => 'Semua', 'value' => '']];
    foreach ($statuses as $st) {
    $tabs[] = ['label' => $st->label(), 'value' => $st->value];
    }
    foreach ($tabs as $i => $tab) {
    $tabs[$i]['active'] = (string) $currentStatus === (string) $tab['value'];
    $tabs[$i]['text'] = $tab['label'] . (($tabs[$i]['active'] && $total !== null) ? ' (' . $total . ')' : '');
    }
    @endphp

    <x-page-header eyebrow="Pengadaan" :title="__('Daftar Purchase Order')"
        subtitle="Kelola semua purchase order pengadaan bahan baku ke vendor.">
        <x-slot name="actions">
            @if ($isAdmin)
            <x-button :href="route('purchase-orders.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Buat PO Baru
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

        @if (session('error'))
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            {{ session('error') }}
        </div>
        @endif

        <!-- Filter: pencarian + tanggal + tab status dalam satu card -->
        <x-card>
            <div class="space-y-4">
                <form method="GET" action="{{ route('purchase-orders.index') }}"
                    class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 xl:grid-cols-[1fr_12rem_auto]">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">

                    <div>
                        <label for="search" class="mb-1.5 block text-label uppercase text-muted">Cari Nomor PO</label>
                        <input id="search" type="text" name="search" value="{{ $currentSearch }}"
                            placeholder="Cari nomor PO..." class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="order_date" class="mb-1.5 block text-label uppercase text-muted">Tanggal Pesan</label>
                        <input id="order_date" type="date" name="order_date" value="{{ $currentDate }}"
                            onchange="this.form.submit()" class="{{ $inputClass }}">
                    </div>

                    <div class="flex items-center gap-4 xl:h-[2.625rem]">
                        @if ($hasFilter)
                        <a href="{{ route('purchase-orders.index') }}"
                            class="text-small font-medium text-primary hover:text-primary-dark">Reset</a>
                        @endif
                    </div>

                    {{-- Enter pada kolom pencarian tetap mengirim form --}}
                    <button type="submit" class="sr-only">Cari</button>
                </form>

                {{-- Tab status: selebar card --}}
                <nav class="flex w-full items-center gap-1 overflow-x-auto rounded-full bg-primary/5 p-1" aria-label="Filter status purchase order">
                    @foreach ($tabs as $tab)
                    <a href="{{ $tabLink($tab['value']) }}"
                        class="flex-1 whitespace-nowrap rounded-full px-5 py-2 text-center text-small font-semibold transition {{ $tab['active'] ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                        {{ $tab['text'] }}
                    </a>
                    @endforeach
                </nav>
            </div>
        </x-card>

        <!-- Tabel Purchase Order -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Nomor PO</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Vendor</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tgl Pesan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tgl Kirim</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Jml Item</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Dibuat Oleh</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($orders as $order)
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('purchase-orders.show', $order) }}" class="font-medium text-primary transition hover:text-primary-dark">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-medium text-ink">{{ $order->vendor?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $order->order_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $order->expected_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $order->details_count }} item</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($order->status)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                                    {{ $order->status->label() }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $order->creator?->name ?? '-' }}</td>

                            <!-- Aksi (teks, bukan tombol) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <a href="{{ route('purchase-orders.show', $order) }}" class="text-primary transition hover:text-primary-dark">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Belum ada purchase order yang ditemukan.</p>
                                @if ($hasFilter)
                                <a href="{{ route('purchase-orders.index') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                                @elseif ($isAdmin)
                                <a href="{{ route('purchase-orders.create') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Buat PO pertama Anda</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($orders->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $orders->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>