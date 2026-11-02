<x-app-layout>
    @php
        $canManage = in_array(Auth::user()->role?->value, ['admin', 'gudang'], true);
        $hasFilter = $currentSearch || $currentDate;

        $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    @endphp

    <x-page-header eyebrow="Pengadaan" :title="__('Penerimaan Bahan Baku Supplier')"
                   subtitle="Pencatatan barang tiba dari supplier, verifikasi kondisi fisik (Baik/Rusak), dan mutasi stok masuk otomatis.">
        <x-slot name="actions">
            @if ($canManage)
            <x-button :href="route('material-receipts.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Catat Penerimaan Baru
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

        <!-- Filter: pencarian + tanggal terima -->
        <x-card>
            <form method="GET" action="{{ route('material-receipts.index') }}"
                  class="grid grid-cols-1 items-end gap-4 md:grid-cols-[1fr_12rem_auto]">
                <div>
                    <label for="search" class="mb-1.5 block text-label uppercase text-muted">Cari No. Penerimaan / No. PO</label>
                    <input id="search" type="text" name="search" value="{{ $currentSearch }}"
                           placeholder="Cari RC-2026... atau PO-2026..." class="{{ $inputClass }}">
                </div>

                <div>
                    <label for="date" class="mb-1.5 block text-label uppercase text-muted">Tanggal Terima</label>
                    <input id="date" type="date" name="date" value="{{ $currentDate }}"
                           onchange="this.form.submit()" class="{{ $inputClass }}">
                </div>

                <div class="flex items-center gap-4 md:h-[2.625rem]">
                    @if ($hasFilter)
                    <a href="{{ route('material-receipts.index') }}"
                       class="text-small font-medium text-primary hover:text-primary-dark">Reset</a>
                    @endif
                </div>

                {{-- Enter pada kolom pencarian tetap mengirim form --}}
                <button type="submit" class="sr-only">Cari</button>
            </form>
        </x-card>

        <!-- Tabel Penerimaan -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">No. Tanda Terima</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Tgl Terima</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Referensi PO</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Supplier</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Jml Item</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Penerima</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($receipts as $receipt)
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('material-receipts.show', $receipt) }}" class="font-medium text-primary transition hover:text-primary-dark">
                                    {{ $receipt->receipt_number }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $receipt->receipt_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($receipt->purchaseOrder)
                                <a href="{{ route('purchase-orders.show', $receipt->purchaseOrder) }}" class="text-primary transition hover:text-primary-dark">
                                    {{ $receipt->purchaseOrder->order_number }}
                                </a>
                                @else
                                <span class="text-muted">Penerimaan Langsung</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-ink">{{ $receipt->purchaseOrder?->vendor?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $receipt->details_count }} item</td>
                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $receipt->receiver?->name ?? '-' }}</td>

                            <!-- Aksi (teks, bukan tombol) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <a href="{{ route('material-receipts.show', $receipt) }}" class="text-primary transition hover:text-primary-dark">Rincian</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Belum ada data penerimaan bahan yang dicatat.</p>
                                @if ($hasFilter)
                                <a href="{{ route('material-receipts.index') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                                @elseif ($canManage)
                                <a href="{{ route('material-receipts.create') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Catat penerimaan bahan pertama</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($receipts->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $receipts->links() }}
            </div>
            @endif
        </x-card>

    </div>
</x-app-layout>