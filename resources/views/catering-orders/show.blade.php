<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        FR-14 & UC-07
                    </span>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Rincian Pesanan Katering') }} &mdash; <span class="font-mono text-emerald-700">{{ $order->order_number }}</span>
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Informasi lengkap pesanan pelanggan, rincian menu hidangan, nomor invoice, dan jadwal pengiriman.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('catering-orders.index') }}"
                   id="btn-back-to-list"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
                @if (Auth::user()?->isAdmin())
                    <a href="{{ route('catering-orders.create') }}"
                       id="btn-create-another-order"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                        + Buat Pesanan Baru
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{
        showInvoiceModal: false,
        showCancelModal: false
    }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between no-print" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-sm flex items-center justify-between no-print" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Card 1: Action Bar & Header Pesanan -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-100 pb-4 gap-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs uppercase font-semibold tracking-wider text-gray-500">Nomor Invoice Pesanan</span>
                            <span class="px-2 py-0.5 text-2xs font-mono font-bold bg-gray-100 text-gray-600 rounded">RESMI</span>
                        </div>
                        <h1 class="text-2xl font-mono font-extrabold text-gray-900 mt-1" id="order-invoice-number">
                            {{ $order->order_number }}
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Dibuat pada {{ $order->created_at?->format('d F Y, H:i') ?? '-' }}
                        </p>
                    </div>

                    <!-- Status Badge & Action Controls -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center space-x-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                            <span class="text-xs text-gray-500 font-medium">Status:</span>
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold border {{ $order->status_badge_class }}" id="order-status-badge">
                                {{ $order->status_label }}
                            </span>
                        </div>

                        @if (Auth::user()?->isAdmin())
                            <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-flex items-center gap-1.5 no-print" id="form-update-status-quick">
                                @csrf
                                @method('PATCH')
                                <select name="status"
                                        id="select-quick-status"
                                        onchange="this.form.submit()"
                                        title="Ubah status langsung"
                                        class="text-xs font-medium rounded-lg border-gray-300 py-1.5 pl-2.5 pr-7 text-gray-700 bg-white hover:border-emerald-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs transition">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Tombol Aksi Utama (Print/Ringkasan Invoice, Edit Pesanan, Batalkan Pesanan) --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 no-print">
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- 1. Cetak / Lihat Ringkasan Invoice --}}
                        <button type="button"
                                @click="showInvoiceModal = true"
                                id="btn-view-invoice-summary"
                                class="inline-flex items-center px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg border border-emerald-200 transition shadow-2xs">
                            <svg class="w-4 h-4 me-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat Ringkasan Invoice
                        </button>

                        <button type="button"
                                onclick="window.print()"
                                id="btn-print-invoice"
                                class="inline-flex items-center px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg border border-gray-200 transition shadow-2xs">
                            <svg class="w-4 h-4 me-1.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Invoice
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if (Auth::user()?->isAdmin())
                            {{-- 2. Edit Pesanan --}}
                            <a href="{{ route('catering-orders.edit', $order) }}"
                               id="btn-edit-order"
                               class="inline-flex items-center px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg border border-blue-200 transition shadow-2xs">
                                <svg class="w-4 h-4 me-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Pesanan
                            </a>

                            {{-- 3. Batalkan Pesanan --}}
                            @if ($order->status !== 'cancelled')
                                <button type="button"
                                        @click="showCancelModal = true"
                                        id="btn-cancel-order"
                                        class="inline-flex items-center px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg border border-red-200 transition shadow-2xs">
                                    <svg class="w-4 h-4 me-1.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Batalkan Pesanan
                                </button>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 rounded-lg border border-red-200 cursor-not-allowed">
                                    <svg class="w-4 h-4 me-1.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    Pesanan Telah Dibatalkan
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Grid Data Pelanggan & Jadwal Pengiriman -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Kolom Kiri: Data Lengkap Pelanggan & PIC -->
                    <div class="p-5 rounded-xl bg-gray-50/80 border border-gray-200/90 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-200/70 pb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Data Lengkap Pelanggan</span>
                            </h4>
                            <span class="text-2xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                {{ $order->customer?->is_active ? 'Pelanggan Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="space-y-2.5 text-sm">
                            <div>
                                <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">Nama Pelanggan / Institusi</span>
                                <div class="font-bold text-gray-900 text-base" id="customer-name">
                                    {{ $order->customer?->name ?? '-' }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">PIC / Kontak</span>
                                    <div class="font-semibold text-gray-800" id="customer-pic">
                                        {{ $order->customer?->name ?? '-' }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">No. Telepon / WhatsApp</span>
                                    <div class="font-semibold text-gray-900" id="customer-phone">
                                        {{ $order->customer?->phone ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            @if ($order->customer?->email)
                                <div class="pt-1">
                                    <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">Alamat Email</span>
                                    <div class="font-medium text-gray-700 text-xs" id="customer-email">
                                        {{ $order->customer->email }}
                                    </div>
                                </div>
                            @endif

                            <div class="pt-1 border-t border-gray-200/60">
                                <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">Alamat Pengiriman Lengkap</span>
                                <p class="text-gray-800 text-xs mt-1 whitespace-pre-line leading-relaxed font-normal" id="customer-address">
                                    {{ $order->customer?->address ?? 'Belum ada alamat pengiriman terdaftar' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Jadwal Pengiriman & Info Pemesanan -->
                    <div class="p-5 rounded-xl bg-gray-50/80 border border-gray-200/90 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-200/70 pb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Jadwal Pengiriman & Petugas</span>
                            </h4>
                            <span class="text-2xs font-mono font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                                JADWAL
                            </span>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-lg">
                                <span class="text-2xs font-bold uppercase tracking-wider text-emerald-700 block">Tanggal Pengiriman Pesanan</span>
                                <div class="text-base font-extrabold text-emerald-900 mt-0.5" id="order-delivery-date">
                                    {{ $order->delivery_date?->format('d F Y') ?? '-' }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">Tanggal Pemesanan</span>
                                    <div class="font-semibold text-gray-800" id="order-date">
                                        {{ $order->order_date?->format('d F Y') ?? '-' }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-2xs font-bold uppercase tracking-wider text-gray-400 block">Petugas Input (Admin)</span>
                                    <div class="font-semibold text-gray-800" id="order-creator">
                                        {{ $order->creator?->name ?? 'Admin' }}
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-200/60 text-2xs text-gray-500 flex items-center justify-between">
                                <span>Terdaftar: {{ $order->created_at?->format('d/m/Y H:i') }}</span>
                                <span>Pembaruan: {{ $order->updated_at?->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box Catatan Khusus Pesanan -->
                <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-xl text-xs space-y-1">
                    <div class="flex items-center space-x-1.5 text-amber-900 font-bold uppercase tracking-wider">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>Catatan Khusus Pesanan / Instruksi Khusus:</span>
                    </div>
                    <p class="text-amber-800 whitespace-pre-line leading-relaxed ps-5" id="order-special-notes">
                        {{ $order->notes ?: 'Tidak ada catatan khusus untuk pesanan katering ini.' }}
                    </p>
                </div>
            </div>

            <!-- Card 2: Tabel Rincian Menu Hidangan yang Dipesan -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" id="printable-order-details">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Tabel Rincian Menu Hidangan yang Dipesan</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Rincian kuantitas porsi, harga satuan katering, dan subtotal masing-masing hidangan.
                        </p>
                    </div>
                    <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Total {{ $order->details->count() }} Jenis Menu
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600" id="table-order-items">
                        <thead class="bg-gray-50/80 text-xs uppercase font-semibold text-gray-600 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 w-12 text-center">#</th>
                                <th scope="col" class="px-6 py-3.5">Menu Hidangan</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Jumlah Porsi</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($order->details as $index => $detail)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="px-6 py-4 text-xs text-gray-400 text-center font-mono">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 item-name">{{ $detail->product?->name ?? 'Produk #' . $detail->product_id }}</div>
                                        @if ($detail->notes)
                                            <div class="text-xs text-emerald-700 mt-0.5 italic item-notes">
                                                &bull; Catatan Khusus: {{ $detail->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-800 item-quantity">
                                        {{ $detail->quantity }} {{ $detail->product?->unit ?? 'porsi' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-700 item-unit-price">
                                        Rp {{ number_format((float) $detail->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-extrabold text-gray-900 item-subtotal">
                                        Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                        Belum ada rincian item menu untuk pesanan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-emerald-50/50 border-t-2 border-emerald-200">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Total Akumulasi Porsi
                                </td>
                                <td class="px-6 py-4 text-center font-extrabold text-emerald-800 text-base" id="total-portions">
                                    {{ $order->details->sum('quantity') }} Porsi
                                </td>
                                <td class="px-6 py-4 text-right text-xs uppercase font-bold text-emerald-800">
                                    Total Tagihan (Grand Total):
                                </td>
                                <td class="px-6 py-4 text-right text-xl font-extrabold text-emerald-700" id="order-grand-total">
                                    {{ $order->formattedTotal() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Print View Kop & Signature (Khusus Tampil Saat window.print) -->
            <div class="print-only hidden p-6 bg-white space-y-6">
                <div class="border-b-2 border-gray-800 pb-4 text-center">
                    <h1 class="text-2xl font-black uppercase tracking-wider">FOODFLOW CATERING & LOGISTICS</h1>
                    <p class="text-xs text-gray-600">Sistem Pengelolaan Katering Sehat & Rencana Produksi Makanan Bersertifikat</p>
                    <p class="text-xs text-gray-500">Jl. Industri Pangan Sehat No. 12 &bull; Telp: (021) 555-FOODFLOW &bull; support@foodflow.local</p>
                </div>

                <div class="flex justify-between items-start text-xs">
                    <div>
                        <div class="font-bold uppercase text-gray-600">Kepada Yth:</div>
                        <div class="font-extrabold text-base text-gray-900">{{ $order->customer?->name }}</div>
                        <div>PIC / Kontak: {{ $order->customer?->name }}</div>
                        <div>No. Telp / WA: {{ $order->customer?->phone }}</div>
                        <div class="max-w-sm mt-1">Alamat Kirim: {{ $order->customer?->address }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-extrabold font-mono">{{ $order->order_number }}</div>
                        <div>Tanggal Pesan: {{ $order->order_date?->format('d/m/Y') }}</div>
                        <div class="font-bold text-emerald-800">Tanggal Kirim: {{ $order->delivery_date?->format('d/m/Y') }}</div>
                        <div>Status: {{ $order->status_label }}</div>
                    </div>
                </div>

                <div class="pt-8 grid grid-cols-2 text-center text-xs">
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

        </div>

        <!-- Modal 1: Ringkasan Invoice Resmi (Alpine.js) -->
        <div x-show="showInvoiceModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-invoice-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showInvoiceModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showInvoiceModal = false"
                     class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                     aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showInvoiceModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center space-x-2">
                            <span class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="modal-invoice-title">
                                    Ringkasan Invoice Pesanan Katering
                                </h3>
                                <p class="text-xs text-gray-500">Pratinjau tagihan resmi katering FoodFlow</p>
                            </div>
                        </div>
                        <button type="button"
                                @click="showInvoiceModal = false"
                                class="text-gray-400 hover:text-gray-500 p-1.5 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body: Invoice Paper Style -->
                    <div class="py-5 space-y-5 text-sm">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <span class="text-2xs font-bold uppercase text-gray-400 block">Nomor Invoice</span>
                                <span class="text-lg font-mono font-extrabold text-emerald-800">{{ $order->order_number }}</span>
                            </div>
                            <div class="sm:text-right">
                                <span class="text-2xs font-bold uppercase text-gray-400 block">Tanggal Pengiriman</span>
                                <span class="text-sm font-bold text-gray-800">{{ $order->delivery_date?->format('d F Y') }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3 bg-white border border-gray-200 rounded-lg space-y-1">
                                <span class="font-bold uppercase text-gray-400 text-2xs block">Informasi Pelanggan & PIC</span>
                                <div class="font-bold text-sm text-gray-900">{{ $order->customer?->name }}</div>
                                <div>PIC: {{ $order->customer?->name }}</div>
                                <div>Telp / WA: {{ $order->customer?->phone ?? '-' }}</div>
                                <div class="text-gray-600">Alamat: {{ $order->customer?->address ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-white border border-gray-200 rounded-lg space-y-1">
                                <span class="font-bold uppercase text-gray-400 text-2xs block">Status & Petugas</span>
                                <div>Status: <span class="font-bold text-emerald-700">{{ $order->status_label }}</span></div>
                                <div>Petugas Input: {{ $order->creator?->name ?? 'Admin' }}</div>
                                <div>Tgl Pesan: {{ $order->order_date?->format('d F Y') }}</div>
                                @if ($order->notes)
                                    <div class="text-amber-800 italic">Catatan: {{ $order->notes }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-100 text-gray-700 font-bold uppercase">
                                    <tr>
                                        <th class="px-4 py-2.5">Menu</th>
                                        <th class="px-4 py-2.5 text-center">Porsi</th>
                                        <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($order->details as $item)
                                        <tr>
                                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $item->product?->name ?? 'Produk #' . $item->product_id }}</td>
                                            <td class="px-4 py-2.5 text-center font-bold">{{ $item->quantity }}</td>
                                            <td class="px-4 py-2.5 text-right">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2.5 text-right font-bold text-gray-900">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-emerald-50/70 border-t border-emerald-200 font-bold">
                                    <tr>
                                        <td colspan="2" class="px-4 py-2.5 text-gray-700">Total: {{ $order->details->sum('quantity') }} Porsi</td>
                                        <td class="px-4 py-2.5 text-right text-gray-700 uppercase">Grand Total:</td>
                                        <td class="px-4 py-2.5 text-right text-base text-emerald-800 font-extrabold">{{ $order->formattedTotal() }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end space-x-2 border-t border-gray-100 pt-4">
                        <button type="button"
                                @click="showInvoiceModal = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Tutup Pratinjau
                        </button>
                        <button type="button"
                                @click="showInvoiceModal = false; window.print()"
                                id="btn-modal-print-now"
                                class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                            Cetak Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()?->isAdmin())
            <!-- Modal 2: Konfirmasi Pembatalan Pesanan (Alpine.js) -->
            <div x-show="showCancelModal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-cancel-title"
                 role="dialog"
                 aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showCancelModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="showCancelModal = false"
                         class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showCancelModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">

                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full shrink-0 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900" id="modal-cancel-title">
                                    Batalkan Pesanan Katering?
                                </h3>
                                <p class="text-xs text-gray-500">Nomor Pesanan: {{ $order->order_number }}</p>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-gray-600 leading-relaxed">
                            Apakah Anda yakin ingin membatalkan pesanan katering ini? Status pesanan akan diubah menjadi <strong class="text-red-700">Dibatalkan (cancelled)</strong>. Anda dapat mengaktifkannya kembali melalui dropdown status jika diperlukan.
                        </div>

                        <div class="mt-5 flex items-center justify-end space-x-2">
                            <button type="button"
                                    @click="showCancelModal = false"
                                    class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                Tutup
                            </button>
                            <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" id="form-confirm-cancel-order">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        id="btn-confirm-cancel-order"
                                        class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition">
                                    Ya, Batalkan Pesanan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- Print Media Stylesheet -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print, header, nav, #btn-back-to-list, #btn-create-another-order, #form-update-status-quick {
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
