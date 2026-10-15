<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Master Data Pelanggan') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan catering, sekolah, instansi, dan alamat pengiriman pesanan (UC-26 & FR-10).</p>
            </div>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Pelanggan Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Session Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('customers.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Cari Nama / Kontak / Alamat</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik nama pelanggan, sekolah, kantor, atau telepon..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status Langganan</label>
                        <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Semua Status --</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif Memesan</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Tidak Aktif (Dinonaktifkan)</option>
                        </select>
                    </div>

                    <!-- Tombol Filter -->
                    <div class="md:col-span-3 flex items-center justify-end gap-2 pt-2">
                        @if (!empty($filters['search']) || !empty($filters['status']))
                            <a href="{{ route('customers.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm transition">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customers Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Nama Pelanggan / Instansi</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Kontak (Telepon & Email)</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Alamat Pengiriman</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($customers as $item)
                                <tr class="hover:bg-gray-50/75 transition">
                                    <!-- Nama & Catatan -->
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                        @if ($item->notes)
                                            <div class="text-xs text-gray-500 truncate max-w-xs mt-0.5" title="{{ $item->notes }}">{{ $item->notes }}</div>
                                        @endif
                                    </td>

                                    <!-- Kontak -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-gray-900 text-xs font-medium">{{ $item->phone ?: '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->email ?: '-' }}</div>
                                    </td>

                                    <!-- Alamat Pengiriman -->
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-600 max-w-sm truncate" title="{{ $item->address }}">
                                            {{ $item->address ?: '-' }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->isActive())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <!-- Detail (Admin & Owner) -->
                                        <a href="{{ route('customers.show', $item) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900 transition">
                                            Detail
                                        </a>

                                        @if (auth()->user()->isAdmin())
                                            <!-- Edit -->
                                            <a href="{{ route('customers.edit', $item) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 transition">
                                                Edit
                                            </a>

                                            <!-- Toggle Status (BR-02: Soft Toggle) -->
                                            <form method="POST" action="{{ route('customers.toggle-status', $item) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status pelanggan ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs {{ $item->isActive() ? 'text-amber-600 hover:text-amber-900' : 'text-emerald-600 hover:text-emerald-900' }} transition">
                                                    {{ $item->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="font-medium">Tidak ada data pelanggan yang ditemukan.</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($customers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
