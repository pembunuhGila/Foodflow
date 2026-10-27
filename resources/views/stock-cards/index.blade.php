<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buku Kartu Mutasi Stok Bahan Baku') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Histori lengkap seluruh mutasi stok bahan (Masuk Penerimaan, Keluar Dapur, dan Penyesuaian Fisik).
                </p>
            </div>
            @if (in_array(Auth::user()->role?->value, ['admin', 'gudang'], true))
                <a href="{{ route('stock-adjustments.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Penyesuaian Fisik (Adjustment)
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Filter Form --}}
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('stock-cards.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <div>
                        <label for="ingredient_id" class="block text-xs font-medium text-gray-600 mb-1">Bahan Baku</label>
                        <select name="ingredient_id" id="ingredient_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-200">
                            <option value="">Semua Bahan Baku</option>
                            @foreach ($ingredients as $ing)
                                <option value="{{ $ing->id }}" {{ $selectedIngredientId == $ing->id ? 'selected' : '' }}>
                                    {{ $ing->name }} (Saldo: {{ (float) ($ing->current_stock ?? 0) }} {{ $ing->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="transaction_type" class="block text-xs font-medium text-gray-600 mb-1">Jenis Mutasi</label>
                        <select name="transaction_type" id="transaction_type" class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-200">
                            <option value="">Semua Jenis</option>
                            <option value="in" {{ $selectedType === 'in' ? 'selected' : '' }}>Stok Masuk (IN)</option>
                            <option value="out" {{ $selectedType === 'out' ? 'selected' : '' }}>Stok Keluar (OUT)</option>
                            <option value="adjustment" {{ $selectedType === 'adjustment' ? 'selected' : '' }}>Penyesuaian Fisik (Adjustment)</option>
                        </select>
                    </div>

                    <div>
                        <label for="from_date" class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                               class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-200">
                    </div>

                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label for="to_date" class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                            <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                                   class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-200">
                        </div>
                        <div class="flex items-end gap-1.5">
                            <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-xs font-semibold rounded-lg hover:bg-amber-700 transition">
                                Filter
                            </button>
                            <a href="{{ route('stock-cards.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Ringkasan Saldo Bahan jika spesifik dipilih --}}
            @if ($selectedIngredient)
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Kartu Stok Terpilih:</span>
                        <h3 class="text-lg font-bold text-gray-900">{{ $selectedIngredient->name }}</h3>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">Saldo Fisik Saat Ini</span>
                        <p class="text-2xl font-extrabold text-amber-900">
                            {{ number_format((float) $selectedIngredient->current_stock, 2, ',', '.') }}
                            <span class="text-sm font-semibold text-amber-700">{{ $selectedIngredient->unit }}</span>
                        </p>
                    </div>
                </div>
            @endif

            {{-- Table Kartu Stok --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                @if ($transactions->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada riwayat mutasi stok yang sesuai dengan filter.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bahan Baku</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Mutasi</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk (IN)</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar (OUT)</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Adjustment</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan & Referensi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Operator</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($transactions as $tx)
                                    @php
                                        $type = strtolower($tx->transaction_type);
                                        $qty = (float) $tx->quantity;
                                        $unit = $tx->ingredient?->unit ?? '';
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-4 py-3 text-sm font-mono text-gray-700 whitespace-nowrap">
                                            {{ $tx->transaction_date?->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                            {{ $tx->ingredient?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($type === 'in')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                    MASUK (IN)
                                                </span>
                                            @elseif ($type === 'out')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                                    KELUAR (OUT)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                    ADJUSTMENT
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-mono font-semibold text-emerald-700">
                                            {{ $type === 'in' ? '+' . number_format($qty, 2, ',', '.') . ' ' . $unit : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-mono font-semibold text-rose-700">
                                            {{ $type === 'out' ? '-' . number_format($qty, 2, ',', '.') . ' ' . $unit : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-mono font-semibold {{ $qty >= 0 ? 'text-blue-700' : 'text-amber-700' }}">
                                            @if ($type === 'adjustment')
                                                {{ ($qty >= 0 ? '+' : '') . number_format($qty, 2, ',', '.') . ' ' . $unit }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs">
                                            {{ $tx->notes ?: '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $tx->creator?->name ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($transactions->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
