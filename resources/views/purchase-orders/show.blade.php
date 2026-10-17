<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Purchase Order') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1 font-mono">
                    {{ $order->order_number }}
                </p>
            </div>
            <div>
                <a href="{{ route('purchase-orders.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Card Header Info --}}
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Informasi Purchase Order</span>
                    </h3>
                    @if ($order->status)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $order->status->badgeClass() }}">
                            {{ $order->status->label() }}
                        </span>
                    @endif
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor PO</dt>
                        <dd class="mt-1 font-mono font-semibold text-gray-800">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor / Supplier</dt>
                        <dd class="mt-1 text-gray-800">{{ $order->vendor?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pesan</dt>
                        <dd class="mt-1 text-gray-800">{{ $order->order_date?->translatedFormat('d F Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengiriman Diharapkan</dt>
                        <dd class="mt-1 text-gray-800">{{ $order->expected_date?->translatedFormat('d F Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</dt>
                        <dd class="mt-1 text-gray-800">{{ $order->creator?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Pada</dt>
                        <dd class="mt-1 text-gray-600">{{ $order->created_at?->translatedFormat('d F Y, H:i') ?? '—' }}</dd>
                    </div>
                    @if ($order->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</dt>
                            <dd class="mt-1 text-gray-700 bg-gray-50 rounded-lg p-3 text-sm leading-relaxed">{{ $order->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Card Rincian Bahan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900">Rincian Bahan Baku yang Dipesan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bahan Baku</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga/Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($order->details as $i => $detail)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                        {{ $detail->ingredient?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                        {{ number_format((float) $detail->quantity, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $detail->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                        Rp {{ number_format((float) $detail->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-700 text-right">
                                        Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                                        Tidak ada detail bahan baku dalam PO ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($order->details->isNotEmpty())
                            <tfoot>
                                <tr class="bg-blue-50 border-t-2 border-blue-200">
                                    <td colspan="5" class="px-4 py-3 text-sm font-bold text-blue-800 text-right uppercase tracking-wider">
                                        Grand Total Estimasi
                                    </td>
                                    <td class="px-4 py-3 text-base font-extrabold text-blue-700 text-right">
                                        Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
