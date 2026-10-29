<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $product->name }}
                    </h2>
                    @if ($product->isActive())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            Nonaktif
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Detail spesifikasi hidangan catering, harga satuan, dan komposisi nilai gizi per porsi.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-md transition shadow-sm">
                    Kembali ke Daftar
                </a>
                @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-widest rounded-md transition shadow-sm">
                        Edit Menu
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Left Card: Profil & Informasi Menu -->
                <div class="md:col-span-2 bg-white p-6 sm:p-8 rounded-lg shadow-sm border border-gray-200 space-y-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                            {{ $product->description ?: 'Tidak ada deskripsi atau catatan khusus untuk menu ini.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-500 uppercase tracking-wider block font-medium">Harga Jual Menu</span>
                            <span class="text-xl font-bold text-gray-900 mt-1 block">{{ $product->formattedPrice() }}</span>
                            <span class="text-xs text-gray-500">per {{ $product->unit ?? 'porsi' }}</span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-500 uppercase tracking-wider block font-medium">Satuan Penyajian</span>
                            <span class="text-xl font-bold text-gray-900 mt-1 block uppercase">{{ $product->unit ?? 'porsi' }}</span>
                            <span class="text-xs text-gray-500">Standar takaran</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Ditambahkan pada: {{ $product->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                        <span>Terakhir diubah: {{ $product->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                    </div>
                </div>

                <!-- Right Card: Nutrition Facts Card -->
                <div class="bg-gradient-to-br from-emerald-900 via-teal-900 to-gray-900 text-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <div class="border-b border-white/20 pb-3 mb-4">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-300">Dikelola Ahli Gizi / Produksi</span>
                            <h4 class="text-lg font-black uppercase tracking-tight text-white mt-0.5">Informasi Nilai Gizi</h4>
                            <p class="text-xs text-white/70">Per 1 {{ $product->unit ?? 'porsi' }} hidangan</p>
                        </div>

                        <!-- Kalori Utama -->
                        <div class="flex items-baseline justify-between border-b-4 border-white/30 py-2">
                            <div>
                                <span class="text-xs uppercase font-semibold text-white/80">Jumlah Energi</span>
                                <div class="text-2xl font-black text-amber-300">Kalori</div>
                            </div>
                            <div class="text-3xl font-black text-amber-300">
                                {{ number_format($product->calories, 0) }} <span class="text-sm font-normal text-white/80">kkal</span>
                            </div>
                        </div>

                        <!-- Makronutrisi -->
                        <div class="divide-y divide-white/10 text-sm mt-3">
                            <div class="flex justify-between py-2.5">
                                <span class="font-medium text-white/90"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>Protein</span>
                                <span class="font-bold text-white">{{ number_format($product->protein, 1) }} g</span>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <span class="font-medium text-white/90"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>Lemak Total</span>
                                <span class="font-bold text-white">{{ number_format($product->fat, 1) }} g</span>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <span class="font-medium text-white/90"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>Karbohidrat Total</span>
                                <span class="font-bold text-white">{{ number_format($product->carbohydrates, 1) }} g</span>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <span class="font-medium text-white/80 pl-4 text-xs"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>Serat Pangan</span>
                                <span class="font-medium text-white/90 text-xs">{{ number_format($product->dietary_fiber, 1) }} g</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-white/20 text-[11px] text-white/60">
                        * Persentase nilai gizi dihitung berdasarkan kebutuhan energi harian standar katering gizi seimbang.
                    </div>
                </div>

            </div>

            <!-- Section 3: Komposisi Resep Bahan Baku (Recipe Builder - FR-08) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg></span> Komposisi Resep Bahan Baku
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Rincian takaran bahan baku mentah untuk menghasilkan 1 {{ $product->unit ?? 'porsi' }} menu ini.
                        </p>
                    </div>

                    @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                        <a href="{{ route('products.recipe.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-wider rounded-md transition shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Kelola Resep (Recipe Builder)
                        </a>
                    @endif
                </div>

                @if ($product->recipeItems->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="w-12 px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Bahan Baku</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Takaran per 1 {{ $product->unit ?? 'porsi' }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan Resep</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan Baku Gudang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($product->recipeItems as $index => $item)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-center text-xs font-bold text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <a href="{{ route('ingredients.show', $item->ingredient) }}" class="font-semibold text-gray-900 hover:text-emerald-600">
                                                {{ $item->ingredient->name }}
                                            </a>
                                            @if ($item->ingredient->category)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600">
                                                    {{ $item->ingredient->category }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap font-bold text-emerald-700">
                                            {{ rtrim(rtrim(number_format((float) $item->quantity_per_unit, 3, ',', '.'), '0'), ',') }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-700 font-medium">
                                            {{ $item->unit }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-500 text-xs">
                                            {{ $item->ingredient->unit }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 flex items-center justify-between">
                        <span>Total: <strong>{{ $product->recipeItems->count() }}</strong> jenis bahan baku mentah terdaftar.</span>
                        <span class="text-emerald-700 font-medium">Resep ini siap digunakan oleh kalkulator bahan produksi.</span>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <span class="text-4xl block mb-2">🧂</span>
                        <h4 class="text-base font-semibold text-gray-800">Menu ini belum memiliki komposisi resep bahan baku</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                            Tentukan takaran bahan baku mentah agar sistem dapat menghitung kebutuhan logistik secara otomatis pada rencana produksi.
                        </p>
                        @if (in_array(auth()->user()->role?->value, ['admin', 'produksi'], true))
                            <div class="mt-4">
                                <a href="{{ route('products.recipe.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-wider rounded-md transition shadow-sm">
                                    + Susun Resep Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
