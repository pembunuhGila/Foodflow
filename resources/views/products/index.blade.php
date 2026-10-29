<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-blue-900 leading-tight">
                    {{ __('Master Menu Makanan & Rekap Gizi') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data menu masakan katering sehat beserta harga jual dan informasi kandungan nutrisi/gizi per porsi (UC-23 & FR-11, FR-12).</p>
            </div>
            @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Menu Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
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

            @if (session('warning'))
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Cari Nama Menu / Deskripsi</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik nama menu masakan, lauk, sup..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status Ketersediaan</label>
                        <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Semua Status --</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Menu Aktif</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition">Reset Filter</a>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white bg-gray-800 hover:bg-gray-700 rounded-md transition">Terapkan Filter</button>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Menu Masakan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga per Satuan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kandungan Gizi (Porsi)</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 4h.01"/></svg></div>
                                                
                                            </div>
                                            <div>
                                                <a href="{{ route('products.show', $product) }}" class="font-medium text-gray-900 hover:text-emerald-600">
                                                    {{ $product->name }}
                                                </a>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    @if ($product->recipe_items_count > 0)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            {{ $product->recipe_items_count }} bahan
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                            ⚠️ Belum ada resep
                                                        </span>
                                                    @endif
                                                    @if ($product->description)
                                                        <span class="text-xs text-gray-400 truncate max-w-xs" title="{{ $product->description }}">{{ Str::limit($product->description, 35) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $product->formattedPrice() }}</div>
                                        <div class="text-xs text-gray-500">per {{ $product->unit ?? 'porsi' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800" title="Kalori">
                                                🔥 {{ number_format($product->calories, 0) }} kkal
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" title="Protein">
                                                P: {{ number_format($product->protein, 1) }}g
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800" title="Lemak">
                                                L: {{ number_format($product->fat, 1) }}g
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200" title="Karbohidrat">
                                                K: {{ number_format($product->carbohydrates, 1) }}g
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($product->isActive())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center gap-2">
    <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800 font-medium">Detail</a>
    @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
        <a href="{{ route('products.recipe.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Resep</a>
        <a href="{{ route('products.edit', $product) }}" class="text-emerald-600 hover:text-emerald-800 font-medium">Edit</a>
        <form action="{{ route('products.toggle-status', $product) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-amber-600 hover:text-amber-800 font-medium" onclick="return confirm('Apakah Anda yakin ingin mengubah status menu ini?')">
                {{ $product->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline ml-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini? Jika menu pernah dipesan, sistem akan menonaktifkannya.')">
                Hapus
            </button>
        </form>
    @endif
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium text-gray-900">Belum ada data menu makanan</p>
                                        <p class="mt-1 text-xs text-gray-500">Mulai daftarkan menu katering sehat dengan klik tombol di kanan atas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
