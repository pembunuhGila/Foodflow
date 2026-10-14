<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Master Data Bahan Baku') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data bahan makanan mentah, satuan unit, dan batas minimum stok persediaan (UC-04).</p>
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

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('ingredients.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
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

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status Ketersediaan</label>
                        <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Semua Status --</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif Digunakan</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Tidak Aktif (Dinonaktifkan)</option>
                        </select>
                    </div>

                    <!-- Tombol Filter -->
                    <div class="md:col-span-4 flex items-center justify-end gap-2 pt-2">
                        @if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['status']))
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
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Satuan Baku</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Stok Minimum</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($ingredients as $item)
                                <tr class="hover:bg-gray-50/75 transition">
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

                                    <!-- Batas Minimum Stok -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900">{{ number_format($item->minimum_stock, 2) }}</span>
                                        <span class="text-xs text-gray-500">{{ $item->unit }}</span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
