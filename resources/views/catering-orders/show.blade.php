<x-app-layout>
    @php
    $isAdmin = Auth::user()?->isAdmin();
    $st = $order->status instanceof \BackedEnum ? $order->status->value : (string) $order->status;

    $statusBadge = [
    'pending' => ['bg-amber-50 text-amber-800 border border-amber-200', 'bg-amber-500'],
    'confirmed' => ['bg-info-bg text-info-text border border-info-text/20', 'bg-info-text'],
    'processing' => ['bg-primary text-white', 'bg-white'],
    'delivered' => ['bg-success-bg text-success-text border border-success-text/20', 'bg-success-text'],
    'cancelled' => ['bg-danger-bg text-danger-text border border-danger-text/20', 'bg-danger-text'],
    ];
    $badge = $statusBadge[$st] ?? ['bg-gray-100 text-gray-600', 'bg-gray-400'];

    $nextStep = [
    'pending' => ['status' => 'confirmed', 'label' => 'Konfirmasi'],
    'confirmed' => ['status' => 'processing', 'label' => 'Proses'],
    'processing' => ['status' => 'delivered', 'label' => 'Selesai'],
    ];
    $next = $nextStep[$st] ?? null;

    $nextConfirm = $next ? [
    'formId' => 'next-order-form',
    'tone' => 'primary',
    'eyebrow' => 'Ubah status pesanan',
    'title' => $next['label'] . ' pesanan ini?',
    'subjectLabel' => 'Pesanan',
    'subject' => $order->order_number,
    'message' => 'Apakah Anda yakin ingin mengubah status pesanan ini?',
    'detail' => '',
    'confirmLabel' => 'Ya, ' . $next['label'],
    ] : null;

    $cancelConfirm = [
    'formId' => 'cancel-order-form',
    'tone' => 'danger',
    'eyebrow' => 'Batalkan pesanan',
    'title' => 'Batalkan pesanan ini?',
    'subjectLabel' => 'Pesanan',
    'subject' => $order->order_number,
    'message' => 'Apakah Anda yakin ingin membatalkan pesanan ini?',
    'detail' => 'Status pesanan akan menjadi Dibatalkan dan dapat diubah kembali melalui halaman edit.',
    'confirmLabel' => 'Ya, Batalkan Pesanan',
    ];
    @endphp

    {{-- Header: breadcrumb + nomor + status, tombol di kanan --}}
    <div class="no-print mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('catering-orders.index') }}" class="text-muted transition hover:text-primary">Daftar Pesanan</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Pesanan</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink" id="order-invoice-number">{{ $order->order_number }}</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-small font-medium {{ $badge[0] }}" id="order-status-badge">
                    <span class="h-1.5 w-1.5 rounded-full {{ $badge[1] }}"></span> {{ $order->status_label }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('catering-orders.index') }}" id="btn-back-to-list"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            @if ($isAdmin)
            <a href="{{ route('catering-orders.create') }}" id="btn-create-another-order"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                <x-icon name="plus" class="h-4 w-4" />
                Buat Pesanan Baru
            </a>
            @endif
        </div>
    </div>

    <div class="space-y-6" x-data="{ showInvoiceModal: false }">

        <x-flash-alert />

        <!-- Informasi Pesanan -->
        <x-card class="w-full sm:p-8">
            <div class="flex flex-col gap-3 border-b border-line pb-4 md:flex-row md:items-center md:justify-between">
                <p class="text-small text-muted">
                    Dibuat pada
                    <span class="font-medium text-ink">{{ $order->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                </p>

                {{-- Aksi (Tombol Button) --}}
                <div class="no-print flex flex-wrap items-center gap-2">
                    <button type="button" @click="showInvoiceModal = true" id="btn-view-invoice-summary"
                        class="inline-flex items-center gap-1.5 rounded-input border border-line bg-white px-3 py-1.5 text-small font-medium text-ink shadow-sm transition hover:bg-primary/5 hover:border-primary/30">
                        <x-icon name="eye" class="h-4 w-4 text-muted" />
                        <span>Lihat Ringkasan Invoice</span>
                    </button>

                    <button type="button" onclick="window.print()" id="btn-print-invoice"
                        class="inline-flex items-center gap-1.5 rounded-input border border-primary/30 bg-primary/5 px-3 py-1.5 text-small font-medium text-primary shadow-sm transition hover:bg-primary/10">
                        <x-icon name="printer" class="h-4 w-4" />
                        <span>Cetak Invoice</span>
                    </button>

                    @if ($isAdmin)
                    <a href="{{ route('catering-orders.edit', $order) }}" id="btn-edit-order"
                        class="inline-flex items-center gap-1.5 rounded-input border border-line bg-white px-3 py-1.5 text-small font-medium text-ink shadow-sm transition hover:bg-primary/5">
                        <svg class="h-3.5 w-3.5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Edit Pesanan</span>
                    </a>

                    @if ($next)
                    <form id="next-order-form" method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next['status'] }}">
                        <button type="button" @click="$dispatch('open-confirm', @js($nextConfirm))"
                            class="inline-flex items-center gap-1.5 rounded-input bg-primary px-3.5 py-1.5 text-small font-medium text-white shadow-sm transition hover:bg-primary-dark">
                            <x-icon name="check" class="h-4 w-4" />
                            <span>{{ $next['label'] }}</span>
                        </button>
                    </form>
                    @endif

                    @if ($st !== 'cancelled')
                    <form id="cancel-order-form" method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="button" @click="$dispatch('open-confirm', @js($cancelConfirm))" id="btn-cancel-order"
                            class="inline-flex items-center gap-1.5 rounded-input border border-danger-text/20 bg-danger-bg/50 px-3 py-1.5 text-small font-medium text-danger-text shadow-sm transition hover:bg-danger-bg">
                            <span>Batalkan Pesanan</span>
                        </button>
                    </form>
                    @else
                    <span class="inline-flex items-center rounded-input bg-slate-100 px-3 py-1.5 text-small text-muted">Pesanan telah dibatalkan</span>
                    @endif
                    @endif
                </div>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Nama Pelanggan / Institusi</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink" id="customer-name">{{ $order->customer?->name ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">No. Telepon / WhatsApp</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink" id="customer-phone">{{ $order->customer?->phone ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Alamat Email</dt>
                    <dd class="mt-1.5 break-all text-body font-medium text-ink" id="customer-email">{{ $order->customer?->email ?: '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Status Pelanggan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->customer?->is_active ? 'Aktif' : 'Nonaktif' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Alamat Pengiriman Lengkap</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink" id="customer-address">{{ $order->customer?->address ?? 'Belum ada alamat pengiriman terdaftar' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Tanggal Pengiriman</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink" id="order-delivery-date">{{ $order->delivery_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Tanggal Pemesanan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink" id="order-date">{{ $order->order_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Petugas Input</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink" id="order-creator">{{ $order->creator?->name ?? 'Admin' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Terakhir Diperbarui</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Catatan Khusus Pesanan</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink" id="order-special-notes">{{ $order->notes ?: 'Tidak ada catatan khusus untuk pesanan katering ini.' }}</dd>
                </div>
            </dl>
        </x-card>

        <!-- Rincian Menu -->
        <x-card :padding="false" class="overflow-hidden" id="printable-order-details">
            <div class="flex flex-col gap-2 border-b border-line px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-ink">Rincian Menu Hidangan yang Dipesan</h2>
                    <p class="mt-0.5 text-small text-muted">Kuantitas porsi, harga satuan, dan subtotal masing-masing hidangan.</p>
                </div>
                <span class="text-small text-muted">Total <span class="font-medium text-ink">{{ $order->details->count() }}</span> jenis menu</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body" id="table-order-items">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">#</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Menu Hidangan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Jumlah Porsi</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Harga Satuan</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($order->details as $index => $detail)
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-6 py-4 text-center text-small text-muted">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="item-name font-medium text-ink">{{ $detail->product?->name ?? 'Produk #' . $detail->product_id }}</div>
                                @if ($detail->notes)
                                <div class="item-notes text-small text-muted">Catatan: {{ $detail->notes }}</div>
                                @endif
                            </td>
                            <td class="item-quantity whitespace-nowrap px-6 py-4 text-ink">{{ $detail->quantity }} {{ $detail->product?->unit ?? 'porsi' }}</td>
                            <td class="item-unit-price whitespace-nowrap px-6 py-4 text-right text-ink">Rp {{ number_format((float) $detail->unit_price, 0, ',', '.') }}</td>
                            <td class="item-subtotal whitespace-nowrap px-6 py-4 text-right font-medium text-ink">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-muted">Belum ada rincian item menu untuk pesanan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-line bg-primary/5">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-label uppercase text-muted">Total Akumulasi Porsi</td>
                            <td class="px-6 py-4 font-medium text-ink" id="total-portions">{{ $order->details->sum('quantity') }} porsi</td>
                            <td class="px-6 py-4 text-right text-label uppercase text-muted">Grand Total</td>
                            <td class="px-6 py-4 text-right text-lg font-semibold text-primary" id="order-grand-total">{{ $order->formattedTotal() }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        <!-- Kop & tanda tangan (khusus cetak) -->
        
<!-- Invoice khusus cetak -->
<div class="print-only hidden">
    <div class="invoice-sheet">

        <!-- Header Invoice -->
        <div class="invoice-header">
            <div class="invoice-company">
                <h1>FOODFLOW</h1>
                <p>Catering &amp; Logistics</p>
                <span>Sistem Informasi Manajemen Katering</span>
            </div>

            <div class="invoice-title">
                <h2>INVOICE</h2>
                <strong>{{ $order->order_number }}</strong>

                <div class="invoice-date">
                    <span>Tanggal Pemesanan</span>
                    <strong>{{ $order->order_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</strong>
                </div>

                <div class="invoice-date">
                    <span>Tanggal Pengiriman</span>
                    <strong>{{ $order->delivery_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="invoice-accent"></div>

        <!-- Informasi Pelanggan dan Total -->
        <div class="invoice-info">
            <div class="invoice-customer">
                <span class="invoice-label">KEPADA:</span>
                <h3>{{ $order->customer?->name ?? '-' }}</h3>

                <p>
                    No. Telepon:
                    {{ $order->customer?->phone ?? '-' }}
                </p>

                <p>
                    Email:
                    {{ $order->customer?->email ?? '-' }}
                </p>

                <p>
                    Alamat:
                    {{ $order->customer?->address ?? '-' }}
                </p>
            </div>

            <div class="invoice-amount">
                <span class="invoice-label">JUMLAH YANG HARUS DIBAYAR</span>
                <strong>{{ $order->formattedTotal() }}</strong>
                <span class="invoice-status">
                    Status: {{ $order->status_label }}
                </span>
            </div>
        </div>

        <!-- Tabel Rincian Pesanan -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th>Harga Satuan</th>
                    <th>Kuantitas</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($order->details as $detail)
                    <tr>
                        <td>
                            <strong>
                                {{ $detail->product?->name ?? 'Produk #' . $detail->product_id }}
                            </strong>

                            @if ($detail->notes)
                                <small>{{ $detail->notes }}</small>
                            @endif
                        </td>

                        <td>
                            Rp {{ number_format((float) $detail->unit_price, 0, ',', '.') }}
                        </td>

                        <td>
                            {{ $detail->quantity }}
                            {{ $detail->product?->unit ?? 'porsi' }}
                        </td>

                        <td class="invoice-item-total">
                            Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="invoice-empty">
                            Belum ada rincian menu untuk pesanan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Ringkasan Total -->
        <div class="invoice-bottom">

            <div class="invoice-notes">
                <strong>Catatan Pesanan</strong>
                <p>
                    {{ $order->notes ?: 'Terima kasih telah mempercayakan kebutuhan katering Anda kepada FoodFlow.' }}
                </p>
            </div>

            <div class="invoice-total">
                <div>
                    <span>Total Porsi</span>
                    <strong>{{ $order->details->sum('quantity') }} porsi</strong>
                </div>

                <div class="invoice-grand-total">
                    <span>Grand Total</span>
                    <strong>{{ $order->formattedTotal() }}</strong>
                </div>
            </div>

        </div>

        <!-- Footer Invoice -->
        <div class="invoice-footer">
            <div class="invoice-thanks">
                <h3>TERIMA KASIH</h3>
                <p>Terima kasih atas kepercayaan Anda kepada FoodFlow.</p>
            </div>

            <div class="invoice-signature">
                <span>Petugas FoodFlow</span>
                <div class="signature-space"></div>
                <strong>{{ $order->creator?->name ?? 'Admin FoodFlow' }}</strong>
            </div>
        </div>

    </div>
</div>
<!-- Akhir Invoice khusus cetak -->

        <!-- Modal ringkasan invoice -->
        <div x-show="showInvoiceModal" x-cloak class="no-print fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-invoice-title" role="dialog" aria-modal="true"
            @keydown.escape.window="showInvoiceModal = false">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div class="fixed inset-0 bg-ink/50" @click="showInvoiceModal = false" aria-hidden="true"></div>

                <div x-show="showInvoiceModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="relative w-full max-w-3xl rounded-card bg-white p-6 shadow-xl">

                    <div class="flex items-start justify-between border-b border-line pb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-ink" id="modal-invoice-title">Ringkasan Invoice Pesanan Katering</h3>
                            <p class="mt-0.5 text-small text-muted">Pratinjau tagihan resmi katering FoodFlow</p>
                        </div>
                        <button type="button" @click="showInvoiceModal = false" class="text-muted transition hover:text-ink" aria-label="Tutup">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4 py-5">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-input border border-line bg-primary/5 p-4">
                                <dt class="text-label uppercase text-muted">Nomor Invoice</dt>
                                <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->order_number }}</dd>
                            </div>
                            <div class="rounded-input border border-line bg-primary/5 p-4">
                                <dt class="text-label uppercase text-muted">Tanggal Pengiriman</dt>
                                <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->delivery_date?->locale('id')->translatedFormat('d F Y') }}</dd>
                            </div>
                            <div class="rounded-input border border-line bg-primary/5 p-4">
                                <dt class="text-label uppercase text-muted">Pelanggan</dt>
                                <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->customer?->name }}</dd>
                                <dd class="text-small text-muted">{{ $order->customer?->phone ?? '-' }}</dd>
                                <dd class="text-small text-muted">{{ $order->customer?->address ?? '-' }}</dd>
                            </div>
                            <div class="rounded-input border border-line bg-primary/5 p-4">
                                <dt class="text-label uppercase text-muted">Status & Petugas</dt>
                                <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->status_label }}</dd>
                                <dd class="text-small text-muted">Petugas: {{ $order->creator?->name ?? 'Admin' }}</dd>
                                @if ($order->notes)
                                <dd class="text-small text-muted">Catatan: {{ $order->notes }}</dd>
                                @endif
                            </div>
                        </dl>

                        <div class="overflow-x-auto rounded-input border border-line">
                            <table class="min-w-full text-small">
                                <thead class="bg-primary/5">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-label uppercase text-muted">Menu</th>
                                        <th class="px-4 py-3 text-left text-label uppercase text-muted">Porsi</th>
                                        <th class="px-4 py-3 text-right text-label uppercase text-muted">Harga Satuan</th>
                                        <th class="px-4 py-3 text-right text-label uppercase text-muted">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach ($order->details as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-ink">{{ $item->product?->name ?? 'Produk #' . $item->product_id }}</td>
                                        <td class="px-4 py-3 text-ink">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-ink">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-t border-line bg-primary/5">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-muted">Total: {{ $order->details->sum('quantity') }} porsi</td>
                                        <td class="px-4 py-3 text-right text-label uppercase text-muted">Grand Total</td>
                                        <td class="px-4 py-3 text-right text-body font-semibold text-primary">{{ $order->formattedTotal() }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-line pt-4">
                        <x-button type="button" variant="outline" @click="showInvoiceModal = false">Tutup Pratinjau</x-button>
                        <x-button type="button" id="btn-modal-print-now" @click="showInvoiceModal = false; window.print()">Cetak Sekarang</x-button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Print Media Stylesheet -->
    <style>
        
/* =====================================
   INVOICE FOODFLOW - PRINT STYLE
===================================== */

.invoice-sheet {
    width: 100%;
    max-width: 190mm;
    margin: 0 auto;
    padding: 0;
    color: #202938;
    background: #fff;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10pt;
}

/* HEADER */
.invoice-header {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 24px;
    background: #485d80;
    color: #fff;
}

.invoice-company h1 {
    margin: 0;
    font-size: 23pt;
    font-weight: 700;
    letter-spacing: .5px;
}

.invoice-company p {
    margin: 3px 0;
    font-size: 11pt;
    font-weight: 600;
}

.invoice-company span {
    display: block;
    margin-top: 14px;
    font-size: 9pt;
    color: #e5eaf2;
}

.invoice-title {
    min-width: 210px;
    text-align: right;
}

.invoice-title h2 {
    margin: 0;
    font-size: 31pt;
    font-weight: 700;
    line-height: 1.1;
}

.invoice-title > strong {
    display: block;
    margin: 5px 0 12px;
    font-size: 12pt;
}

.invoice-date {
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    font-size: 8.5pt;
}

.invoice-date span {
    color: #dce5f0;
}

/* GARIS AKSEN */
.invoice-accent {
    height: 9px;
    background: #b6d7df;
}

/* INFORMASI PELANGGAN */
.invoice-info {
    display: flex;
    justify-content: space-between;
    gap: 25px;
    padding: 25px 8px;
}

.invoice-label {
    display: block;
    margin-bottom: 7px;
    font-size: 8pt;
    font-weight: 700;
    color: #596579;
}

.invoice-customer h3 {
    margin: 0 0 6px;
    font-size: 15pt;
    font-weight: 700;
}

.invoice-customer p {
    margin: 3px 0;
    font-size: 9pt;
    line-height: 1.4;
}

.invoice-amount {
    min-width: 220px;
    text-align: right;
}

.invoice-amount strong {
    display: block;
    color: #485d80;
    font-size: 23pt;
    font-weight: 700;
}

.invoice-status {
    display: block;
    margin-top: 6px;
    color: #596579;
    font-size: 8.5pt;
}

/* TABEL PESANAN */
.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
    font-size: 9pt;
}

.invoice-table thead {
    background: #485d80;
    color: #fff;
}

.invoice-table th {
    padding: 10px;
    text-align: right;
    font-weight: 600;
    white-space: nowrap;
}

.invoice-table th:first-child {
    text-align: left;
}

.invoice-table td {
    padding: 12px 10px;
    text-align: right;
    vertical-align: top;
    border-bottom: 1px solid #e3e7ed;
}

.invoice-table td:first-child {
    text-align: left;
}

.invoice-table tbody tr:nth-child(even) {
    background: #f2f4f7;
}

.invoice-table td strong {
    display: block;
    font-size: 9.5pt;
}

.invoice-table td small {
    display: block;
    margin-top: 3px;
    color: #687386;
    font-size: 8pt;
}

.invoice-item-total {
    font-weight: 700;
}

.invoice-empty {
    text-align: center !important;
    color: #687386;
}

/* RINGKASAN TOTAL */
.invoice-bottom {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-top: 28px;
    padding: 18px 8px 0;
    border-top: 2px solid #485d80;
}

.invoice-notes {
    max-width: 105mm;
}

.invoice-notes strong {
    display: block;
    margin-bottom: 6px;
    font-size: 10pt;
}

.invoice-notes p {
    margin: 0;
    font-size: 8.5pt;
    line-height: 1.5;
    white-space: pre-line;
}

.invoice-total {
    width: 65mm;
    background: #485d80;
    color: #fff;
}

.invoice-total > div {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 9px 10px;
    font-size: 9pt;
}

.invoice-grand-total {
    border-top: 1px solid rgba(255,255,255,.4);
    font-weight: 700;
}

/* FOOTER */
.invoice-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-top: 40px;
    padding: 0 8px;
}

