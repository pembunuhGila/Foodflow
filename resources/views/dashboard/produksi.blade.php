<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <!-- Breadcrumb -->
                <p class="text-xs font-semibold uppercase tracking-widest text-purple-600 mb-1">
                    FOODFLOW / PRODUKSI / Dashboard Produksi
                </p>
                <h2 class="text-xl font-bold text-gray-900">
                    Dashboard Produksi
                </h2>
            </div>

            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-purple-100 bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-800">
                <span class="h-2 w-2 rounded-full bg-purple-600 animate-pulse"></span>
                {{ Auth::user()->role?->label() ?? 'Staff Produksi' }}
            </span>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- 1. Greeting & Welcome Banner --}}
            <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100 mb-2">
                        <span>Ringkasan Operasional Dapur</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                        Selamat bekerja, {{ Auth::user()->name }}
                    </h1>
                    <p class="mt-1.5 text-xs sm:text-sm text-gray-500 max-w-2xl leading-relaxed">
                        Pantau rencana memasak harian, alokasi target porsi, kesiapan logistik bahan, dan pencatatan realisasi dapur secara terpadu.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/75 px-4 py-2.5 text-right">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Tanggal Operasional</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            {{ $today->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <a href="{{ route('production-plans.create') }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-xl px-4 py-2.5 shadow-md shadow-purple-200 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Rencana</span>
                    </a>
                </div>
            </section>

            {{-- 2. 4 Kartu Ringkasan Atas (Metric Cards) --}}
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Rencana Produksi Aktif (Purple) -->
                <a href="{{ route('production-plans.index') }}"
                   class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:shadow-md hover:border-purple-200 flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                Rencana Produksi Aktif
                            </p>
                            <p class="mt-2 text-2xl font-bold text-gray-900 font-mono">
                                {{ number_format($activePlanCount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Status dijadwalkan / sedang dimasak
                    </p>
                </a>

                <!-- Card 2: Target Porsi Hari Ini (Indigo) -->
                <a href="{{ route('production-plans.index') }}"
                   class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:shadow-md hover:border-indigo-200 flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                Target Porsi Hari Ini
                            </p>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-gray-900 font-mono">
                                    {{ number_format($todayTarget, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-gray-400 font-medium">porsi</span>
                            </div>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Total akumulasi target rencana hari ini
                    </p>
                </a>

                <!-- Card 3: Realisasi Porsi (Emerald) -->
                <a href="{{ route('productions.index') }}"
                   class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:shadow-md hover:border-emerald-200 flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                Realisasi Porsi
                            </p>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-emerald-700 font-mono">
                                    {{ number_format($todayActual, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-emerald-600 font-medium">porsi</span>
                            </div>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Porsi matang yang telah dicatat dapur
                    </p>
                </a>

                <!-- Card 4: Permintaan Bahan Pending (Amber) -->
                <a href="{{ route('material-requests.index') }}"
                   class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:shadow-md hover:border-amber-200 flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                Permintaan Bahan Pending
                            </p>
                            <p class="mt-2 text-2xl font-bold font-mono {{ $pendingMaterials > 0 ? 'text-amber-700' : 'text-gray-900' }}">
                                {{ number_format($pendingMaterials, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Menunggu verifikasi serah terima gudang
                    </p>
                </a>
            </section>

            {{-- 3 & 4. Grafik Target vs Realisasi (Chart.js) + Progres Hari Ini (Donut) --}}
            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- 3. Kartu Bar Chart Target vs Realisasi -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm xl:col-span-2 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-gray-50">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">
                                    Target vs Realisasi Produksi
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Perbandingan jumlah porsi target dan hasil matang selama 7 hari terakhir.
                                </p>
                            </div>

                            <div class="flex items-center gap-4 text-xs font-medium">
                                <span class="inline-flex items-center gap-1.5 text-gray-600">
                                    <span class="w-3 h-3 rounded bg-purple-600"></span>
                                    <span>Target</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5 text-gray-600">
                                    <span class="w-3 h-3 rounded bg-emerald-500"></span>
                                    <span>Realisasi</span>
                                </span>
                            </div>
                        </div>

                        <!-- Canvas Chart.js Container -->
                        <div class="mt-4 relative" style="height: 280px;">
                            <canvas id="productionWeeklyChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400">
                        <span>Arahkan kursor ke batang grafik untuk rincian porsi.</span>
                        <span class="font-medium text-gray-500">Periode 7 Hari Terakhir</span>
                    </div>
                </div>

                <!-- 4. Kartu Progres Hari Ini (Donut / Circular) -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="pb-4 border-b border-gray-50">
                            <h3 class="text-base font-bold text-gray-900">
                                Progres Hari Ini
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Persentase capaian terhadap target porsi harian.
                            </p>
                        </div>

                        <!-- Donut Chart Canvas Container with Centered Text -->
                        <div class="my-6 relative flex items-center justify-center" style="height: 190px;">
                            <canvas id="todayProgressChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-purple-700 font-mono">
                                    {{ number_format($completionRate, 1, ',', '.') }}%
                                </span>
                                <span class="text-[11px] font-medium text-gray-400 mt-0.5">Tercapai</span>
                            </div>
                        </div>

                        <!-- Rincian Target vs Realisasi dalam List Ringkas -->
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/75 border border-gray-100/80">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                    <span class="text-xs font-medium text-gray-600">Target Produksi</span>
                                </div>
                                <span class="text-sm font-bold font-mono text-gray-900">
                                    {{ number_format($todayTarget, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">porsi</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/75 border border-gray-100/80">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-medium text-gray-600">Porsi Terealisasi</span>
                                </div>
                                <span class="text-sm font-bold font-mono text-emerald-700">
                                    {{ number_format($todayActual, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">porsi</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Text Alert Ringkas -->
                    <div class="mt-4 pt-3 border-t border-gray-50">
                        @if ($todayTarget === 0)
                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/80 text-xs text-gray-500 text-center">
                                Belum ada target jadwal produksi hari ini.
                            </div>
                        @elseif ($todayActual >= $todayTarget)
                            <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-medium text-center flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Target porsi hari ini telah terpenuhi!</span>
                            </div>
                        @else
                            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 text-center">
                                Sisa <strong>{{ number_format($todayTarget - $todayActual, 0, ',', '.') }} porsi</strong> lagi untuk mencapai target.
                            </div>
                        @endif
                    </div>
                </div>

            </section>

            {{-- 5. Tabel Rencana Produksi Aktif --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Rencana Produksi Aktif
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Rencana yang berstatus dijadwalkan atau sedang dalam proses masak.
                        </p>
                    </div>

                    <a href="{{ route('production-plans.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors self-start sm:self-auto">
                        <span>Lihat Semua Rencana</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if ($activePlans->isEmpty())
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center mx-auto mb-3 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm">Belum Ada Rencana Produksi Aktif</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            Rencana produksi yang dijadwalkan atau sedang dimasak akan tampil secara otomatis di tabel ini.
                        </p>
                        <a href="{{ route('production-plans.create') }}"
                           class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all">
                            <span>+ Buat Rencana Baru</span>
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50/70 text-gray-400 font-bold uppercase tracking-wider text-xs">
                                <tr>
                                    <th scope="col" class="py-3 px-4 text-left rounded-l-xl">Nomor Rencana</th>
                                    <th scope="col" class="py-3 px-4 text-left">Tanggal Masak</th>
                                    <th scope="col" class="py-3 px-4 text-left">Menu Hidangan</th>
                                    <th scope="col" class="py-3 px-4 text-right">Target</th>
                                    <th scope="col" class="py-3 px-4 text-right">Realisasi</th>
                                    <th scope="col" class="py-3 px-4 text-center">Status</th>
                                    <th scope="col" class="py-3 px-4 text-center rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach ($activePlans as $plan)
                                    <tr class="hover:bg-purple-50/30 transition-colors">
                                        <td class="whitespace-nowrap py-3.5 px-4">
                                            <a href="{{ route('production-plans.show', $plan) }}" class="font-mono font-bold text-gray-900 hover:text-purple-600 transition-colors">
                                                {{ $plan->plan_number }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap py-3.5 px-4 text-xs text-gray-600">
                                            {{ $plan->production_date?->translatedFormat('d M Y') ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-gray-800 text-xs">
                                                {{ $plan->details->count() }} menu masakan
                                            </div>
                                            <div class="text-[11px] text-gray-400 truncate max-w-xs mt-0.5">
                                                {{ $plan->details->pluck('product.name')->filter()->join(', ') ?: 'Belum ada menu' }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap py-3.5 px-4 text-right font-mono font-bold text-gray-900 text-xs">
                                            {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap py-3.5 px-4 text-right font-mono font-bold text-emerald-700 text-xs">
                                            {{ number_format($plan->totalActualQuantity(), 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap py-3.5 px-4 text-center">
                                            <span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $plan->status->badgeClass() }}">
                                                {{ $plan->status->label() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap py-3.5 px-4 text-center">
                                            <a href="{{ route('production-plans.show', $plan) }}"
                                               title="Lihat Detail Rencana"
                                               class="inline-flex items-center justify-center p-1.5 bg-purple-50 hover:bg-purple-100 text-purple-600 rounded-lg transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- 6. Akses Operasional (Quick Shortcuts Bottom) --}}
            <section class="mb-4">
                <div class="mb-3.5">
                    <h3 class="text-base font-bold text-gray-900">
                        Akses Operasional
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pintasan cepat langsung ke modul utama produksi makanan.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pintasan 1: Rencana Produksi -->
                    <a href="{{ route('production-plans.index') }}"
                       class="group bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-purple-200 hover:shadow-md cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="text-gray-300 group-hover:text-purple-600 group-hover:translate-x-1 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm group-hover:text-purple-600 transition-colors">Rencana Produksi</h4>
                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                                Atur jadwal, menu masakan, target porsi, dan kalkulasi logistik bahan baku.
                            </p>
                        </div>
                    </a>

                    <!-- Pintasan 2: Permintaan Bahan -->
                    <a href="{{ route('material-requests.index') }}"
                       class="group bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-purple-200 hover:shadow-md cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <span class="text-gray-300 group-hover:text-amber-600 group-hover:translate-x-1 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm group-hover:text-amber-600 transition-colors">Permintaan Bahan</h4>
                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                                Ajukan kebutuhan bahan baku ke gudang dan pantau status serah terima stok.
                            </p>
                        </div>
                    </a>

                    <!-- Pintasan 3: Realisasi Masak -->
                    <a href="{{ route('productions.index') }}"
                       class="group bg-white rounded-2xl border border-gray-100 p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-purple-200 hover:shadow-md cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm group-hover:text-emerald-600 transition-colors">Realisasi Masak</h4>
                            <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                                Catat porsi matang aktual dari dapur dan pantau evaluasi hasil masakan.
                            </p>
                        </div>
                    </a>
                </div>
            </section>

        </div>
    </div>

    <!-- Script Chart.js CDN & Inisialisasi Grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data dari backend
            const weeklyData = @json($weeklyChart);
            const labels = weeklyData.map(item => item.label + ' (' + item.date + ')');
            const targetData = weeklyData.map(item => item.target);
            const actualData = weeklyData.map(item => item.actual);

            // 1. Inisialisasi Bar Chart (Target vs Realisasi)
            const weeklyCtx = document.getElementById('productionWeeklyChart');
            if (weeklyCtx) {
                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Target Porsi',
                                data: targetData,
                                backgroundColor: '#8B5CF6',
                                hoverBackgroundColor: '#7C3AED',
                                borderRadius: 6,
                                barThickness: 16,
                                maxBarThickness: 20
                            },
                            {
                                label: 'Realisasi Porsi',
                                data: actualData,
                                backgroundColor: '#10B981',
                                hoverBackgroundColor: '#059669',
                                borderRadius: 6,
                                barThickness: 16,
                                maxBarThickness: 20
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1E293B',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID') + ' porsi';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { size: 11 },
                                    color: '#64748B'
                                }
                            },
                            y: {
                                border: {
                                    dash: [4, 4]
                                },
                                grid: {
                                    color: '#F1F5F9'
                                },
                                ticks: {
                                    font: { size: 11 },
                                    color: '#64748B',
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // 2. Inisialisasi Donut Chart (Progres Hari Ini)
            const progressCtx = document.getElementById('todayProgressChart');
            if (progressCtx) {
                const rate = {{ min(100, (float) $completionRate) }};
                const remainder = Math.max(0, 100 - rate);

                new Chart(progressCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Tercapai', 'Sisa'],
                        datasets: [{
                            data: [rate, remainder],
                            backgroundColor: ['#7C3AED', '#F1F5F9'],
                            borderWidth: 0,
                            borderRadius: rate > 0 ? 4 : 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>