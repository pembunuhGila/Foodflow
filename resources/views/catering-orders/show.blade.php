<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
                    Informasi lengkap pesanan pelanggan, rincian menu hidangan, dan jadwal pengiriman.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('catering-orders.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
                @if (Auth::user()?->isAdmin())
                    <a href="{{ route('catering-orders.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                        + Buat Pesanan Lain
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Card 1: Ringkasan Pesanan & Pelanggan -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4 gap-3">
                    <div>
                        <span class="text-xs text-gray-400 uppercase tracking-wider block">Nomor Pesanan</span>
                        <span class="text-xl font-mono font-extrabold text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-500">Status Pesanan:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_class }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        @if (Auth::user()?->isAdmin())
                            <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <select name="status"
                                        onchange="this.form.submit()"
                                        class="text-xs font-medium rounded-lg border-gray-300 py-1 pl-2 pr-7 text-gray-700 bg-white hover:border-emerald-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs transition">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kolom Kiri: Informasi Pelanggan -->
                    <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-200/80 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Data Pelanggan</span>
                        </h4>
                        <div class="space-y-1 text-sm">
                            <div class="font-semibold text-gray-900">{{ $order->customer?->name ?? '-' }}</div>
                            <div class="text-xs text-gray-600 flex items-center space-x-1">
                                <span>No. Telp / WhatsApp:</span>
                                <span class="font-medium text-gray-900">{{ $order->customer?->phone ?? '-' }}</span>
                            </div>
                            @if ($order->customer?->email)
                                <div class="text-xs text-gray-600">Email: {{ $order->customer->email }}</div>
                            @endif
                            <div class="text-xs text-gray-600 pt-1">
                                <span class="font-medium text-gray-700">Alamat Pengiriman:</span>
                                <p class="text-gray-900 mt-0.5 whitespace-pre-line">{{ $order->customer?->address ?? 'Belum ada alamat terdaftar' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Waktu & Tanggal Jadwal -->
                    <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-200/80 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Jadwal & Petugas Input</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Pemesanan:</span>
                                <span class="font-semibold text-gray-800">{{ $order->order_date?->format('d F Y') }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-emerald-700 font-semibold block">Tanggal Kirim / Acara:</span>
                                <span class="font-bold text-emerald-800">{{ $order->delivery_date?->format('d F Y') }}</span>
                            </div>
                            <div class="col-span-2 pt-1 border-t border-gray-200/60 text-xs text-gray-500">
                                Diinput oleh: <span class="font-medium text-gray-700">{{ $order->creator?->name ?? 'Admin' }}</span>
                                ({{ $order->created_at?->format('d/m/Y H:i') }})
                            </div>
                        </div>
                    </div>
                </div>

                @if ($order->notes)
                    <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-lg text-xs">
                        <span class="font-bold text-amber-900 block mb-0.5">Catatan Pesanan / Instruksi Khusus:</span>
                        <p class="text-amber-800 whitespace-pre-line">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Card 2: Rincian Menu Makanan & Porsi -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Daftar Menu Hidangan yang Dipesan</span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/70 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">#</th>
                                <th scope="col" class="px-6 py-3">Menu Hidangan</th>
                                <th scope="col" class="px-6 py-3 text-center">Jumlah Porsi</th>
                                <th scope="col" class="px-6 py-3 text-right">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($order->details as $index => $detail)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4 text-xs text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $detail->product?->name ?? 'Produk #' . $detail->product_id }}</div>
                                        @if ($detail->notes)
                                            <div class="text-xs text-emerald-700 mt-0.5 italic">&bull; Catatan: {{ $detail->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-800">
                                        {{ $detail->quantity }} {{ $detail->product?->unit ?? 'porsi' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-700">
                                        Rp {{ number_format((float) $detail->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                                        Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-emerald-50/40 border-t-2 border-emerald-200">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm font-bold text-gray-800 uppercase tracking-wider">
                                    Total Pesanan
                                </td>
                                <td class="px-6 py-4 text-center font-extrabold text-emerald-800">
                                    {{ $order->details->sum('quantity') }} Porsi
                                </td>
                                <td class="px-6 py-4 text-right text-xs uppercase font-semibold text-emerald-800">
                                    Grand Total:
                                </td>
                                <td class="px-6 py-4 text-right text-xl font-extrabold text-emerald-700">
                                    {{ $order->formattedTotal() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
