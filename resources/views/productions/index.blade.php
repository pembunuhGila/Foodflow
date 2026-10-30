<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pencatatan Realisasi Masak Dapur') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Catat jumlah porsi hidangan yang berhasil dimasak per menu pada rencana produksi yang sedang berjalan (In Progress).
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('production-plans.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 me-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Semua Rencana Produksi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-800 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Status Tab -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Sedang Dimasak (Aktif)</span>
                            <div class="text-3xl font-extrabold text-amber-900 mt-1">{{ $inProgressCount }}</div>
                            <p class="text-xs text-amber-700/80 mt-1">Rencana produksi yang siap dicatat realisasi porsinya.</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-800 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Panduan Operasional</span>
                            <p class="text-sm font-medium text-gray-800 mt-1">Aturan Status Selesai Dapur</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Rencana otomatis berubah ke <span class="font-semibold text-emerald-700">Selesai</span> hanya saat <strong class="text-gray-700">seluruh menu</strong> telah mencapai/melampaui target porsi.
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Pencarian -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('productions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Cari No. Rencana</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="Contoh: PLN-2026..."
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Status</label>
                        <select name="status"
                                id="status"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                            <option value="in_progress" {{ $currentStatus === 'in_progress' ? 'selected' : '' }}>Sedang Dimasak (In Progress)</option>
                            <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>Semua Status Terkait</option>
                        </select>
                    </div>

                    <div>
                        <label for="production_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Masak</label>
                        <input type="date"
                               name="production_date"
                               id="production_date"
                               value="{{ $currentDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 transition shadow-sm">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                        @if ($currentSearch || $currentStatus !== 'in_progress' || $currentDate)
                            <a href="{{ route('productions.index') }}"
                               class="inline-flex justify-center items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Rencana Produksi -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 divide-y divide-gray-100">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50/80 font-bold tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-4">No. Rencana</th>
                                <th scope="col" class="px-6 py-4">Tanggal Masak</th>
                                <th scope="col" class="px-6 py-4">Rincian Menu & Target Porsi</th>
                                <th scope="col" class="px-6 py-4 text-center">Realisasi Total</th>
                                <th scope="col" class="px-6 py-4 text-center">Status Rencana</th>
                                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($plans as $plan)
                                @php
                                    $totalPlanned = $plan->totalPlannedQuantity();
                                    $totalActual = $plan->totalActualQuantity();
                                    $percent = $totalPlanned > 0 ? min(100, round(($totalActual / $totalPlanned) * 100)) : 0;
                                    $allDone = $plan->isAllItemsCompleted();
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-mono text-xs font-bold shrink-0">
                                                PLN
                                            </div>
                                            <div>
                                                <div>{{ $plan->plan_number }}</div>
                                                <div class="text-xs font-normal text-gray-400">Dibuat: {{ $plan->created_at->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-800">{{ $plan->production_date->translatedFormat('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">Jadwal Masak</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-1.5">
                                            @foreach ($plan->details as $detail)
                                                @php
                                                    $actual = $detail->production?->actual_quantity ?? 0;
                                                    $isCompleted = $detail->production && $detail->production->actual_quantity >= $detail->planned_quantity;
                                                    $isPartial = $detail->production && $detail->production->actual_quantity > 0 && $detail->production->actual_quantity < $detail->planned_quantity;
                                                @endphp
                                                <div class="flex items-center justify-between text-xs py-0.5 border-b border-gray-50 last:border-0">
                                                    <span class="font-medium text-gray-800 truncate max-w-xs" title="{{ $detail->product->name }}">
                                                        {{ $detail->product->name }}
                                                    </span>
                                                    <div class="flex items-center space-x-2 ms-3 shrink-0">
                                                        <span class="text-gray-500">
                                                            <strong class="{{ $actual >= $detail->planned_quantity ? 'text-emerald-700 font-bold' : ($actual > 0 ? 'text-amber-700 font-bold' : 'text-gray-400') }}">
                                                                {{ $actual }}
                                                            </strong> / {{ $detail->planned_quantity }} porsi
                                                        </span>
                                                        @if ($isCompleted)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                                Selesai
                                                            </span>
                                                        @elseif ($isPartial)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">
                                                                Parsial
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">
                                                                Belum
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="inline-block text-left w-28">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-bold text-gray-700">{{ $totalActual }} / {{ $totalPlanned }}</span>
                                                <span class="text-gray-500 font-mono text-[11px]">{{ $percent }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="{{ $percent >= 100 ? 'bg-emerald-500' : ($percent > 0 ? 'bg-amber-500' : 'bg-gray-300') }} h-1.5 rounded-full"
                                                     style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $plan->status->badgeClass() }}">
                                            {{ $plan->status->label() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if ($plan->status->value === 'in_progress')
                                            <a href="{{ route('production-plans.realization.create', $plan) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                                <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                {{ $totalActual > 0 ? 'Perbarui Realisasi' : 'Catat Realisasi' }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">
                                                {{ $plan->status->value === 'completed' ? 'Telah Selesai' : 'Tidak Aktif' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-500">Tidak ada rencana produksi yang cocok dengan filter.</span>
                                            <span class="text-xs text-gray-400">Pastikan ada rencana berstatus 'Sedang Dimasak' (In Progress) untuk dicatat realisasinya.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($plans->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $plans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
