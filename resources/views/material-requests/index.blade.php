<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Permintaan Bahan Baku & Verifikasi Stok Keluar') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Verifikasi permintaan bahan baku dari dapur dan catat transaksi stok keluar (Stok OUT) ke kartu stok.
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if ($pendingCount > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                        <svg class="w-3.5 h-3.5 me-1.5 text-amber-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="6" />
                        </svg>
                        {{ $pendingCount }} Menunggu Verifikasi
                    </span>
                @endif

                @if (in_array(Auth::user()->role?->value, ['admin', 'produksi'], true))
                    <a href="{{ route('material-requests.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajukan Permintaan Bahan
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Alert --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error') || $errors->has('status') || $errors->has('general'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-red-800 font-medium">
                        {{ session('error') ?? $errors->first('status') ?? $errors->first('general') }}
                    </p>
                </div>
            @endif

            {{-- Tab & Filter Controls --}}
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                {{-- Status Tabs --}}
                <div class="flex items-center space-x-2 overflow-x-auto pb-2 sm:pb-0">
                    <a href="{{ route('material-requests.index', ['status' => 'pending', 'search' => $currentSearch]) }}"
                       class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Menunggu Verifikasi
                        @if ($pendingCount > 0)
                            <span class="ms-1.5 px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-200 text-amber-800' }}">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('material-requests.index', ['status' => 'approved', 'search' => $currentSearch]) }}"
                       class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition {{ $currentStatus === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Sudah Diverifikasi
                    </a>
                    <a href="{{ route('material-requests.index', ['status' => 'all', 'search' => $currentSearch]) }}"
                       class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition {{ $currentStatus === 'all' ? 'bg-gray-800 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua Status
                    </a>
                </div>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('material-requests.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ $currentSearch }}"
                               placeholder="Cari ID, PLN-XXXX, atau pemohon..."
                               class="text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200 w-64">
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-900 transition">
                        Cari
                    </button>
                    @if ($currentSearch)
                        <a href="{{ route('material-requests.index', ['status' => $currentStatus]) }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table List --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                @if ($materialRequests->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-700">Tidak ada permintaan bahan</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            @if ($currentStatus === 'pending')
                                Saat ini tidak ada permintaan bahan dari dapur yang membutuhkan verifikasi stok keluar.
                            @else
                                Tidak ada data permintaan bahan yang sesuai dengan kriteria filter.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50/80 text-gray-600 text-xs uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="py-3.5 px-4 text-left">ID Permintaan</th>
                                    <th class="py-3.5 px-4 text-left">Tanggal</th>
                                    <th class="py-3.5 px-4 text-left">Rencana Produksi</th>
                                    <th class="py-3.5 px-4 text-left">Pemohon (Dapur)</th>
                                    <th class="py-3.5 px-4 text-center">Jumlah Item</th>
                                    <th class="py-3.5 px-4 text-center">Status</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-800 font-medium">
                                @foreach ($materialRequests as $req)
                                    <tr class="hover:bg-gray-50/60 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-gray-900">
                                            #MR-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 text-xs">
                                            {{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if ($req->productionPlan)
                                                <a href="{{ route('production-plans.show', $req->productionPlan) }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                                    {{ $req->productionPlan->plan_number }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Tanpa Rencana</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="text-xs font-semibold text-gray-900">{{ $req->requestedBy?->name ?? 'Dapur' }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $req->requestedBy?->role?->label() ?? 'Staff' }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                {{ $req->details_count ?? $req->details->count() }} Bahan
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if ($req->isPending())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    Pending
                                                </span>
                                            @elseif ($req->isApproved())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                    {{ ucfirst($req->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            @if ($req->isPending())
                                                <a href="{{ route('material-requests.show', $req) }}"
                                                   class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                                    Periksa & Verifikasi &rarr;
                                                </a>
                                            @else
                                                <a href="{{ route('material-requests.show', $req) }}"
                                                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                                                    Lihat Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($materialRequests->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $materialRequests->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
