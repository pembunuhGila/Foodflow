 @php
    $criticalIngredients = $criticalIngredients ?? \App\Models\Ingredient::active()->lowStock()->orderBy('current_stock')->get();
    $criticalCount = $criticalCount ?? $criticalIngredients->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Staff Gudang / Inventory') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                Role: {{ Auth::user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Widget Peringatan Batas Minimum Stok (BR-06) -->
            @if ($criticalCount > 0)
                <div class="bg-gradient-to-r from-red-50 via-amber-50 to-orange-50 border-l-4 border-red-500 rounded-lg shadow-sm p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-amber-200">
                        <div class="flex items-start gap-3">
                            <div class="p-2.5 bg-red-100 text-red-600 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-bold text-gray-900">Peringatan Batas Minimum Stok (BR-06)</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white shadow-sm">
                                        {{ $criticalCount }} Bahan Butuh Restock
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mt-1">
                                    Terdapat <strong>{{ $criticalCount }}</strong> bahan baku yang stoknya menyentuh atau berada di bawah batas minimum persediaan. Segera lakukan pengadaan atau koordinasi restock.
                                </p>
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            <a href="{{ route('ingredients.index', ['stock_status' => 'low_stock']) }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md uppercase tracking-wider shadow-sm transition">
                                Kelola Bahan Kritis &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Ringkasan Tabel Bahan Kritis -->
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-amber-200 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <th class="py-2.5 pr-4">Bahan Baku</th>
                                    <th class="py-2.5 px-4">Kategori</th>
                                    <th class="py-2.5 px-4 text-right">Stok Saat Ini</th>
                                    <th class="py-2.5 px-4 text-right">Batas Minimum</th>
                                    <th class="py-2.5 px-4 text-center">Status</th>
                                    <th class="py-2.5 pl-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100 font-medium">
                                @foreach ($criticalIngredients->take(5) as $criticalItem)
                                    <tr class="hover:bg-amber-100/40 transition">
                                        <td class="py-3 pr-4 text-gray-900 font-semibold">
                                            {{ $criticalItem->name }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 text-xs">
                                            {{ $criticalItem->category ?? 'Umum' }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono">
                                            @if ($criticalItem->isOutOfStock())
                                                <span class="text-red-700 font-bold">0.00 {{ $criticalItem->unit }}</span>
                                            @else
                                                <span class="text-amber-700 font-bold">{{ number_format($criticalItem->current_stock, 2) }} {{ $criticalItem->unit }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono text-gray-600">
                                            {{ number_format($criticalItem->minimum_stock, 2) }} {{ $criticalItem->unit }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($criticalItem->isOutOfStock())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                                                    Stok Habis
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    Stok Menipis
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 pl-4 text-right">
                                            <a href="{{ route('ingredients.show', $criticalItem) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($criticalCount > 5)
                            <div class="pt-3 text-right">
                                <a href="{{ route('ingredients.index', ['stock_status' => 'low_stock']) }}" class="text-xs font-semibold text-amber-800 hover:text-amber-900 underline">
                                    + {{ $criticalCount - 5 }} bahan kritis lainnya...
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-emerald-900">Batas Minimum Stok Terkendali (BR-06)</h4>
                            <p class="text-xs text-emerald-700 mt-0.5">Semua persediaan bahan baku aktif saat ini berada dalam kondisi aman di atas batas minimum.</p>
                        </div>
                    </div>
                    <a href="{{ route('ingredients.index') }}" class="text-xs font-semibold text-emerald-800 hover:text-emerald-950 underline flex-shrink-0">
                        Lihat Seluruh Bahan
                    </a>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600">Anda login sebagai <strong>Staff Gudang / Inventory</strong>. Sesuai PRD FoodFlow, Anda memiliki hak akses untuk:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="border border-amber-200 rounded-lg p-4 bg-amber-50/50">
                        <div class="font-semibold text-amber-900 mb-1">Penerimaan & Pemeriksaan</div>
                        <p class="text-sm text-gray-600">Catat penerimaan dan periksa kondisi bahan (Baik, Rusak, Kedaluwarsa, Tidak Sesuai).</p>
                    </div>
                    <div class="border border-amber-200 rounded-lg p-4 bg-amber-50/50">
                        <div class="font-semibold text-amber-900 mb-1">Stok & Transaksi</div>
                        <p class="text-sm text-gray-600">Catat transaksi stok masuk (IN), stok keluar (OUT), dan penyesuaian (ADJUSTMENT).</p>
                    </div>
                    <a href="{{ route('material-requests.index') }}" class="border border-amber-200 rounded-lg p-4 bg-amber-50/50 hover:bg-amber-100/80 transition block group">
                        <div class="flex items-center justify-between mb-1">
                            <div class="font-semibold text-amber-900 group-hover:text-amber-950">Verifikasi Permintaan</div>
                            <span class="text-amber-700 text-xs font-bold group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </div>
                        <p class="text-sm text-gray-600">Periksa dan verifikasi permintaan bahan dari bagian produksi serta catat stok keluar.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

