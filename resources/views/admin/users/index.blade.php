<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Kelola Data Pengguna & Hak Akses') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daftar pengguna aktif dan pengelolaan hak akses sesuai UC-22.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengguna Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ resetModalOpen: false, selectedUser: null, resetAction: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Cari Nama / Email</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik nama atau email..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Filter Role -->
                    <div>
                        <label for="role" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Peran (Role)</label>
                        <select name="role" id="role" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Semua Role --</option>
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->value }}" {{ ($filters['role'] ?? '') === $roleOption->value ? 'selected' : '' }}>
                                    {{ $roleOption->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status Akun</label>
                        <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Semua Status --</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}" {{ ($filters['status'] ?? '') === $statusOption->value ? 'selected' : '' }}>
                                    {{ $statusOption->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Submit Buttons -->
                    <div class="md:col-span-4 flex items-center justify-end gap-2 pt-2">
                        @if (!empty($filters['search']) || !empty($filters['role']) || !empty($filters['status']))
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm transition">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Pengguna</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Peran (Role)</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Terdaftar</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($users as $userItem)
                                <tr class="hover:bg-gray-50/75 transition">
                                    <!-- Nama & Email -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $userItem->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $userItem->email }}</div>
                                    </td>

                                    <!-- Role -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $roleBadgeClass = match($userItem->role?->value) {
                                                'admin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'gudang' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'produksi' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'owner' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                default => 'bg-gray-100 text-gray-800 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $roleBadgeClass }}">
                                            {{ $userItem->role?->label() ?? 'Tanpa Role' }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($userItem->isActive())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tanggal Terdaftar -->
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $userItem->created_at?->format('d M Y, H:i') ?? '-' }}
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <!-- Edit -->
                                        <a href="{{ route('admin.users.edit', $userItem) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                            Edit
                                        </a>

                                        <!-- Reset Password Modal Trigger -->
                                        <button
                                            type="button"
                                            @click="resetModalOpen = true; selectedUser = '{{ $userItem->name }}'; resetAction = '{{ route('admin.users.reset-password', $userItem) }}'"
                                            class="inline-flex items-center text-amber-600 hover:text-amber-900"
                                        >
                                            Reset PW
                                        </button>

                                        <!-- Toggle Status (Kecuali Akun Sendiri) -->
                                        @if ($userItem->id !== Auth::id())
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $userItem) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin {{ $userItem->isActive() ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $userItem->name }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center {{ $userItem->isActive() ? 'text-red-600 hover:text-red-900' : 'text-emerald-600 hover:text-emerald-900' }}">
                                                    {{ $userItem->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 italic">(Akun Anda)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Alpine.js Reset Password Modal -->
        <div
            x-show="resetModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="resetModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="resetModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    x-show="resetModalOpen"
                    x-transition
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6"
                >
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                        Reset Kata Sandi Pengguna
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">
                        Masukkan kata sandi baru untuk <strong x-text="selectedUser"></strong>. Sesuai UC-22 & UC-02.
                    </p>

                    <form :action="resetAction" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="modal_password" class="block text-xs font-medium text-gray-700">Kata Sandi Baru</label>
                            <input type="password" name="password" id="modal_password" required minlength="8" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="modal_password_confirmation" class="block text-xs font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="modal_password_confirmation" required minlength="8" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="resetModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-md">
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
