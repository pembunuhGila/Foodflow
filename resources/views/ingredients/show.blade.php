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

                    <div class="flex items-center gap-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div>
                            <span class="block text-xs uppercase text-gray-500 font-semibold tracking-wider">Satuan Baku</span>
                            <span class="text-lg font-bold text-gray-900 font-mono">{{ $ingredient->unit }}</span>
                        </div>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <div>
                            <span class="block text-xs uppercase text-gray-500 font-semibold tracking-wider">Batas Minimum</span>
                            <span class="text-lg font-bold text-amber-600">{{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</span>
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
