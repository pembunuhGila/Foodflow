<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Bahan Baku') }}: {{ $ingredient->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Rincian spesifikasi, satuan baku, batas minimum stok, dan status ketersediaan (UC-04).</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ingredients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md uppercase tracking-wider transition">
                    &larr; Kembali
                </a>
                @if (in_array(auth()->user()->role?->value, ['admin', 'gudang'], true))
                    <a href="{{ route('ingredients.edit', $ingredient) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md uppercase tracking-wider shadow-sm transition">
                        Edit Bahan Baku
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Peringatan Batas Minimum Stok (BR-06) --}}
            @if ($ingredient->isOutOfStock())
                <div class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-5 shadow-sm flex items-start gap-3">
                    <div class="p-2 bg-red-100 text-red-600 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-red-900">Peringatan: Stok Bahan Baku Habis! (BR-06)</h4>
                        <p class="text-sm text-red-700 mt-1">
                            Persediaan saat ini adalah <strong>0.00 {{ $ingredient->unit }}</strong>, sementara batas stok minimum yang ditetapkan adalah <strong>{{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</strong>. Segera lakukan pengadaan atau pemesanan ke supplier.
                        </p>
                    </div>
                </div>
            @elseif ($ingredient->isLowStock())
                <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-lg p-5 shadow-sm flex items-start gap-3">
                    <div class="p-2 bg-amber-100 text-amber-700 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-amber-900">Peringatan Batas Minimum Stok (BR-06)</h4>
                        <p class="text-sm text-amber-800 mt-1">
                            Persediaan saat ini (<strong>{{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}</strong>) berada di bawah atau menyentuh batas minimum stok (<strong>{{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</strong>). Bahan baku ini membutuhkan restock segera sebesar minimal <strong>{{ number_format($ingredient->getStockDeficit(), 2) }} {{ $ingredient->unit }}</strong>.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Card Ringkasan Utama -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-100 gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $ingredient->name }}</h3>
                            @if ($ingredient->isActive())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-2 h-2 rounded-full bg-green-600"></span>
                                    Aktif Digunakan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    Dinonaktifkan
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Kategori: <strong class="text-gray-700">{{ $ingredient->category ?? 'Umum / Belum Ditentukan' }}</strong> &bull;
                            Terdaftar sejak: {{ $ingredient->created_at?->format('d F Y, H:i') ?? '-' }}
                        </p>
                    </div>

                    <!-- Panel Info Stok & Satuan -->
                    <div class="flex items-center gap-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div>
                            <span class="block text-xs uppercase text-gray-500 font-semibold tracking-wider">Satuan Baku</span>
                            <span class="text-lg font-bold text-gray-900 font-mono">{{ $ingredient->unit }}</span>
                        </div>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <div>
                            <span class="block text-xs uppercase text-gray-500 font-semibold tracking-wider">Stok Saat Ini</span>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold font-mono {{ $ingredient->isOutOfStock() ? 'text-red-600' : ($ingredient->isLowStock() ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}
                                </span>
                            </div>
                        </div>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <div>
                            <span class="block text-xs uppercase text-gray-500 font-semibold tracking-wider">Batas Minimum</span>
                            <span class="text-lg font-bold text-gray-900 font-mono">{{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</span>
                        </div>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <div>
                            <span class="block text-xs uppercase text-gray-500 font-semibold tracking-wider">Kondisi</span>
                            @if ($ingredient->isOutOfStock())
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                                    Stok Habis
                                </span>
                            @elseif ($ingredient->isLowStock())
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Stok Aman
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Catatan & Spesifikasi -->
                <div class="pt-6">
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-2">Catatan Spesifikasi & Penanganan</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100 leading-relaxed">
                        {{ $ingredient->notes ?: 'Tidak ada catatan spesifikasi khusus untuk bahan baku ini.' }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
