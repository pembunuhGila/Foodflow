<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Operasional Dapur
                    </span>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Daftar Rencana Produksi Dapur') }}
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Pantau jadwal memasak, target porsi hidangan, kesiapan menu, dan status operasional dapur secara terpadu.
                </p>
            </div>
            @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                <div>
                    <a href="{{ route('production-plans.create') }}"
                       id="btn-create-production-plan"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Rencana Baru
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter Validation Warning -->
            @if (!empty($filterWarning))
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ $filterWarning }}</span>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Jumlah Rencana Produksi -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('production-plans.index') }}"
                   class="p-4 bg-white rounded-xl border border-gray-100 shadow-2xs hover:border-gray-300 transition text-center {{ empty($currentStatus) ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
                    <span class="text-xs text-gray-500 font-medium block">Semua Rencana</span>
                    <span class="text-xl font-extrabold text-gray-800">{{ $statusCounts['all'] ?? 0 }}</span>
                </a>
                <a href="{{ route('production-plans.index', ['status' => 'scheduled']) }}"
                   class="p-4 bg-blue-50/60 rounded-xl border border-blue-200 shadow-2xs hover:bg-blue-100/60 transition text-center {{ $currentStatus === 'scheduled' ? 'ring-2 ring-blue-500 font-bold' : '' }}">
                    <span class="text-xs text-blue-700 font-semibold block">Dijadwalkan (Scheduled)</span>
                    <span class="text-xl font-extrabold text-blue-900">{{ $statusCounts['scheduled'] ?? 0 }}</span>
                </a>
                <a href="{{ route('production-plans.index', ['status' => 'in_progress']) }}"
                   class="p-4 bg-amber-50/60 rounded-xl border border-amber-200 shadow-2xs hover:bg-amber-100/60 transition text-center {{ $currentStatus === 'in_progress' ? 'ring-2 ring-amber-500 font-bold' : '' }}">
                    <span class="text-xs text-amber-700 font-semibold block">Sedang Dimasak (In Progress)</span>
                    <span class="text-xl font-extrabold text-amber-900">{{ $statusCounts['in_progress'] ?? 0 }}</span>
                </a>
                <a href="{{ route('production-plans.index', ['status' => 'completed']) }}"
                   class="p-4 bg-emerald-50/60 rounded-xl border border-emerald-200 shadow-2xs hover:bg-emerald-100/60 transition text-center {{ $currentStatus === 'completed' ? 'ring-2 ring-emerald-500 font-bold' : '' }}">
                    <span class="text-xs text-emerald-700 font-semibold block">Selesai (Completed)</span>
                    <span class="text-xl font-extrabold text-emerald-900">{{ $statusCounts['completed'] ?? 0 }}</span>
                </a>
            </div>

            <!-- Filter & Pencarian -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('production-plans.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="production_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Tanggal Produksi
                        </label>
                        <input type="date"
                               name="production_date"
                               id="production_date"
                               value="{{ $currentDate }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Status Rencana
                        </label>
                        <select name="status"
                                id="status"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <option value="">-- Semua Status --</option>
                            <option value="scheduled" {{ $currentStatus === 'scheduled' ? 'selected' : '' }}>Dijadwalkan (Scheduled)</option>
                            <option value="in_progress" {{ $currentStatus === 'in_progress' ? 'selected' : '' }}>Sedang Dimasak (In Progress)</option>
                            <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                            Cari No. Rencana
                        </label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="Contoh: PLN-2026..."
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                id="btn-apply-filter"
                                class="flex-1 px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                            Terapkan Filter
                        </button>
                        @if ($currentSearch || $currentStatus || $currentDate)
                            <a href="{{ route('production-plans.index') }}"
                               id="btn-reset-filter"
                               class="px-3 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition"
                               title="Reset Filter">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Rencana Produksi Dapur -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left">No. Rencana</th>
                                <th scope="col" class="px-6 py-4 text-left">Tanggal Produksi</th>
                                <th scope="col" class="px-6 py-4 text-left">Target Menu & Porsi</th>
                                <th scope="col" class="px-6 py-4 text-left">Dibuat Oleh</th>
                                <th scope="col" class="px-6 py-4 text-center">Status</th>
                                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($plans as $plan)
                                <tr class="hover:bg-gray-50/75 transition">
                                    {{-- 1. No. Rencana & Tanggal Rencana Dibuat --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900 font-mono">
                                            <a href="{{ route('production-plans.show', $plan) }}" class="text-emerald-700 hover:text-emerald-900 hover:underline">
                                                {{ $plan->plan_number }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            Dibuat: {{ $plan->plan_date ? $plan->plan_date->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    {{-- 2. Tanggal Produksi / Memasak --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-800">
                                            {{ $plan->production_date ? $plan->production_date->translatedFormat('l, d F Y') : '-' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ $plan->production_date ? $plan->production_date->format('d/m/Y') : '' }}
                                        </div>
                                        @if ($plan->notes)
                                            <div class="text-xs text-gray-500 truncate max-w-xs mt-0.5" title="{{ $plan->notes }}">
                                                Catatan: {{ $plan->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 3. Target Menu & Total Porsi --}}
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} Porsi
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                ({{ $plan->details_count }} menu)
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1.5 space-y-1">
                                            @foreach ($plan->details->take(3) as $detail)
                                                <div class="truncate max-w-xs flex items-center justify-between text-gray-700">
                                                    <span>&bull; {{ $detail->product?->name ?? 'Menu tidak ditemukan' }}</span>
                                                    <span class="text-gray-500 font-medium ms-1">{{ number_format($detail->planned_quantity, 0, ',', '.') }} porsi</span>
                                                </div>
                                            @endforeach
                                            @if ($plan->details_count > 3)
                                                <div class="text-gray-400 italic text-[11px] pt-0.5">
                                                    +{{ $plan->details_count - 3 }} menu hidangan lainnya...
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 4. Pembuat Rencana (Creator) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        <div class="font-medium text-gray-800">{{ $plan->creator?->name ?? 'Sistem' }}</div>
                                        <div class="text-gray-400 capitalize">{{ $plan->creator?->role?->value ?? '-' }}</div>
                                    </td>

                                    {{-- 5. Status Badge --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusEnum = $plan->status instanceof \App\Enums\ProductionPlanStatus 
                                                ? $plan->status 
                                                : \App\Enums\ProductionPlanStatus::tryFrom($plan->status);
                                        @endphp
                                        @if ($statusEnum)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusEnum->badgeClass() }}">
                                                {{ $statusEnum->label() }}
                                                @if ($statusEnum === \App\Enums\ProductionPlanStatus::Scheduled)
                                                    <span class="text-[10px] opacity-75 ms-1">(Scheduled)</span>
                                                @elseif ($statusEnum === \App\Enums\ProductionPlanStatus::InProgress)
                                                    <span class="text-[10px] opacity-75 ms-1">(In Progress)</span>
                                                @elseif ($statusEnum === \App\Enums\ProductionPlanStatus::Completed)
                                                    <span class="text-[10px] opacity-75 ms-1">(Completed)</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $plan->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 6. Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <a href="{{ route('production-plans.show', $plan) }}"
                                           class="inline-flex items-center text-emerald-600 hover:text-emerald-900 font-semibold"
                                           title="Lihat Detail Rencana Produksi">
                                            Detail &rarr;
                                        </a>

                                        <a href="{{ route('production-plans.ingredients', $plan) }}"
                                           class="inline-flex items-center text-blue-600 hover:text-blue-900"
                                           title="Lembar Kebutuhan Bahan Dapur">
                                            Lembar Bahan
                                        </a>

                                        @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                            <a href="{{ route('production-plans.edit', $plan) }}"
                                               class="inline-flex items-center text-indigo-600 hover:text-indigo-900"
                                               title="Edit Rencana">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('production-plans.destroy', $plan) }}"
                                                  class="inline-block"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana produksi {{ $plan->plan_number }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                @php
                                    $hasActiveFilter = !empty($currentDate) || !empty($currentStatus) || !empty($currentSearch);
                                @endphp
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-md mx-auto text-center">
                                            @if ($hasActiveFilter)
                                                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-base font-semibold text-gray-800">Tidak ada rencana produksi ditemukan</h3>
                                                <p class="text-sm text-gray-500 mt-1 mb-4">
                                                    Tidak ada jadwal rencana produksi yang sesuai dengan filter yang dipilih.
                                                </p>
                                                <a href="{{ route('production-plans.index') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition shadow-2xs">
                                                    Reset Filter
                                                </a>
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-base font-semibold text-gray-800">Belum ada rencana produksi</h3>
                                                <p class="text-sm text-gray-500 mt-1 mb-4">
                                                    Belum ada rencana produksi dapur yang dibuat dalam sistem.
                                                </p>
                                                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                                    <a href="{{ route('production-plans.create') }}"
                                                       class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                                        + Buat Rencana Produksi Baru
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($plans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $plans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
