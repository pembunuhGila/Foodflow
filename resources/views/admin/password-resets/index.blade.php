<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Permintaan Pemulihan Kata Sandi') }}
                    </h2>
                    @if ($pendingCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 animate-pulse">
                            {{ $pendingCount }} Menunggu Proses
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Daftar permohonan reset password dari pengguna sesuai use case UC-02.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                &larr; Kelola Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        processModalOpen: false,
        rejectModalOpen: false,
        selectedRequest: null,
        processAction: '',
        rejectAction: ''
    }">
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

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.password-resets.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Cari Email / Nama</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik email atau nama user..." class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Status Permintaan</label>
                        <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Semua Status --</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}" {{ ($filters['status'] ?? '') === $statusOption->value ? 'selected' : '' }}>
                                    {{ $statusOption->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm transition">
                            Terapkan Filter
                        </button>
                        @if (!empty($filters['search']) || !empty($filters['status']))
                            <a href="{{ route('admin.password-resets.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Pengguna & Email</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Waktu Diajukan</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-left font-semibold text-gray-700">Keterangan / Diproses Oleh</th>
                                <th scope="col" class="px-6 py-3.5 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($requests as $item)
                                <tr class="hover:bg-gray-50/75 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $item->user?->name ?? 'User Tidak Ditemukan' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->email }}</div>
                                        <span class="inline-block mt-1 text-[11px] px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">
                                            Role: {{ $item->user?->role?->label() ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $item->created_at->format('d M Y, H:i') }}
                                        <div class="text-[11px] text-gray-400">{{ $item->created_at->diffForHumans() }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badgeClass = match($item->status->value) {
                                                'diajukan' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'diproses' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'gagal' => 'bg-red-100 text-red-800 border-red-200',
                                                default => 'bg-gray-100 text-gray-800 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                            {{ $item->status->label() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        @if ($item->processor)
                                            <div class="font-medium text-gray-800">Oleh: {{ $item->processor->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $item->processed_at?->format('d M Y, H:i') }}</div>
                                        @endif
                                        @if ($item->admin_notes)
                                            <div class="mt-1 italic text-gray-500 text-[11px]">"{{ $item->admin_notes }}"</div>
                                        @else
                                            <div class="text-gray-400">-</div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        @if ($item->isPending())
                                            <!-- Tombol Proses & Buat Password Baru -->
                                            <button
                                                type="button"
                                                @click="processModalOpen = true; selectedRequest = '{{ $item->user?->name }} ({{ $item->email }})'; processAction = '{{ route('admin.password-resets.approve', $item) }}'"
                                                class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700 shadow-sm transition"
                                            >
                                                Proses & Buat Password
                                            </button>

                                            <!-- Tombol Tolak -->
                                            <button
                                                type="button"
                                                @click="rejectModalOpen = true; selectedRequest = '{{ $item->user?->name }} ({{ $item->email }})'; rejectAction = '{{ route('admin.password-resets.reject', $item) }}'"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded hover:bg-red-100 transition"
                                            >
                                                Tolak
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sudah Diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Belum ada permohonan pemulihan kata sandi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Alpine.js Modal: Proses & Buat Password Baru -->
        <div x-show="processModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="processModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="processModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="processModalOpen" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900">
                        Buat Kata Sandi Baru
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">
                        Masukkan kata sandi baru untuk <strong x-text="selectedRequest"></strong> sesuai UC-02 Langkah 7.
                    </p>

                    <form :action="processAction" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="approve_password" class="block text-xs font-medium text-gray-700">Kata Sandi Baru</label>
                            <input type="password" name="password" id="approve_password" required minlength="8" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Minimal 8 karakter">
                        </div>

                        <div>
                            <label for="approve_password_confirmation" class="block text-xs font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="approve_password_confirmation" required minlength="8" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ulangi kata sandi">
                        </div>

                        <div>
                            <label for="approve_notes" class="block text-xs font-medium text-gray-700">Catatan Admin (Opsional)</label>
                            <textarea name="notes" id="approve_notes" rows="2" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Password sementara telah dikirimkan via WA/Email"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="processModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md">
                                Konfirmasi & Aktifkan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Alpine.js Modal: Tolak Permintaan -->
        <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="rejectModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="rejectModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="rejectModalOpen" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900 text-red-600">
                        Tolak Permintaan Pemulihan
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">
                        Apakah Anda yakin ingin menolak permohonan dari <strong x-text="selectedRequest"></strong>?
                    </p>

                    <form :action="rejectAction" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="reject_notes" class="block text-xs font-medium text-gray-700">Alasan Penolakan</label>
                            <textarea name="notes" id="reject_notes" rows="2" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Email tidak valid atau permintaan dibatalkan"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                                Tolak Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
