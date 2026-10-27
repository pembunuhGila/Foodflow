<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Evaluasi Penggunaan Bahan Baku Riil Dapur') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1 font-mono">
                    Rencana Produksi: {{ $plan->plan_code }} &mdash; Tanggal Masak: {{ $plan->plan_date?->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center space-x-2">
                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                    <a href="{{ route('production-plans.material-usages.create', $plan) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Ubah / Input Pemakaian
                    </a>
                @endif
                <a href="{{ route('production-plans.show', $plan) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest rounded-lg transition shadow-sm">
                    &larr; Kembali ke Rencana
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Perbandingan Kebutuhan Resep vs Pemakaian Riil (FR-35)</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Selisih positif (+) menunjukkan pemakaian lebih boros dari resep, selisih negatif (-) menunjukkan hemat/sisa.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bahan Baku</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Takaran Standar Resep</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemakaian Riil</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Selisih (Efisiensi)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan Dapur</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dicatat Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($usages as $index => $usage)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-800">
                                        {{ $usage->ingredient?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right font-mono">
                                        {{ number_format((float) $usage->planned_quantity, 3, ',', '.') }} {{ $usage->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right font-mono">
                                        {{ number_format((float) $usage->actual_quantity, 3, ',', '.') }} {{ $usage->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $diff = (float) $usage->difference_quantity;
                                        @endphp
                                        @if ($diff > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                +{{ number_format($diff, 3, ',', '.') }} {{ $usage->unit }} (Boros/Susut)
                                            </span>
                                        @elseif ($diff < 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                {{ number_format($diff, 3, ',', '.') }} {{ $usage->unit }} (Sisa/Hemat)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                Sesuai Resep (0)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        {{ $usage->notes ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ $usage->recorder?->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <p class="text-sm text-gray-500">Belum ada pencatatan pemakaian bahan riil untuk jadwal masak ini.</p>
                                        @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                            <a href="{{ route('production-plans.material-usages.create', $plan) }}"
                                               class="mt-3 inline-flex items-center text-xs font-medium text-blue-600 hover:text-blue-800">
                                                Input pemakaian bahan riil sekarang &rarr;
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
