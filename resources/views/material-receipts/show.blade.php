<x-app-layout>
    {{-- Header: breadcrumb + nomor + status, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between print:hidden">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('material-receipts.index') }}" class="text-muted transition hover:text-primary">Daftar Penerimaan</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Penerimaan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">{{ $receipt->receipt_number }}</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                    Terverifikasi Gudang
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('material-receipts.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                Cetak Bukti Penerimaan
            </button>
        </div>
    </div>

    <div class="space-y-6">

        <x-flash-alert />

        <!-- Informasi penerimaan -->
        <x-card class="w-full sm:p-8">
            <p class="text-small text-muted">
                Dicatat pada
                <span class="font-medium text-ink">{{ $receipt->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</span>
            </p>

            <dl class="mt-6 grid grid-cols-1 gap-4 border-t border-line pt-6 md:grid-cols-2">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">No. Penerimaan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $receipt->receipt_number }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Tanggal Terima</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $receipt->receipt_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Referensi Purchase Order</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">
                        @if ($receipt->purchaseOrder)
                        <a href="{{ route('purchase-orders.show', $receipt->purchaseOrder) }}" class="text-primary transition hover:text-primary-dark">
                            {{ $receipt->purchaseOrder->order_number }}
                        </a>
                        <span class="text-small font-normal text-muted">({{ $receipt->purchaseOrder->vendor?->name }})</span>
                        @else
                        <span class="text-muted">Penerimaan Langsung (Tanpa PO)</span>
                        @endif
                    </dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Diterima &amp; Diperiksa Oleh</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $receipt->receiver?->name ?? '-' }}</dd>
                </div>

                @if ($receipt->notes)
                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Catatan / Surat Jalan</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink">{{ $receipt->notes }}</dd>
                </div>
                @endif
            </dl>
        </x-card>

        <!-- Daftar bahan -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="border-b border-line px-6 py-5">
                <h2 class="text-lg font-semibold text-ink">Daftar Bahan &amp; Kondisi Fisik Diterima</h2>
                <p class="mt-0.5 text-small text-muted">Hanya bahan berkondisi baik yang menambah stok gudang.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">#</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Jumlah</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Kondisi Fisik</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Mutasi Stok</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach ($receipt->details as $index => $detail)
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-6 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-ink">{{ $detail->ingredient?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">{{ number_format((float) $detail->quantity, 2, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $detail->unit }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($detail->condition->isAccepted())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                                    {{ $detail->condition->label() }}
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-bg px-2.5 py-1 text-small font-medium text-danger-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-danger-text"></span>
                                    {{ $detail->condition->label() }}
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($detail->condition->isAccepted())
                                <span class="font-medium text-primary">Masuk stok (+{{ (float) $detail->quantity }})</span>
                                @else
                                <span class="font-medium text-danger-text">Ditolak (0 stok)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-small text-muted">{{ $detail->notes ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

    </div>
</x-app-layout>