.invoice-thanks {
    text-align: right;
}

.invoice-thanks h3 {
    margin: 0;
    font-size: 15pt;
    font-weight: 700;
}

.invoice-thanks p {
    margin-top: 4px;
    font-size: 9pt;
    font-style: italic;
    color: #596579;
}

.invoice-signature {
    min-width: 55mm;
    text-align: center;
}

.invoice-signature span {
    display: block;
    font-size: 9pt;
}

.signature-space {
    height: 45px;
    border-bottom: 1px solid #485d80;
    margin-bottom: 6px;
}

.invoice-signature strong {
    font-size: 9pt;
}

/* PENGATURAN CETAK */
@page {
    size: A4 portrait;
    margin: 12mm;
}

@media print {

    html,
    body {
        width: 100% !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        color: #202938 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Sembunyikan seluruh halaman aplikasi */
    body * {
        visibility: hidden !important;
    }

    /* Tampilkan hanya invoice */
    .print-only,
    .print-only * {
        visibility: visible !important;
    }

    .print-only {
        display: block !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    .invoice-sheet {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .invoice-header,
    .invoice-table thead,
    .invoice-total {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .invoice-header,
    .invoice-info,
    .invoice-table tr,
    .invoice-bottom,
    .invoice-footer {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
    </style>
</x-app-layout>