<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full text-xs font-semibold border border-purple-100">
                        Operasional Dapur
                    </span>
                    <span class="text-xs text-gray-400">&bull;</span>
                    <span class="text-xs text-gray-500 font-medium">Jadwal &amp; Alokasi Porsi</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Daftar Rencana Produksi Dapur') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Pantau jadwal memasak, target porsi hidangan, kesiapan menu, dan status operasional dapur secara terpadu.
                </p>
            </div>
            @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                <div>
                    <a href="{{ route('production-plans.create') }}"
                       id="btn-create-production-plan"
                       class="bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-semibold text-xs rounded-xl px-4 py-2.5 shadow-md shadow-purple-200 transition-all flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Rencana Baru</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        showDeleteModal: false,
        selectedPlan: {},
        deleteFormAction: '',

        confirmDelete(plan, deleteUrl) {
            this.selectedPlan = plan;
            this.deleteFormAction = deleteUrl;
            this.showDeleteModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Success -->
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

            <!-- Flash Alert Warning -->
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

            <!-- Filter Validation Warning -->
            @if (!empty($filterWarning))
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-xl shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">{{ $filterWarning }}</span>
                    </div>
                </div>
            @endif

            <!-- 2. SUMMARY STATS CARDS (Modern Pastel Theme) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {{-- Card 1: Semua Rencana --}}
                <a href="{{ route('production-plans.index') }}"
                   class="bg-white p-5 rounded-2xl border transition-all text-center group shadow-2xs {{ empty($currentStatus) ? 'border-purple-300 ring-2 ring-purple-500/20 bg-purple-50/20' : 'border-gray-100 hover:border-gray-200' }}">
                    <span class="text-xs text-gray-500 font-medium block">Semua Rencana</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block group-hover:text-purple-700 transition">
                        {{ $statusCounts['all'] ?? 0 }}
                    </span>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">Total rencana kerja</span>
                </a>

                {{-- Card 2: Dijadwalkan --}}
                <a href="{{ route('production-plans.index', ['status' => 'scheduled']) }}"
                   class="bg-blue-50/50 p-5 rounded-2xl border transition-all text-center group shadow-2xs {{ $currentStatus === 'scheduled' ? 'border-blue-400 ring-2 ring-blue-500/20 font-bold bg-blue-50/80' : 'border-blue-100/80 hover:border-blue-200' }}">
                    <span class="text-xs text-blue-700 font-semibold block">Dijadwalkan</span>
                    <span class="text-2xl font-black text-blue-900 mt-1 block">
                        {{ $statusCounts['scheduled'] ?? 0 }}
                    </span>
                    <span class="text-[11px] text-blue-600/70 mt-0.5 block">Menunggu dimasak</span>
                </a>

                {{-- Card 3: Sedang Dimasak --}}
                <a href="{{ route('production-plans.index', ['status' => 'in_progress']) }}"
                   class="bg-amber-50/50 p-5 rounded-2xl border transition-all text-center group shadow-2xs {{ $currentStatus === 'in_progress' ? 'border-amber-400 ring-2 ring-amber-500/20 font-bold bg-amber-50/80' : 'border-amber-100/80 hover:border-amber-200' }}">
                    <span class="text-xs text-amber-700 font-semibold block">Sedang Dimasak</span>
                    <span class="text-2xl font-black text-amber-900 mt-1 block">
                        {{ $statusCounts['in_progress'] ?? 0 }}
                    </span>
                    <span class="text-[11px] text-amber-600/70 mt-0.5 block">Proses masak aktif</span>
                </a>

                {{-- Card 4: Selesai --}}
                <a href="{{ route('production-plans.index', ['status' => 'completed']) }}"
                   class="bg-emerald-50/50 p-5 rounded-2xl border transition-all text-center group shadow-2xs {{ $currentStatus === 'completed' ? 'border-emerald-400 ring-2 ring-emerald-500/20 font-bold bg-emerald-50/80' : 'border-emerald-100/80 hover:border-emerald-200' }}">
                    <span class="text-xs text-emerald-700 font-semibold block">Selesai</span>
                    <span class="text-2xl font-black text-emerald-900 mt-1 block">
                        {{ $statusCounts['completed'] ?? 0 }}
                    </span>
                    <span class="text-[11px] text-emerald-600/70 mt-0.5 block">Produksi tuntas</span>
                </a>
            </div>

            <!-- 3. FILTER CARD & INPUTS -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6">
                <form method="GET" action="{{ route('production-plans.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    {{-- Input Tanggal --}}
                    <div class="md:col-span-4">
                        <label for="production_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Tanggal Produksi
                        </label>
                        <input type="date"
                               name="production_date"
                               id="production_date"
                               value="{{ $currentDate }}"
                               class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2 text-sm text-gray-800 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>

                    {{-- Select Status Rencana --}}
                    <div class="md:col-span-3">
                        <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Status Rencana
                        </label>
                        <select name="status"
                                id="status"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2 text-sm text-gray-800 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                            <option value="">Semua Status</option>
                            <option value="scheduled" {{ $currentStatus === 'scheduled' ? 'selected' : '' }}>Dijadwalkan</option>
                            <option value="in_progress" {{ $currentStatus === 'in_progress' ? 'selected' : '' }}>Sedang Dimasak</option>
                            <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    {{-- Cari No. Rencana --}}
                    <div class="md:col-span-3">
                        <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Cari No. Rencana
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ $currentSearch }}"
                                   placeholder="Contoh: PLN-2026..."
                                   class="w-full pl-9 bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2 text-sm text-gray-800 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all">
                        </div>
                    </div>

                    {{-- Tombol Filter & Reset --}}
                    <div class="md:col-span-2 flex items-center gap-2">
                        <button type="submit"
                                id="btn-apply-filter"
                                class="w-full bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl px-4 py-2.5 transition-all text-center shadow-2xs">
                            Terapkan Filter
                        </button>
                        @if ($currentSearch || $currentStatus || $currentDate)
                            <a href="{{ route('production-plans.index') }}"
                               id="btn-reset-filter"
                               class="p-2.5 bg-gray-100 text-gray-600 hover:text-gray-900 rounded-xl hover:bg-gray-200 transition"
                               title="Reset Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 4. TABEL DAFTAR RENCANA PRODUKSI DAPUR -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50/70 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left">No. Rencana</th>
                                <th scope="col" class="px-6 py-4 text-left">Tanggal Produksi</th>
                                <th scope="col" class="px-6 py-4 text-left">Target Menu &amp; Porsi</th>
                                <th scope="col" class="px-6 py-4 text-left">Dibuat Oleh</th>
                                <th scope="col" class="px-6 py-4 text-center">Status</th>
                                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($plans as $plan)
                                @php
                                    $planPayload = [
                                        'id' => $plan->id,
                                        'plan_number' => $plan->plan_number,
                                    ];
                                @endphp
                                <tr class="hover:bg-purple-50/20 transition">
                                    {{-- 1. No. Rencana & Tanggal Rencana Dibuat --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-purple-700 font-mono tracking-tight">
                                            <a href="{{ route('production-plans.show', $plan) }}" class="hover:text-purple-900 transition">
                                                {{ $plan->plan_number }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            Dibuat: {{ $plan->plan_date ? $plan->plan_date->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    {{-- 2. Tanggal Produksi / Memasak --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">
                                            {{ $plan->production_date ? $plan->production_date->translatedFormat('l, d M Y') : '-' }}
                                        </div>
                                        @if ($plan->notes)
                                            <div class="text-xs text-gray-400 italic truncate max-w-xs mt-0.5" title="{{ $plan->notes }}">
                                                Catatan: {{ $plan->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 3. Target Menu & Total Porsi --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md font-bold text-xs bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }} Porsi
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                ({{ $plan->details_count }} menu masakan)
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-700 font-medium space-y-1">
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
                                        <div class="font-semibold text-gray-900">{{ $plan->creator?->name ?? 'Sistem' }}</div>
                                        <div class="text-gray-400 capitalize">{{ $plan->creator?->role?->value ?? '-' }}</div>
                                    </td>

                                    {{-- 5. Status Badge Ringkas --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusEnum = $plan->status instanceof \App\Enums\ProductionPlanStatus 
                                                ? $plan->status 
                                                : \App\Enums\ProductionPlanStatus::tryFrom($plan->status);
                                        @endphp
                                        @if ($statusEnum === \App\Enums\ProductionPlanStatus::Scheduled)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 me-1.5"></span>
                                                Dijadwalkan
                                            </span>
                                        @elseif ($statusEnum === \App\Enums\ProductionPlanStatus::InProgress)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 me-1.5"></span>
                                                Sedang Dimasak
                                            </span>
                                        @elseif ($statusEnum === \App\Enums\ProductionPlanStatus::Completed)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1.5"></span>
                                                Selesai
                                            </span>
                                        @elseif ($statusEnum)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusEnum->badgeClass() }}">
                                                {{ $statusEnum->label() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $plan->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 6. Kolom Aksi Minimalis dengan Icon Buttons & Tooltips --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center gap-1.5 justify-center">
                                            {{-- Detail (Ikon Mata) --}}
                                            <a href="{{ route('production-plans.show', $plan) }}"
                                               class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-xl border border-transparent hover:border-purple-200/60 transition shadow-2xs"
                                               title="Lihat Detail Rencana Produksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- Lembar Bahan (Ikon Clipboard / MRP) --}}
                                            <a href="{{ route('production-plans.ingredients', $plan) }}"
                                               class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl border border-transparent hover:border-blue-200/60 transition shadow-2xs"
                                               title="Lembar Kebutuhan Bahan Dapur (MRP)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </a>

                                            @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                                {{-- Edit (Ikon Pensil) --}}
                                                <a href="{{ route('production-plans.edit', $plan) }}"
                                                   class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl border border-transparent hover:border-emerald-200/60 transition shadow-2xs"
                                                   title="Edit Rencana">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                {{-- Hapus (Ikon Tong Sampah dengan Modal Konfirmasi) --}}
                                                <button type="button"
                                                        @click="confirmDelete({{ json_encode($planPayload) }}, '{{ route('production-plans.destroy', $plan) }}')"
                                                        class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl border border-transparent hover:border-rose-200/60 transition shadow-2xs"
                                                        title="Hapus Rencana Produksi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
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
                                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-base font-bold text-gray-900">Tidak ada rencana produksi ditemukan</h3>
                                                <p class="text-xs text-gray-500 mt-1 mb-4">
                                                    Tidak ada jadwal rencana produksi yang sesuai dengan filter yang dipilih.
                                                </p>
                                                <a href="{{ route('production-plans.index') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition shadow-2xs">
                                                    Reset Filter
                                                </a>
                                            @else
                                                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-base font-bold text-gray-900">Belum ada rencana produksi</h3>
                                                <p class="text-xs text-gray-500 mt-1 mb-4">
                                                    Belum ada jadwal rencana produksi dapur yang dibuat dalam sistem.
                                                </p>
                                                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                                                    <a href="{{ route('production-plans.create') }}"
                                                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl transition shadow-md shadow-purple-200">
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

        {{-- ======================================================== --}}
        {{-- MODAL KONFIRMASI HAPUS RENCANA (DANGER MODAL)            --}}
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
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Hapus Rencana Produksi</h3>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                        Apakah Anda yakin ingin menghapus rencana produksi <strong class="text-gray-900 font-semibold" x-text="selectedPlan.plan_number"></strong>?
                    </p>

                    <!-- Alert Box Note Warning -->
                    <div class="bg-rose-50/50 border border-rose-100/80 rounded-2xl p-3.5 mb-6 text-left flex items-start gap-3">
                        <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-rose-800/80 leading-relaxed">
                            <strong>Perhatian:</strong> Penghapusan rencana produksi akan membatalkan jadwal masak yang telah dialokasikan. Pastikan tidak ada transaksi bahan aktif terkait rencana ini.
                        </p>
                    </div>

                    <!-- Footer Tombol Aksi Sejajar (2 Kolom) -->
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="showDeleteModal = false" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-rose-200 transition-all">
                            Ya, Hapus Rencana
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
