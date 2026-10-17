<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        FR-14 & UC-07
                    </span>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Daftar Pesanan Katering') }}
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola data pemesanan katering, pantau jadwal kirim / acara pelanggan, dan rincian variasi menu makanan.
                </p>
            </div>
            @if (Auth::user()?->isAdmin())
                <div>
                    <a href="{{ route('catering-orders.create') }}"
                       id="btn-create-catering-order"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Pesanan Baru
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <!-- Filter & Pencarian -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('catering-orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Cari Pesanan / Pelanggan</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="No. ORD atau Nama Pelanggan..."
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Status Pesanan</label>
                        <select name="status"
                                id="status"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                            <option value="processing" {{ $currentStatus === 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="delivered" {{ $currentStatus === 'delivered' ? 'selected' : '' }}>Terkirim</option>
                            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label for="delivery_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Kirim / Acara</label>
                        <input type="date"
                               name="delivery_date"
                               id="delivery_date"
                               value="{{ $currentDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition shadow-2xs">
                            Filter
                        </button>
                        @if ($currentSearch || $currentStatus || $currentDate)
                            <a href="{{ route('catering-orders.index') }}"
                               class="px-3 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition"
                               title="Reset filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Pesanan -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/70 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5">Nomor Pesanan</th>
                                <th scope="col" class="px-6 py-3.5">Pelanggan</th>
                                <th scope="col" class="px-6 py-3.5">Tgl Pesan / Kirim</th>
                                <th scope="col" class="px-6 py-3.5">Rincian Menu</th>
                                <th scope="col" class="px-6 py-3.5">Total Tagihan</th>
                                <th scope="col" class="px-6 py-3.5">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-900">
                                        <a href="{{ route('catering-orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900 underline decoration-dotted">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $order->customer?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $order->customer?->phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="text-gray-500">Pesan: <span class="font-medium text-gray-700">{{ $order->order_date?->format('d/m/Y') }}</span></div>
                                        <div class="text-emerald-700 font-semibold mt-0.5">Kirim: {{ $order->delivery_date?->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $order->details_count }} Menu ({{ $order->details->sum('quantity') }} porsi)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ $order->formattedTotal() }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status_badge_class }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('catering-orders.show', $order) }}"
                                           class="inline-flex items-center text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="text-sm font-medium">Belum ada pesanan katering yang tercatat.</span>
                                            @if (Auth::user()?->isAdmin())
                                                <a href="{{ route('catering-orders.create') }}" class="text-xs text-emerald-600 hover:underline font-semibold mt-1">
                                                    + Buat Pesanan Katering Sekarang
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
