<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Daftar Purchase Order') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola semua purchase order pengadaan bahan baku ke vendor.
                </p>
            </div>
            @if (Auth::user()->isAdmin())
                <a href="{{ route('purchase-orders.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-semibold uppercase tracking-widest rounded-lg hover:bg-blue-700 transition shadow-sm">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat PO Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
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
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-medium text-gray-600 mb-1">Cari Nomor PO</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="Cari nomor PO..."
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    <div class="w-full sm:w-44">
                        <label for="status" class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" id="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ $currentStatus === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-48">
                        <label for="order_date" class="block text-xs font-medium text-gray-600 mb-1">Tanggal Pesan</label>
                        <input type="date"
                               name="order_date"
                               id="order_date"
                               value="{{ $currentDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Filter
                        </button>
                        <a href="{{ route('purchase-orders.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                @if ($orders->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada purchase order yang ditemukan.</p>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('purchase-orders.create') }}"
                               class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                Buat PO pertama Anda &rarr;
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor PO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vendor</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Pesan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Kirim</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jml Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat Oleh</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-800">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $order->vendor?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $order->order_date?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $order->expected_date?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-center">
                                            {{ $order->details_count }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($order->status)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $order->status->badgeClass() }}">
                                                    {{ $order->status->label() }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $order->creator?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('purchase-orders.show', $order) }}"
                                               class="inline-flex items-center text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($orders->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
