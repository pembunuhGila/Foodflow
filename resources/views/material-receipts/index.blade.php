<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Penerimaan Bahan Baku Supplier') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Pencatatan barang tiba dari supplier, verifikasi kondisi fisik (Baik/Rusak), dan mutasi stok masuk otomatis.
                </p>
            </div>
            @if (in_array(Auth::user()->role?->value, ['admin', 'gudang'], true))
                <a href="{{ route('material-receipts.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-xs font-semibold uppercase tracking-widest rounded-lg hover:bg-emerald-700 transition shadow-sm">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Penerimaan Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Filter & Search --}}
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('material-receipts.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-medium text-gray-600 mb-1">Cari No. Penerimaan / No. PO</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="Cari RC-2026... atau PO-2026..."
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>
                    <div class="w-full sm:w-48">
                        <label for="date" class="block text-xs font-medium text-gray-600 mb-1">Tanggal Terima</label>
                        <input type="date"
                               name="date"
                               id="date"
                               value="{{ $currentDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                            Filter
                        </button>
                        <a href="{{ route('material-receipts.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                @if ($receipts->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada data penerimaan bahan yang dicatat.</p>
                        @if (in_array(Auth::user()->role?->value, ['admin', 'gudang'], true))
                            <a href="{{ route('material-receipts.create') }}"
                               class="mt-4 inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-800">
                                Catat penerimaan bahan pertama &rarr;
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Tanda Terima</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Terima</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Referensi PO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jml Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($receipts as $receipt)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-800">
                                            {{ $receipt->receipt_number }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $receipt->receipt_date?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if ($receipt->purchaseOrder)
                                                <a href="{{ route('purchase-orders.show', $receipt->purchaseOrder) }}" class="font-mono text-blue-600 hover:underline">
                                                    {{ $receipt->purchaseOrder->order_number }}
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Penerimaan Langsung</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $receipt->purchaseOrder?->vendor?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-medium text-gray-700">
                                            {{ $receipt->details_count }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $receipt->receiver?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('material-receipts.show', $receipt) }}"
                                               class="inline-flex items-center text-xs font-medium text-emerald-600 hover:text-emerald-800 transition">
                                                Rincian &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($receipts->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $receipts->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
