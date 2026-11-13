<x-app-layout>
    @php
    $search = $filters['search'] ?? '';
    $status = $filters['status'] ?? '';
    $hasFilter = $search !== '' || $status !== '';
    $total = method_exists($requests, 'total') ? $requests->total() : null;

    // Tab status membawa kata kunci pencarian
    $tabLink = fn ($s) => route('admin.password-resets.index', array_filter(['search' => $search, 'status' => $s]));

    $tabs = [['label' => 'Semua', 'value' => '']];
    foreach ($statuses as $statusOption) {
    $tabs[] = ['label' => $statusOption->label(), 'value' => $statusOption->value];
    }
    foreach ($tabs as $i => $tab) {
    $tabs[$i]['href'] = $tabLink($tab['value']);
    $tabs[$i]['active'] = (string) $status === (string) $tab['value'];
    if ($tabs[$i]['active'] && $total !== null) {
    $tabs[$i]['count'] = $total;
    }
    }
    @endphp

    <x-page-header eyebrow="Pengguna" :title="__('Permintaan Pemulihan Kata Sandi')"
        subtitle="Daftar permohonan reset password dari pengguna sesuai use case UC-02.">
        <x-slot name="actions">
            @if ($pendingCount > 0)
            <x-badge variant="warning">{{ $pendingCount }} Menunggu Proses</x-badge>
            @endif
            <x-button variant="outline" :href="route('admin.users.index')">&larr; Kelola Pengguna</x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6" x-data="{
        processModalOpen: false,
        rejectModalOpen: false,
        selectedRequest: null,
        processAction: '',
        rejectAction: ''
    }" @keydown.escape.window="processModalOpen = false; rejectModalOpen = false">

        <x-flash-alert />

        <!-- Filter & Search -->
        <x-filter-bar :action="route('admin.password-resets.index')" :search="$search"
            placeholder="Cari email atau nama pengguna..."
            :tabs="$tabs" :hidden="['status' => $status]">
            @if ($hasFilter)
            <a href="{{ route('admin.password-resets.index') }}" class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
            @endif
        </x-filter-bar>

        <!-- Tabel -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Pengguna & Email</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Waktu Diajukan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Keterangan / Diproses Oleh</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($requests as $item)
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $item->user?->name ?? 'User Tidak Ditemukan' }}</div>
                                <div class="text-small text-muted">{{ $item->email }}</div>
                                <div class="mt-0.5 text-small text-muted">Role: {{ $item->user?->role?->label() ?? '-' }}</div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-small text-ink">
                                {{ $item->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                <div class="text-small text-muted">{{ $item->created_at->locale('id')->diffForHumans() }}</div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                $badgeVariant = match($item->status->value) {
                                'diajukan' => 'warning',
                                'diproses' => 'info',
                                'selesai' => 'success',
                                'gagal' => 'danger',
                                default => 'neutral',
                                };
                                @endphp
                                <x-badge :variant="$badgeVariant">{{ $item->status->label() }}</x-badge>
                            </td>

                            <td class="px-6 py-4 text-small text-muted">
                                @if ($item->processor)
                                <div class="font-medium text-ink">Oleh: {{ $item->processor->name }}</div>
                                <div class="text-small text-muted">{{ $item->processed_at?->locale('id')->translatedFormat('d M Y, H:i') }}</div>
                                @endif
                                @if ($item->admin_notes)
                                <div class="mt-1 italic text-muted">"{{ $item->admin_notes }}"</div>
                                @else
                                <div>-</div>
                                @endif
                            </td>

                            <!-- Aksi (teks, bukan tombol) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                @if ($item->isPending())
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button"
                                        @click="processModalOpen = true; selectedRequest = @js(($item->user?->name ?? '-') . ' (' . $item->email . ')'); processAction = @js(route('admin.password-resets.approve', $item))"
                                        class="text-primary transition hover:text-primary-dark">Proses & Buat Password</button>

                                    <button type="button"
                                        @click="rejectModalOpen = true; selectedRequest = @js(($item->user?->name ?? '-') . ' (' . $item->email . ')'); rejectAction = @js(route('admin.password-resets.reject', $item))"
                                        class="text-danger-text transition hover:underline">Tolak</button>
                                </div>
                                @else
                                <span class="italic text-muted">Sudah Diproses</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="key" class="mb-3 h-10 w-10 text-muted/50" />
                                    <p class="font-medium text-ink">Belum ada permohonan pemulihan kata sandi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $requests->links() }}
            </div>
            @endif
        </x-card>

        <!-- Modal: Proses & Buat Password Baru -->
        <div x-show="processModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" @click="processModalOpen = false" aria-hidden="true"></div>

            <div x-show="processModalOpen" x-transition.opacity.duration.150ms
                class="relative w-full max-w-md rounded-3xl bg-white p-7 shadow-card">
                <h3 class="font-display text-h2 text-ink">Buat Kata Sandi Baru</h3>
                <p class="mb-5 mt-1 text-small text-muted">
                    Masukkan kata sandi baru untuk <strong class="text-ink" x-text="selectedRequest"></strong> sesuai UC-02 Langkah 7.
                </p>

                <form :action="processAction" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="approve_password" value="Kata Sandi Baru" />
                        <x-text-input type="password" name="password" id="approve_password" required minlength="8" placeholder="Minimal 8 karakter" class="mt-1.5 block" />
                    </div>

                    <div>
                        <x-input-label for="approve_password_confirmation" value="Konfirmasi Kata Sandi Baru" />
                        <x-text-input type="password" name="password_confirmation" id="approve_password_confirmation" required minlength="8" placeholder="Ulangi kata sandi" class="mt-1.5 block" />
                    </div>

                    <div>
                        <x-input-label for="approve_notes" value="Catatan Admin (Opsional)" />
                        <x-textarea name="notes" id="approve_notes" rows="2" placeholder="Contoh: Password sementara telah dikirimkan via WA/Email" class="mt-1.5 block"></x-textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-line pt-5">
                        <x-button type="button" variant="outline" @click="processModalOpen = false">Batal</x-button>
                        <x-button type="submit">Konfirmasi & Aktifkan Password</x-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Tolak Permintaan -->
        <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" @click="rejectModalOpen = false" aria-hidden="true"></div>

            <div x-show="rejectModalOpen" x-transition.opacity.duration.150ms
                class="relative w-full max-w-md rounded-3xl bg-white p-7 shadow-card">
                <h3 class="font-display text-h2 text-danger-text">Tolak Permintaan Pemulihan</h3>
                <p class="mb-5 mt-1 text-small text-muted">
                    Apakah Anda yakin ingin menolak permohonan dari <strong class="text-ink" x-text="selectedRequest"></strong>?
                </p>

                <form :action="rejectAction" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="reject_notes" value="Alasan Penolakan" />
                        <x-textarea name="notes" id="reject_notes" rows="2" placeholder="Contoh: Email tidak valid atau permintaan dibatalkan" class="mt-1.5 block"></x-textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-line pt-5">
                        <x-button type="button" variant="outline" @click="rejectModalOpen = false">Batal</x-button>
                        <x-button type="submit" variant="danger">Tolak Permintaan</x-button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>