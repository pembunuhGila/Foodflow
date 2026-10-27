<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Master Data Bahan Baku') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data bahan makanan mentah, satuan unit, dan batas minimum stok persediaan.</p>
            </div>
            @if (in_array(auth()->user()->role?->value, ['admin', 'gudang'], true))
                <a href="{{ route('ingredients.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Bahan Baku Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Session Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Critical Stock Alert Banner (BR-06) -->
            @if (($criticalCount ?? 0) > 0)
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 text-amber-700 rounded-md flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-900">Peringatan Batas Minimum Stok</h4>
                            <p class="text-xs text-amber-800 mt-0.5">
                                Terdapat <strong>{{ $criticalCount }}</strong> bahan baku yang berada pada batas kritis atau di bawah stok minimum.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if (($filters['stock_status'] ?? '') === 'low_stock')
                            <a href="{{ route('ingredients.index') }}" class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition shadow-sm">
                                Tampilkan Semua Bahan
                            </a>
                        @else
                            <a href="{{ route('ingredients.index', ['stock_status' => 'low_stock']) }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-amber-600 rounded hover:bg-amber-700 transition shadow-sm">
                                Lihat {{ $criticalCount }} Bahan Kritis
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('ingredients.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Cari Nama / Catatan</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik nama bahan atau kata kunci..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <!-- Filter Kategori -->
                    <div>
                        <label for="category" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Kategori</label>
                        <select name="category" id="category" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Ketersediaan -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status Penggunaan</label>
                        <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Semua Status --</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif Digunakan</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- Filter Kontrol Stok (BR-06) -->
                    <div>
                        <label for="stock_status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Kondisi Stok</label>
                        <select name="stock_status" id="stock_status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Semua Kondisi Stok --</option>
                            <option value="low_stock" {{ ($filters['stock_status'] ?? '') === 'low_stock' ? 'selected' : '' }}>Kritis / Menipis (&le; Batas Min)</option>
                            <option value="out_of_stock" {{ ($filters['stock_status'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Stok Habis (0)</option>
                            <option value="sufficient" {{ ($filters['stock_status'] ?? '') === 'sufficient' ? 'selected' : '' }}>Stok Aman (&gt; Batas Min)</option>
                        </select>
                    </div>

                    <!-- Tombol Filter -->
                    <div class="sm:col-span-2 md:col-span-4 flex items-center justify-end gap-2 pt-2">
                        @if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['status']) || !empty($filters['stock_status']))
                            <a href="{{ route('ingredients.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md shadow-sm transition">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ingredients Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Nama Bahan Baku</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Kategori</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Satuan</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Stok Saat Ini</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Batas Min</th>
                                <th scope="col" class="px-6 py-3.5 text-center font-semibold text-gray-700">Peringatan Stok</th>
                                <th scope="col" class="px-6 py-3.5 text-center font-semibold text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($ingredients as $item)
                                <tr class="hover:bg-gray-50/75 transition {{ $item->isLowStock() ? 'bg-amber-50/20' : '' }}">
                                    <!-- Nama Bahan -->
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                        @if ($item->notes)
                                            <div class="text-xs text-gray-500 truncate max-w-sm mt-0.5" title="{{ $item->notes }}">{{ $item->notes }}</div>
                                        @endif
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $item->category }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Umum</span>
                                        @endif
                                    </td>

                                    <!-- Satuan -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-xs font-semibold px-2.5 py-1 bg-gray-100 rounded text-gray-800">
                                            {{ $item->unit }}
                                        </span>
                                    </td>

                                    <!-- Stok Saat Ini -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if ($item->isOutOfStock())
                                            <span class="font-bold text-red-600 font-mono">0.00</span>
                                        @elseif ($item->isLowStock())
                                            <span class="font-bold text-amber-600 font-mono">{{ number_format($item->current_stock, 2) }}</span>
                                        @else
                                            <span class="font-medium text-gray-900 font-mono">{{ number_format($item->current_stock, 2) }}</span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $item->unit }}</span>
                                    </td>

                                    <!-- Batas Minimum Stok -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="font-semibold text-gray-900 font-mono">{{ number_format($item->minimum_stock, 2) }}</span>
                                        <span class="text-xs text-gray-500">{{ $item->unit }}</span>
                                    </td>

                                    <!-- Indikator Visual / Badge Alert (BR-06) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($item->isOutOfStock())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                                <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                Stok Habis
                                            </span>
                                        @elseif ($item->isLowStock())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Stok Menipis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Stok Aman
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($item->isActive())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <!-- Lihat Detail (Semua Role) -->
                                        <a href="{{ route('ingredients.show', $item) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900 transition">
                                            Detail
                                        </a>

                                        @if (in_array(auth()->user()->role?->value, ['admin', 'gudang'], true))
                                            <!-- Edit -->
                                            <a href="{{ route('ingredients.edit', $item) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 transition">
                                                Edit
                                            </a>

                                            <!-- Toggle Status (BR-02: Soft Toggle) -->
                                            <form method="POST" action="{{ route('ingredients.toggle-status', $item) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status bahan baku ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs {{ $item->isActive() ? 'text-amber-600 hover:text-amber-900' : 'text-emerald-600 hover:text-emerald-900' }} transition">
                                                    {{ $item->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            <p class="font-medium">Tidak ada data bahan baku yang ditemukan.</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($ingredients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $ingredients->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
