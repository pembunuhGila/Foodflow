<x-app-layout>
    @php
    $search = $filters['search'] ?? '';
    $role = $filters['role'] ?? '';
    $status = $filters['status'] ?? '';
    $hasFilter = $search !== '' || $role !== '' || $status !== '';
    $total = method_exists($users, 'total') ? $users->total() : null;

    $selectClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';

    // Tab status membawa pencarian dan peran
    $tabLink = fn ($s) => route('admin.users.index', array_filter([
    'search' => $search,
    'role' => $role,
    'status' => $s,
    ]));

    $tabs = [['label' => 'Semua', 'value' => '']];
    foreach ($statuses as $statusOption) {
    $tabs[] = ['label' => $statusOption->label(), 'value' => $statusOption->value];
    }
    foreach ($tabs as $i => $tab) {
    $tabs[$i]['active'] = (string) $status === (string) $tab['value'];
    $tabs[$i]['text'] = $tab['label'] . (($tabs[$i]['active'] && $total !== null) ? ' (' . $total . ')' : '');
    }
    @endphp

    <x-page-header eyebrow="Pengguna" :title="__('Kelola Data Pengguna & Hak Akses')"
        subtitle="Daftar pengguna dan pengelolaan hak akses sistem.">
        <x-slot name="actions">
            <x-button :href="route('admin.users.create')">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Pengguna Baru
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-6" x-data="{ resetModalOpen: false, selectedUser: null, resetAction: '' }">

        <x-flash-alert />

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Filter: pencarian + peran + tab status dalam satu card -->
        <x-card>
            <form method="GET" action="{{ route('admin.users.index') }}"
                class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 xl:grid-cols-[1fr_14rem_auto]">
                <input type="hidden" name="status" value="{{ $status }}">

                <div>
                    <label for="search" class="mb-1.5 block text-label uppercase text-muted">Cari Nama / Email</label>
                    <input id="search" type="text" name="search" value="{{ $search }}"
                        placeholder="Ketik nama atau email..." class="{{ $inputClass }}">
                </div>

                <div>
                    <label for="role" class="mb-1.5 block text-label uppercase text-muted">Peran</label>
                    <select id="role" name="role" onchange="this.form.submit()" class="{{ $selectClass }}">
                        <option value="">Semua Peran</option>
                        @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption->value }}" {{ $role === $roleOption->value ? 'selected' : '' }}>
                            {{ $roleOption->label() }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tab status: sejajar dengan Peran --}}
                <div>
                    <span class="mb-1.5 block text-label uppercase text-muted">Status</span>
                    <nav class="flex items-center gap-1 overflow-x-auto rounded-full bg-primary/5 p-1" aria-label="Filter status akun">
                        @foreach ($tabs as $tab)
                        <a href="{{ $tabLink($tab['value']) }}"
                            class="whitespace-nowrap rounded-full px-4 py-1.5 text-center text-small font-semibold transition {{ $tab['active'] ? 'bg-primary text-white shadow-sm' : 'text-ink hover:text-primary' }}">
                            {{ $tab['text'] }}
                        </a>
                        @endforeach
                    </nav>
                </div>

                @if ($hasFilter)
                <div class="xl:col-span-3">
                    <a href="{{ route('admin.users.index') }}"
                        class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                </div>
                @endif

                {{-- Enter pada kolom pencarian tetap mengirim form --}}
                <button type="submit" class="sr-only">Cari</button>
            </form>
        </x-card>

        <!-- Tabel Pengguna -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Pengguna</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Peran</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Terdaftar</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($users as $userItem)
                        <tr class="transition hover:bg-primary/5">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-ink">{{ $userItem->name }}</div>
                                <div class="text-small text-muted">{{ $userItem->email }}</div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-ink">{{ $userItem->role?->label() ?? 'Tanpa Role' }}</td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($userItem->isActive())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-small font-medium text-gray-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-muted">{{ $userItem->created_at?->format('d/m/Y, H:i') ?? '-' }}</td>

                            <!-- Aksi (teks, bukan tombol) -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $userItem) }}" class="text-primary transition hover:text-primary-dark">Edit</a>

                                    <button type="button"
                                        @click="resetModalOpen = true; selectedUser = @js($userItem->name); resetAction = @js(route('admin.users.reset-password', $userItem))"
                                        class="text-primary transition hover:text-primary-dark">Reset Password</button>

                                    @if ($userItem->id !== Auth::id())
                                    @php
                                    $toggleConfirm = [
                                    'formId' => 'toggle-user-' . $userItem->getKey(),
                                    'tone' => $userItem->isActive() ? 'danger' : 'primary',
                                    'eyebrow' => 'Ubah status akun',
                                    'title' => $userItem->isActive() ? 'Nonaktifkan akun ini?' : 'Aktifkan akun ini?',
                                    'subjectLabel' => 'Pengguna',
                                    'subject' => $userItem->name,
                                    'message' => 'Apakah Anda yakin ingin mengubah status akun ini?',
                                    'detail' => '',
                                    'confirmLabel' => $userItem->isActive() ? 'Ya, Nonaktifkan Akun' : 'Ya, Aktifkan Akun',
                                    ];
                                    @endphp
                                    <form id="toggle-user-{{ $userItem->getKey() }}" method="POST" action="{{ route('admin.users.toggle-status', $userItem) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" @click="$dispatch('open-confirm', @js($toggleConfirm))"
                                            class="text-small font-medium transition {{ $userItem->isActive() ? 'text-danger-text hover:underline' : 'text-primary hover:text-primary-dark' }}">
                                            {{ $userItem->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-muted">(Akun Anda)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted">
                                <p class="font-medium text-ink">Tidak ada data pengguna yang sesuai dengan kriteria pencarian.</p>
                                @if ($hasFilter)
                                <a href="{{ route('admin.users.index') }}" class="mt-1 inline-block text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
            <div class="border-t border-line bg-primary/5 px-6 py-4">
                {{ $users->links() }}
            </div>
            @endif
        </x-card>

        <!-- Modal reset password -->
        <div x-show="resetModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true"
            @keydown.escape.window="resetModalOpen = false">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div class="fixed inset-0 bg-black/50" @click="resetModalOpen = false" aria-hidden="true"></div>

                <div x-show="resetModalOpen" x-transition
                    class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-ink" id="modal-title">Reset Kata Sandi Pengguna</h3>
                    <p class="mb-4 mt-1 text-small text-muted">
                        Masukkan kata sandi baru untuk <span class="font-medium text-ink" x-text="selectedUser"></span>.
                    </p>

                    <form :action="resetAction" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="modal_password" class="mb-1.5 block text-label uppercase text-muted">Kata Sandi Baru</label>
                            <input type="password" name="password" id="modal_password" required minlength="8" class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="modal_password_confirmation" class="mb-1.5 block text-label uppercase text-muted">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="modal_password_confirmation" required minlength="8" class="{{ $inputClass }}">
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-line pt-4">
                            <x-button type="button" variant="outline" @click="resetModalOpen = false">Batal</x-button>
                            <x-button type="submit">Simpan Password Baru</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>