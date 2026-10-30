<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                    Menu &amp; Gizi
                </span>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ __('Master Menu Makanan & Rekap Gizi') }}
                </h2>
            </div>
            <p class="text-xs text-gray-500 mt-0.5">
                Kelola hidangan katering sehat, harga satuan, resep bahan baku, dan kandungan nutrisi per porsi.
            </p>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        showToggleModal: false,
        showDeleteModal: false,
        selectedProduct: {},
        toggleFormAction: '',
        deleteFormAction: '',
        isTogglingActive: false,

        confirmToggle(product, toggleUrl) {
            this.selectedProduct = product;
            this.toggleFormAction = toggleUrl;
            this.isTogglingActive = product.is_active;
            this.showToggleModal = true;
        },

        confirmDelete(product, deleteUrl) {
            this.selectedProduct = product;
            this.deleteFormAction = deleteUrl;
            this.showDeleteModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-xl shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter & Action Header Card -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 flex-1 items-end">
                        <div class="sm:col-span-6">
                            <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Cari Nama Menu / Bahan
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik nama hidangan katering..." class="w-full pl-10 text-sm border-gray-200 rounded-xl shadow-xs focus:border-purple-500 focus:ring-purple-500 bg-gray-50/50">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Status Menu
                            </label>
                            <select name="status" id="status" class="w-full text-sm border-gray-200 rounded-xl shadow-xs focus:border-purple-500 focus:ring-purple-500 bg-gray-50/50">
                                <option value="">Semua Status</option>
                                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Menu Aktif</option>
                                <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                            </select>
                        </div>

                        <div class="sm:col-span-3 flex items-center gap-2">
                            <a href="{{ route('products.index') }}" class="w-1/2 text-center px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                                Reset
                            </a>
                            <button type="submit" class="w-1/2 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-sm transition">
                                Filter
                            </button>
                        </div>
                    </form>

                    @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                        <div class="lg:border-l lg:border-gray-200 lg:pl-4 flex shrink-0">
                            <a href="{{ route('products.create') }}" class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white text-xs font-semibold uppercase tracking-wider rounded-xl shadow-sm shadow-purple-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Menu Baru</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200/80">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50/75">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Menu Masakan</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kandungan Gizi / Porsi</th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($products as $product)
                                @php
                                    $productPayload = [
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'is_active' => $product->isActive(),
                                    ];
                                @endphp
                                <tr class="hover:bg-purple-50/20 transition">
                                    {{-- Kolom Menu Masakan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-purple-100/70 text-purple-700 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <div>
                                                <a href="{{ route('products.show', $product) }}" class="font-bold text-gray-900 hover:text-purple-700 transition">
                                                    {{ $product->name }}
                                                </a>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if ($product->recipe_items_count > 0)
                                                        <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span>{{ $product->recipe_items_count }} bahan</span>
                                                        </a>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                            <span>Belum ada resep</span>
                                                        </span>
                                                    @endif

                                                    @if ($product->description)
                                                        <span class="text-xs text-gray-400 truncate max-w-xs" title="{{ $product->description }}">
                                                            {{ Str::limit($product->description, 35) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Harga Satuan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $product->formattedPrice() }}</div>
                                        <div class="text-xs text-gray-500">per {{ $product->unit ?? 'porsi' }}</div>
                                    </td>

                                    {{-- Kolom Kandungan Gizi --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/60" title="Kalori">
                                                🔥 {{ number_format($product->calories, 0) }} kkal
                                            </span>
                                            <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-800 border border-blue-200/60" title="Protein">
                                                P: {{ number_format($product->protein, 1) }}g
                                            </span>
                                            <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-rose-50 text-rose-800 border border-rose-200/60" title="Lemak">
                                                L: {{ number_format($product->fat, 1) }}g
                                            </span>
                                            <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-800 border border-purple-200/60" title="Karbohidrat">
                                                K: {{ number_format($product->carbohydrates, 1) }}g
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($product->isActive())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi (Dedicated Pages + Modal Konfirmasi Saja) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center gap-1.5 justify-center">
                                            {{-- 1. Dedicated Page: Detail (Ikon Mata) --}}
                                            <a href="{{ route('products.show', $product) }}" class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-xl border border-transparent hover:border-purple-200/60 transition shadow-2xs" title="Lihat Halaman Detail Menu">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                                                {{-- 2. Dedicated Page: Resep (Ikon Klip Dokumen / Recipe Builder) --}}
                                                <a href="{{ route('products.recipe.edit', $product) }}" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl border border-transparent hover:border-indigo-200/60 transition shadow-2xs" title="Buka Recipe Builder (Komposisi Bahan)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </a>

                                                {{-- 3. Dedicated Page: Edit (Ikon Pensil) --}}
                                                <a href="{{ route('products.edit', $product) }}" class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl border border-transparent hover:border-emerald-200/60 transition shadow-2xs" title="Edit Data Menu & Gizi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                {{-- 4. Modal Konfirmasi: Ubah Status (Ikon Tukar Status) --}}
                                                <button type="button" @click="confirmToggle({{ json_encode($productPayload) }}, '{{ route('products.toggle-status', $product) }}')" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl border border-transparent hover:border-amber-200/60 transition shadow-2xs" title="{{ $product->isActive() ? 'Nonaktifkan Menu' : 'Aktifkan Menu' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                    </svg>
                                                </button>

                                                {{-- 5. Modal Konfirmasi: Hapus Menu (Ikon Tempat Sampah) --}}
                                                <button type="button" @click="confirmDelete({{ json_encode($productPayload) }}, '{{ route('products.destroy', $product) }}')" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-xl border border-transparent hover:border-red-200/60 transition shadow-2xs" title="Hapus Menu">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900">Belum ada data menu makanan</p>
                                        <p class="text-xs text-gray-500 mt-1">Daftarkan menu katering sehat dengan klik tombol Tambah Menu Baru di atas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- MODAL KONFIRMASI STATUS (NONAKTIFKAN / AKTIFKAN)         --}}
        {{-- ======================================================== --}}
        <div x-show="showToggleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showToggleModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" @click="showToggleModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showToggleModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                    <form :action="toggleFormAction" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900" x-text="isTogglingActive ? 'Konfirmasi Nonaktifkan Menu' : 'Konfirmasi Aktifkan Menu'"></h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Perubahan status ketersediaan menu masakan katering.
                                    </p>
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 mt-4 leading-relaxed">
                                Apakah Anda yakin ingin <strong x-text="isTogglingActive ? 'menonaktifkan' : 'mengaktifkan kembali'"></strong> menu <strong class="text-gray-900" x-text="selectedProduct.name"></strong>?
                            </p>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2">
                            <button type="button" @click="showToggleModal = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-semibold uppercase tracking-wider rounded-xl hover:bg-gray-50 transition shadow-xs">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition shadow-xs" x-text="isTogglingActive ? 'Ya, Lanjutkan' : 'Ya, Aktifkan'">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL KONFIRMASI HAPUS (DANGER MODAL - REFACTORED)       --}}
        {{-- ======================================================== --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="showDeleteModal = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-gray-100 overflow-hidden">
                <form :action="deleteFormAction" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Icon Tong Sampah Melayang -->
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <!-- Judul & Teks Utama -->
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Hapus Menu Makanan</h3>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                        Apakah Anda yakin ingin menghapus menu <strong class="text-gray-900 font-semibold" x-text="selectedProduct.name"></strong>?
                    </p>

                    <!-- Alert Box Note Warning -->
                    <div class="bg-rose-50/50 border border-rose-100/80 rounded-2xl p-3.5 mb-6 text-left flex items-start gap-3">
                        <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-rose-800/80 leading-relaxed">
                            <strong>Catatan:</strong> Jika menu sudah pernah terikat pesanan katering, sistem akan mengarsipkannya untuk menjaga riwayat transaksi.
                        </p>
                    </div>

                    <!-- Footer Tombol Aksi Sejajar (2 Kolom) -->
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="showDeleteModal = false" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-rose-200 transition-all">
                            Ya, Hapus Menu
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
