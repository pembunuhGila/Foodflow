<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-400 mb-1.5" aria-label="Breadcrumb">
                    <a href="{{ route('products.index') }}" class="hover:text-purple-600 transition">Daftar Menu</a>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-purple-700 font-semibold">Detail Menu</span>
                </nav>

                {{-- Judul & Badge Status --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="font-bold text-xl sm:text-2xl text-gray-900 leading-tight">
                        {{ $product->name }}
                    </h2>
                    @if ($product->isActive())
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1 text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            {{-- Tombol Aksi Header --}}
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200/80 hover:bg-gray-50 text-gray-700 text-xs font-semibold uppercase tracking-wider rounded-xl transition shadow-2xs">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
                @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition shadow-sm shadow-purple-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Menu</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Detail Grid Bagian Atas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Card: Profil & Informasi Dasar Menu -->
                <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-bold text-purple-600 uppercase tracking-wider block mb-1">Identitas Hidangan</span>
                            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                                {{ $product->description ?: 'Tidak ada deskripsi atau catatan khusus untuk menu ini.' }}
                            </p>
                        </div>

                        {{-- Grid Harga Jual & Satuan Penyajian --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="p-4 bg-purple-50/50 rounded-xl border border-purple-100/50">
                                <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider block">Harga Jual Menu</span>
                                <div class="text-2xl font-extrabold text-gray-900 mt-1">{{ $product->formattedPrice() }}</div>
                                <span class="text-xs text-gray-500 mt-0.5 block">per {{ $product->unit ?? 'porsi' }}</span>
                            </div>
                            <div class="p-4 bg-purple-50/50 rounded-xl border border-purple-100/50">
                                <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider block">Satuan Penyajian</span>
                                <div class="text-2xl font-extrabold text-gray-900 mt-1 uppercase">{{ $product->unit ?? 'porsi' }}</div>
                                <span class="text-xs text-gray-500 mt-0.5 block">Standar takaran katering</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Metadata --}}
                    <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Ditambahkan pada: {{ $product->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Terakhir diubah: {{ $product->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Card: Nutrition Facts / Rekap Gizi (Anti-Slop Light Theme) -->
                <div class="bg-white rounded-2xl border border-purple-100/80 shadow-sm p-6 sm:p-7 flex flex-col justify-between">
                    <div>
                        {{-- Header Gizi --}}
                        <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-gray-100">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">REKAPITULASI NUTRISI</span>
                                <h4 class="text-base font-bold text-gray-900 mt-0.5">Informasi Nilai Gizi</h4>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-100">
                                Dikelola Ahli Gizi
                            </span>
                        </div>

                        {{-- Highlight Kalori Utama --}}
                        <div class="bg-amber-50/70 border border-amber-100/80 rounded-xl p-3.5 mb-4 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-medium text-amber-800 block">Total Energi Kalori</span>
                                <span class="text-[11px] text-amber-600/80">Per 1 {{ $product->unit ?? 'porsi' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-amber-600">
                                    {{ number_format((float) ($product->calories ?? 0), 0) }}
                                </span>
                                <span class="text-xs font-semibold text-amber-700 ml-0.5">kkal</span>
                            </div>
                        </div>

                        {{-- Daftar Makronutrisi dengan Soft Badges --}}
                        <div class="divide-y divide-gray-100 text-sm">
                            <div class="flex items-center justify-between py-2.5">
                                <span class="font-medium text-gray-700 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Protein
                                </span>
                                <span class="text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-md font-semibold text-xs border border-blue-100/70">
                                    {{ number_format((float) ($product->protein ?? 0), 1) }} g
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="font-medium text-gray-700 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    Lemak Total
                                </span>
                                <span class="text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-md font-semibold text-xs border border-rose-100/70">
                                    {{ number_format((float) ($product->fat ?? 0), 1) }} g
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="font-medium text-gray-700 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    Karbohidrat Total
                                </span>
                                <span class="text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-md font-semibold text-xs border border-purple-100/70">
                                    {{ number_format((float) ($product->carbohydrates ?? 0), 1) }} g
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="font-medium text-gray-600 flex items-center gap-2 pl-3 text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Serat Pangan
                                </span>
                                <span class="text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md font-semibold text-xs border border-emerald-100/70">
                                    {{ number_format((float) ($product->dietary_fiber ?? 0), 1) }} g
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Note --}}
                    <div class="text-[11px] text-gray-400 leading-relaxed italic border-t border-gray-100 pt-3 mt-4">
                        * Persentase nilai gizi dihitung berdasarkan kebutuhan energi harian standar katering gizi seimbang.
                    </div>
                </div>

            </div>

            <!-- Card Bagian Bawah: Komposisi Resep Bahan Baku -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Komposisi Resep Bahan Baku</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Rincian takaran bahan mentah yang diperlukan untuk memproduksi 1 {{ $product->unit ?? 'porsi' }} hidangan ini.
                        </p>
                    </div>

                    @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                        <a href="{{ route('products.recipe.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition shadow-sm shadow-purple-200 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Kelola Resep (Recipe Builder)</span>
                        </a>
                    @endif
                </div>

                @if ($product->recipeItems->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/60">
                                <tr>
                                    <th scope="col" class="w-12 px-5 py-3 text-center text-xs uppercase font-semibold tracking-wider text-gray-400">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-semibold tracking-wider text-gray-400">Nama Bahan Baku</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-semibold tracking-wider text-gray-400">Takaran per 1 {{ $product->unit ?? 'porsi' }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-semibold tracking-wider text-gray-400">Satuan Resep</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-semibold tracking-wider text-gray-400">Satuan Baku Gudang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($product->recipeItems as $index => $item)
                                    <tr class="hover:bg-purple-50/20 transition">
                                        <td class="px-5 py-3.5 text-center text-xs font-bold text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('ingredients.show', $item->ingredient) }}" class="font-semibold text-gray-900 hover:text-purple-700 transition">
                                                    {{ $item->ingredient->name }}
                                                </a>
                                                @if ($item->ingredient->category)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                                        {{ $item->ingredient->category }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-purple-50 text-purple-800 border border-purple-200/60">
                                                {{ rtrim(rtrim(number_format((float) $item->quantity_per_unit, 3, ',', '.'), '0'), ',') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap text-gray-700 font-medium">
                                            {{ $item->unit }}
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap text-gray-500 text-xs">
                                            {{ $item->ingredient->unit }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 text-xs text-gray-500 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span>Total: <strong class="text-gray-900">{{ $product->recipeItems->count() }}</strong> jenis bahan baku mentah terdaftar.</span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200/60">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Resep ini siap digunakan oleh kalkulator bahan produksi.
                        </span>
                    </div>
                @else
                    <div class="p-10 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-800">Menu ini belum memiliki komposisi resep bahan baku</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                            Tentukan takaran bahan baku mentah agar sistem dapat menghitung kebutuhan logistik secara otomatis pada rencana produksi.
                        </p>
                        @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                            <div class="mt-4">
                                <a href="{{ route('products.recipe.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition shadow-sm shadow-purple-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Susun Resep Sekarang</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
