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
                    Kelola data pesanan katering pelanggan, pantau jadwal kirim/acara, total porsi, nominal transaksi, dan kelola status pesanan secara terpadu.
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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Statistik Status Cepat -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="{{ route('catering-orders.index') }}"
                   class="p-3 bg-white rounded-xl border border-gray-100 shadow-2xs hover:border-gray-300 transition text-center {{ empty($currentStatus) ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
                    <span class="text-xs text-gray-500 font-medium block">Semua Pesanan</span>
                    <span class="text-lg font-extrabold text-gray-800">{{ $statusCounts['all'] ?? 0 }}</span>
                </a>
                <a href="{{ route('catering-orders.index', ['status' => 'pending']) }}"
                   class="p-3 bg-amber-50/60 rounded-xl border border-amber-200 shadow-2xs hover:bg-amber-100/60 transition text-center {{ $currentStatus === 'pending' ? 'ring-2 ring-amber-500 font-bold' : '' }}">
                    <span class="text-xs text-amber-700 font-semibold block">Pending</span>
                    <span class="text-lg font-extrabold text-amber-900">{{ $statusCounts['pending'] ?? 0 }}</span>
                </a>
                <a href="{{ route('catering-orders.index', ['status' => 'confirmed']) }}"
                   class="p-3 bg-blue-50/60 rounded-xl border border-blue-200 shadow-2xs hover:bg-blue-100/60 transition text-center {{ $currentStatus === 'confirmed' ? 'ring-2 ring-blue-500 font-bold' : '' }}">
                    <span class="text-xs text-blue-700 font-semibold block">Dikonfirmasi</span>
                    <span class="text-lg font-extrabold text-blue-900">{{ $statusCounts['confirmed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('catering-orders.index', ['status' => 'processing']) }}"
                   class="p-3 bg-purple-50/60 rounded-xl border border-purple-200 shadow-2xs hover:bg-purple-100/60 transition text-center {{ $currentStatus === 'processing' ? 'ring-2 ring-purple-500 font-bold' : '' }}">
                    <span class="text-xs text-purple-700 font-semibold block">Diproses</span>
                    <span class="text-lg font-extrabold text-purple-900">{{ $statusCounts['processing'] ?? 0 }}</span>
                </a>
                <a href="{{ route('catering-orders.index', ['status' => 'delivered']) }}"
                   class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-200 shadow-2xs hover:bg-emerald-100/60 transition text-center {{ $currentStatus === 'delivered' ? 'ring-2 ring-emerald-500 font-bold' : '' }}">
                    <span class="text-xs text-emerald-700 font-semibold block">Selesai</span>
                    <span class="text-lg font-extrabold text-emerald-900">{{ $statusCounts['delivered'] ?? 0 }}</span>
                </a>
                <a href="{{ route('catering-orders.index', ['status' => 'cancelled']) }}"
                   class="p-3 bg-red-50/60 rounded-xl border border-red-200 shadow-2xs hover:bg-red-100/60 transition text-center {{ $currentStatus === 'cancelled' ? 'ring-2 ring-red-500 font-bold' : '' }}">
                    <span class="text-xs text-red-700 font-semibold block">Dibatalkan</span>
                    <span class="text-lg font-extrabold text-red-900">{{ $statusCounts['cancelled'] ?? 0 }}</span>
                </a>
            </div>

            <!-- Filter & Pencarian Lengkap -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('catering-orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    {{-- 1. Cari Pesanan / Pelanggan --}}
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Cari Pesanan / Pelanggan
                        </label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="No. ORD atau Nama Pelanggan..."
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    {{-- 2. Status Pesanan --}}
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Status Pesanan
                        </label>
                        <select name="status"
                                id="status"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="processing" {{ $currentStatus === 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="delivered" {{ $currentStatus === 'delivered' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    {{-- 3. Rentang Tanggal Kirim: Dari --}}
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Tanggal Kirim Dari
                        </label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               value="{{ $startDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    {{-- 4. Rentang Tanggal Kirim: Sampai --}}
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Tanggal Kirim Sampai
                        </label>
                        <input type="date"
                               name="end_date"
                               id="end_date"
                               value="{{ $endDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    {{-- 5. Tombol Aksi Filter & Reset --}}
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                id="btn-filter"
                                class="flex-1 px-4 py-2.5 bg-emerald-700 text-white text-sm font-semibold rounded-lg hover:bg-emerald-800 transition shadow-2xs">
                            Filter
                        </button>
                        @if ($currentSearch || $currentStatus || $startDate || $endDate || $currentDate)
                            <a href="{{ route('catering-orders.index') }}"
                               id="btn-reset-filter"
                               class="px-3 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center"
                               title="Reset filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Pesanan Katering -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600" id="table-catering-orders">
                        <thead class="bg-gray-50/70 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3.5">Nomor Pesanan</th>
                                <th scope="col" class="px-6 py-3.5">Pelanggan</th>
                                <th scope="col" class="px-6 py-3.5">Tanggal Kirim</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Total Porsi</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Total Nominal</th>
                                <th scope="col" class="px-6 py-3.5">Status Pesanan</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition" id="order-row-{{ $order->id }}">
                                    {{-- 1. Nomor Pesanan --}}
                                    <td class="px-6 py-4 font-mono font-bold text-gray-900 whitespace-nowrap">
                                        <a href="{{ route('catering-orders.show', $order) }}"
                                           class="text-emerald-700 hover:text-emerald-900 underline decoration-dotted font-semibold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>

                                    {{-- 2. Nama Pelanggan --}}
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $order->customer?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $order->customer?->phone ?? '-' }}</div>
                                    </td>

                                    {{-- 3. Tanggal Kirim --}}
                                    <td class="px-6 py-4 text-xs whitespace-nowrap">
                                        <div class="font-bold text-emerald-800">
                                            {{ $order->delivery_date?->format('d/m/Y') }}
                                        </div>
                                        <div class="text-gray-400 mt-0.5">
                                            Pesan: {{ $order->order_date?->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    {{-- 4. Total Porsi --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $order->total_portions }} Porsi
                                        </span>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ $order->details_count }} Menu
                                        </div>
                                    </td>

                                    {{-- 5. Total Nominal --}}
                                    <td class="px-6 py-4 text-right font-extrabold text-gray-900 whitespace-nowrap">
                                        {{ $order->formattedTotal() }}
                                    </td>

                                    {{-- 6. Badge Status Pesanan & Aksi Cepat (Khusus Admin) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-1.5">
                                            <div>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_class }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </div>

                                            {{-- Aksi Cepat Perubahan Status Khusus Role Admin (Kelola Penuh) --}}
                                            @if (Auth::user()?->isAdmin())
                                                <div class="flex items-center gap-1.5 pt-1">
                                                    {{-- Dropdown Status Cepat --}}
                                                    <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline-block">
                                                        @csrf
                                                        @method('PATCH')
                                                        <label for="status-select-{{ $order->id }}" class="sr-only">Pilih Status</label>
                                                        <select id="status-select-{{ $order->id }}"
                                                                name="status"
                                                                onchange="this.form.submit()"
                                                                class="text-xs font-medium rounded-md border-gray-300 py-0.5 pl-2 pr-6 text-gray-700 bg-white hover:border-emerald-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs transition">
                                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai</option>
                                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                                        </select>
                                                    </form>

                                                    {{-- Tombol Langsung Cepat 1-Klik Sesuai Alur --}}
                                                    @if ($order->status === 'pending')
                                                        <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="confirmed">
                                                            <button type="submit"
                                                                    class="px-2 py-0.5 text-2xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded shadow-2xs transition"
                                                                    title="Konfirmasi Pesanan">
                                                                Konfirmasi
                                                            </button>
                                                        </form>
                                                    @elseif ($order->status === 'confirmed')
                                                        <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="processing">
                                                            <button type="submit"
                                                                    class="px-2 py-0.5 text-2xs font-bold text-white bg-purple-600 hover:bg-purple-700 rounded shadow-2xs transition"
                                                                    title="Mulai Proses Masak">
                                                                Proses
                                                            </button>
                                                        </form>
                                                    @elseif ($order->status === 'processing')
                                                        <form method="POST" action="{{ route('catering-orders.update-status', $order) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="delivered">
                                                            <button type="submit"
                                                                    class="px-2 py-0.5 text-2xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded shadow-2xs transition"
                                                                    title="Selesaikan Pesanan">
                                                                Selesai
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 7. Aksi Detail --}}
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('catering-orders.show', $order) }}"
                                           class="inline-flex items-center px-3 py-1 bg-gray-50 hover:bg-emerald-50 text-emerald-700 border border-gray-200 hover:border-emerald-300 rounded-lg text-xs font-semibold transition shadow-2xs">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-600">Tidak ada pesanan katering yang sesuai kriteria pencarian/filter.</span>
                                            @if ($currentSearch || $currentStatus || $startDate || $endDate || $currentDate)
                                                <a href="{{ route('catering-orders.index') }}" class="text-xs text-emerald-600 hover:underline font-semibold">
                                                    Reset Filter
                                                </a>
                                            @elseif (Auth::user()?->isAdmin())
                                                <a href="{{ route('catering-orders.create') }}" class="text-xs text-emerald-600 hover:underline font-semibold mt-1">
                                                    + Buat Pesanan Katering Baru
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
