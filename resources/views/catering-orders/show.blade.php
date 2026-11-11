<x-app-layout>
    @php
    $isAdmin = Auth::user()?->isAdmin();
    $st = $order->status instanceof \BackedEnum ? $order->status->value : (string) $order->status;

    $statusBadge = [
    'pending' => ['bg-amber-50 text-amber-800', 'bg-amber-500'],
    'confirmed' => ['bg-primary/10 text-primary', 'bg-primary'],
    'processing' => ['bg-primary text-white', 'bg-white'],
    'delivered' => ['bg-gray-100 text-gray-600', 'bg-gray-400'],
    'cancelled' => ['bg-danger-bg text-danger-text', 'bg-danger-text'],
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

                {{-- Aksi (teks) --}}
                <div class="no-print flex flex-wrap items-center gap-x-4 gap-y-2 text-small font-medium">
                    <button type="button" @click="showInvoiceModal = true" id="btn-view-invoice-summary"
                        class="text-primary transition hover:text-primary-dark">Lihat Ringkasan Invoice</button>
                    <button type="button" onclick="window.print()" id="btn-print-invoice"
                        class="text-primary transition hover:text-primary-dark">Cetak Invoice</button>

                    @if ($isAdmin)
                    <a href="{{ route('catering-orders.edit', $order) }}" id="btn-edit-order"
                        class="text-primary transition hover:text-primary-dark">Edit Pesanan</a>

                    @if ($next)
                    <form id="next-order-form" method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next['status'] }}">
                        <button type="button" @click="$dispatch('open-confirm', @js($nextConfirm))"
                            class="text-primary transition hover:text-primary-dark">{{ $next['label'] }}</button>
                    </form>
                    @endif

                    @if ($st !== 'cancelled')
                    <form id="cancel-order-form" method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="button" @click="$dispatch('open-confirm', @js($cancelConfirm))" id="btn-cancel-order"
                            class="text-danger-text transition hover:underline">Batalkan Pesanan</button>
                    </form>
                    @else
                    <span class="text-muted">Pesanan telah dibatalkan</span>
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
        <div class="print-only hidden space-y-6 bg-white p-6">
            <div class="border-b-2 border-gray-800 pb-4 text-center">
                <h1 class="text-2xl font-black uppercase tracking-wider">FOODFLOW CATERING & LOGISTICS</h1>
                <p class="text-xs text-gray-600">Sistem Pengelolaan Katering Sehat & Rencana Produksi Makanan Bersertifikat</p>
                <p class="text-xs text-gray-500">Jl. Industri Pangan Sehat No. 12 &bull; Telp: (021) 555-FOODFLOW &bull; support@foodflow.local</p>
            </div>

            <div class="flex items-start justify-between text-xs">
                <div>
                    <div class="font-bold uppercase text-gray-600">Kepada Yth:</div>
                    <div class="text-base font-extrabold text-gray-900">{{ $order->customer?->name }}</div>
                    <div>PIC / Kontak: {{ $order->customer?->name }}</div>
                    <div>No. Telp / WA: {{ $order->customer?->phone }}</div>
                    <div class="mt-1 max-w-sm">Alamat Kirim: {{ $order->customer?->address }}</div>
                </div>
                <div class="text-right">
                    <div class="font-mono text-base font-extrabold">{{ $order->order_number }}</div>
                    <div>Tanggal Pesan: {{ $order->order_date?->format('d/m/Y') }}</div>
                    <div class="font-bold">Tanggal Kirim: {{ $order->delivery_date?->format('d/m/Y') }}</div>
                    <div>Status: {{ $order->status_label }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 pt-8 text-center text-xs">
                <div>
                    <p class="text-gray-600">Penerima / Pemesan,</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">{{ $order->customer?->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Hormat Kami, Petugas Katering</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">{{ $order->creator?->name ?? 'Admin FoodFlow' }}</p>
                </div>
            </div>
        </div>

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
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .no-print,
            header,
            nav,
            aside {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            #printable-order-details {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
        }
    </style>
</x-app-layout>