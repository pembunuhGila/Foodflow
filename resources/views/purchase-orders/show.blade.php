<x-app-layout>
    @php
    $isAdmin = Auth::user()->isAdmin();
    @endphp

    {{-- Header: breadcrumb + nomor + status, tombol di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between print:hidden">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('purchase-orders.index') }}" class="text-muted transition hover:text-primary">Daftar Purchase Order</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Detail Purchase Order</span>
            </nav>

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-ink">{{ $order->order_number }}</h1>
                @if ($order->status)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                    {{ $order->status->label() }}
                </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('purchase-orders.index') }}"
                class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-input bg-primary px-4 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                Cetak Surat PO
            </button>
        </div>
    </div>

    <div class="space-y-6">

        @if (session('success'))
        <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary print:hidden" role="status">
            <x-icon name="check" class="h-5 w-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text print:hidden" role="alert">
            {{ session('error') }}
        </div>
        @endif

        <!-- Informasi PO -->
        <x-card class="w-full sm:p-8">
            <div class="flex flex-col gap-3 border-b border-line pb-4 md:flex-row md:items-center md:justify-between">
                <p class="text-small text-muted">
                    Dibuat pada
                    <span class="font-medium text-ink">{{ $order->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                </p>

                {{-- Ubah status (khusus admin) --}}
                @if ($isAdmin)
                <form method="POST" action="{{ route('purchase-orders.update-status', $order) }}"
                    class="flex items-center gap-3 print:hidden">
                    @csrf
                    @method('PATCH')
                    <label for="po-status" class="text-label uppercase text-muted">Ubah Status</label>
                    <select id="po-status" name="status" onchange="this.form.submit()"
                        class="rounded-input border border-line bg-white px-3 py-2 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        @foreach (\App\Enums\PurchaseOrderStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ $order->status === $st ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Nomor PO</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->order_number }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Vendor / Supplier</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->vendor?->name ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Tanggal Pesan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->order_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4">
                    <dt class="text-label uppercase text-muted">Tanggal Pengiriman Diharapkan</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->expected_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>

                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Dibuat Oleh</dt>
                    <dd class="mt-1.5 text-body font-medium text-ink">{{ $order->creator?->name ?? '-' }}</dd>
                </div>

                @if ($order->notes)
                <div class="rounded-input border border-line bg-primary/5 p-4 md:col-span-2">
                    <dt class="text-label uppercase text-muted">Catatan</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-body font-medium text-ink">{{ $order->notes }}</dd>
                </div>
                @endif
            </dl>
        </x-card>

        <!-- Rincian bahan baku -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="border-b border-line px-6 py-5">
                <h2 class="text-lg font-semibold text-ink">Rincian Bahan Baku yang Dipesan</h2>
                <p class="mt-0.5 text-small text-muted">Jumlah, satuan, harga, dan subtotal tiap bahan baku.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-3.5 text-center text-label uppercase text-muted">#</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Bahan Baku</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Jumlah</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Satuan</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Harga / Satuan</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($order->details as $i => $detail)
                        <tr class="transition hover:bg-primary/5">
                            <td class="px-6 py-4 text-center text-small text-muted">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-ink">{{ $detail->ingredient?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-ink">{{ number_format((float) $detail->quantity, 2, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $detail->unit }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-ink">Rp {{ number_format((float) $detail->price, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-ink">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-muted">Tidak ada detail bahan baku dalam PO ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if ($order->details->isNotEmpty())
                    <tfoot class="border-t border-line bg-primary/5">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right text-label uppercase text-muted">Grand Total Estimasi</td>
                            <td class="px-6 py-4 text-right text-lg font-semibold text-primary">Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </x-card>

    </div>
</x-app-layout>