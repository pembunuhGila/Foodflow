
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">
                    FOODFLOW / OPERASIONAL
                </p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">
                    Dashboard Produksi
                </h2>
            </div>

            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-800">
                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                {{ Auth::user()->role->label() }}
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-[1600px] space-y-6 px-4 py-6 sm:px-6 lg:px-8">

            {{-- Judul dan tanggal --}}
            <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">
                        Ringkasan operasional dapur
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Selamat bekerja, {{ Auth::user()->name }}
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Pantau rencana, kebutuhan bahan, dan hasil produksi dari satu halaman.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="rounded-lg border border-slate-200 bg-white px-4 py-2.5">
                        <p class="text-xs text-slate-500">Tanggal operasional</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $today->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <a href="{{ route('production-plans.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-800 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-900">
                        <span class="text-lg leading-none">+</span>
                        Buat Rencana
                    </a>
                </div>
            </section>

            {{-- Ringkasan KPI --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <a href="{{ route('production-plans.index') }}"
                   class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Rencana Produksi Aktif
                            </p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                                {{ number_format($activePlanCount, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-800">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="3" width="16" height="18" rx="2"/>
                                <path d="M8 8h8M8 12h8M8 16h5"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Dijadwalkan atau sedang dimasak
                    </p>
                </a>

                <a href="{{ route('production-plans.index') }}"
                   class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Target Porsi Hari Ini
                            </p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                                {{ number_format($todayTarget, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-50 text-indigo-800">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M4 5h16v15H4zM8 2v6M16 2v6M4 10h16"/>
                                <path d="M8 14h3M8 17h6"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Total target dari rencana hari ini
                    </p>
                </a>

                <a href="{{ route('productions.index') }}"
                   class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Realisasi Porsi
                            </p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                                {{ number_format($todayActual, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Berdasarkan catatan realisasi masak
                    </p>
                </a>

                <a href="{{ route('material-requests.index') }}"
                   class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-amber-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Permintaan Bahan Pending
                            </p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                                {{ number_format($pendingMaterials, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M3 7h18v13H3zM3 7l3-4h12l3 4"/>
                                <path d="M12 11v4M12 17h.01"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Menunggu verifikasi permintaan
                    </p>
                </a>

            </section>

            {{-- Grafik dan progres produksi --}}
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Target vs Realisasi Produksi
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Perbandingan jumlah porsi selama tujuh hari terakhir.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-4 text-xs text-slate-600">
                            <span class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-sm bg-blue-800"></span>
                                Target
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-sm bg-amber-400"></span>
                                Realisasi
                            </span>
                        </div>
                    </div>

                    @php
                        $chartMax = max(
                            1,
                            (int) $weeklyChart->max('target'),
                            (int) $weeklyChart->max('actual')
                        );
                    @endphp

                    <div class="mt-6 grid grid-cols-7 gap-2 sm:gap-4">
                        @foreach ($weeklyChart as $day)
                            @php
                                $targetHeight = $day['target'] > 0
                                    ? max(3, ($day['target'] / $chartMax) * 100)
                                    : 0;

                                $actualHeight = $day['actual'] > 0
                                    ? max(3, ($day['actual'] / $chartMax) * 100)
                                    : 0;
                            @endphp

                            <div class="min-w-0 text-center">
                                <div class="flex h-40 items-end justify-center gap-1 border-b border-slate-200 pb-1">
                                    <div
                                        class="w-3 rounded-t bg-blue-800 sm:w-5"
                                        style="height: {{ $targetHeight }}%"
                                        title="Target {{ $day['target'] }} porsi">
                                    </div>

                                    <div
                                        class="w-3 rounded-t bg-amber-400 sm:w-5"
                                        style="height: {{ $actualHeight }}%"
                                        title="Realisasi {{ $day['actual'] }} porsi">
                                    </div>
                                </div>

                                <p class="mt-2 text-xs font-semibold text-slate-700">
                                    {{ $day['label'] }}
                                </p>
                                <p class="mt-1 text-[10px] text-slate-400">
                                    {{ $day['date'] }}
                                </p>
                                <p class="mt-1 break-words text-[10px] text-slate-500">
                                    {{ number_format($day['actual'], 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-lg bg-slate-50 p-3">
                        <p class="text-xs leading-5 text-slate-500">
                            Angka di bawah grafik menunjukkan realisasi porsi. Arahkan kursor ke batang grafik untuk melihat nilai target atau realisasi.
                        </p>
                    </div>
                </div>

                {{-- Ringkasan target harian --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900">
                        Progres Hari Ini
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Capaian terhadap target porsi.
                    </p>

                    <div class="mt-7 flex justify-center">
                        <div
                            class="relative flex h-40 w-40 items-center justify-center rounded-full"
                            style="background: conic-gradient(#1e40af {{ min(100, $completionRate) }}%, #e2e8f0 0)">
                            <div class="flex h-32 w-32 flex-col items-center justify-center rounded-full bg-white">
                                <span class="text-3xl font-bold text-slate-900">
                                    {{ number_format($completionRate, 1, ',', '.') }}%
                                </span>
                                <span class="mt-1 text-xs text-slate-500">
                                    Target tercapai
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Target produksi</span>
                                <span class="font-semibold text-slate-900">
                                    {{ number_format($todayTarget, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-blue-800"
                                     style="width: {{ $todayTarget > 0 ? 100 : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Porsi terealisasi</span>
                                <span class="font-semibold text-slate-900">
                                    {{ number_format($todayActual, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-amber-400"
                                     style="width: {{ min(100, $completionRate) }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($todayTarget === 0)
                        <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">
                            Belum ada target produksi untuk hari ini.
                        </div>
                    @elseif ($todayActual >= $todayTarget)
                        <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800">
                            Target porsi hari ini sudah tercapai.
                        </div>
                    @else
                        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                            Sisa {{ number_format($todayTarget - $todayActual, 0, ',', '.') }} porsi untuk mencapai target.
                        </div>
                    @endif
                </div>
            </section>

            {{-- Tabel rencana aktif --}}
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Rencana Produksi Aktif
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Rencana yang masih dijadwalkan atau dalam proses.
                        </p>
                    </div>

                    <a href="{{ route('production-plans.index') }}"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-blue-800 hover:text-blue-950">
                        Lihat semua rencana <span>&rarr;</span>
                    </a>
                </div>

                @if ($activePlans->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-800">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="3" width="16" height="18" rx="2"/>
                                <path d="M8 8h8M8 12h8M8 16h5"/>
                            </svg>
                        </div>

                        <h4 class="mt-3 font-semibold text-slate-900">
                            Belum ada rencana produksi aktif
                        </h4>
                        <p class="mt-1 text-sm text-slate-500">
                            Rencana produksi yang dijadwalkan atau sedang dimasak akan tampil di sini.
                        </p>

                        <a href="{{ route('production-plans.create') }}"
                           class="mt-4 inline-flex rounded-lg bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">
                            Buat rencana produksi
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Nomor rencana
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Tanggal
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Menu
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Target
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Realisasi
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Status
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Detail
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($activePlans as $plan)
                                    <tr class="transition hover:bg-blue-50/40">
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="text-sm font-semibold text-slate-900">
                                                {{ $plan->plan_number }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                            {{ $plan->production_date?->format('d/m/Y') ?? '-' }}
                                        </td>

                                        <td class="px-5 py-4">
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $plan->details->count() }} menu
                                            </p>
                                            <p class="mt-1 max-w-xs truncate text-xs text-slate-500">
                                                {{ $plan->details->pluck('product.name')->filter()->join(', ') ?: 'Belum ada detail menu' }}
                                            </p>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium text-slate-800">
                                            {{ number_format($plan->totalPlannedQuantity(), 0, ',', '.') }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium text-slate-800">
                                            {{ number_format($plan->totalActualQuantity(), 0, ',', '.') }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $plan->status->badgeClass() }}">
                                                {{ $plan->status->label() }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <a href="{{ route('production-plans.show', $plan) }}"
                                               class="text-sm font-semibold text-blue-800 hover:text-blue-950">
                                                Buka &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Akses cepat --}}
            <section>
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">
                        Akses Operasional
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Akses langsung ke fitur utama pekerjaan produksi.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <a href="{{ route('production-plans.index') }}"
                       class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <rect x="4" y="3" width="16" height="18" rx="2"/>
                                    <path d="M8 8h8M8 12h8M8 16h5"/>
                                </svg>
                            </div>
                            <span class="text-slate-400 transition group-hover:translate-x-1">&rarr;</span>
                        </div>
                        <h4 class="mt-4 font-semibold text-slate-900">Rencana Produksi</h4>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Atur jadwal, menu, target porsi, dan kebutuhan bahan baku.
                        </p>
                    </a>

                    <a href="{{ route('material-requests.index') }}"
                       class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M3 7h18v13H3zM3 7l3-4h12l3 4"/>
                                    <path d="M12 11v4M10 13h4"/>
                                </svg>
                            </div>
                            <span class="text-slate-400 transition group-hover:translate-x-1">&rarr;</span>
                        </div>
                        <h4 class="mt-4 font-semibold text-slate-900">Permintaan Bahan</h4>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Ajukan kebutuhan bahan dan pantau proses penyerahan dari gudang.
                        </p>
                    </a>

                    <a href="{{ route('productions.index') }}"
                       class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m8 12 2.5 2.5L16 9"/>
                                </svg>
                            </div>
                            <span class="text-slate-400 transition group-hover:translate-x-1">&rarr;</span>
                        </div>
                        <h4 class="mt-4 font-semibold text-slate-900">Realisasi Masak</h4>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Catat porsi matang aktual dan pantau hasil produksi.
                        </p>
                    </a>

                </div>
            </section>

            <footer class="border-t border-slate-200 py-4">
                <p class="text-xs text-slate-400">
                    FoodFlow · Dashboard operasional produksi · Data berdasarkan catatan sistem.
                </p>
            </footer>

        </div>
    </div>
</x-app-layout>
