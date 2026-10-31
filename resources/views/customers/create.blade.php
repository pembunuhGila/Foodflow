<x-app-layout>
    @php
    $inputClass = 'block w-full rounded-input border border-line bg-white px-3 py-2.5 text-body text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    $labelClass = 'mb-1.5 block text-label uppercase text-muted';
    @endphp

    {{-- Header: breadcrumb + judul, tombol kembali di kanan --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 text-small font-medium" aria-label="Breadcrumb">
                <a href="{{ route('customers.index') }}" class="text-muted transition hover:text-primary">Daftar Pelanggan</a>
                <svg class="h-3.5 w-3.5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-primary">Tambah Pelanggan</span>
            </nav>

            <h1 class="mt-2 text-2xl font-semibold text-ink">Tambah Pelanggan Baru</h1>
        </div>

        <a href="{{ route('customers.index') }}"
            class="inline-flex items-center gap-2 rounded-input border border-line bg-white px-4 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-6">

        @if ($errors->any())
        <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <x-card class="w-full">
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
                @csrf

                {{-- Nama --}}
                <div>
                    <label for="name" class="{{ $labelClass }}">
                        Nama Pelanggan / Sekolah / Instansi <span class="text-danger-text">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: SDIT Insan Mandiri, PT Citra Perkasa" class="{{ $inputClass }}">
                    @error('name')<p class="mt-1 text-small text-danger-text">{{ $message }}</p>@enderror
                </div>

                {{-- Telepon & Email --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="phone" class="{{ $labelClass }}">Nomor Telepon / WhatsApp PIC</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                            placeholder="Contoh: 082112345678" class="{{ $inputClass }}">
                        @error('phone')<p class="mt-1 text-small text-danger-text">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="{{ $labelClass }}">Alamat Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="Contoh: kontak@sekolah.sch.id" class="{{ $inputClass }}">
                        @error('email')<p class="mt-1 text-small text-danger-text">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Alamat --}}
                <div>
                    <label for="address" class="{{ $labelClass }}">Alamat Pengiriman Pesanan Katering</label>
                    <textarea id="address" name="address" rows="3"
                        placeholder="Ketik alamat lengkap gedung, jalan, nomor, lantai, patokan pengiriman..."
                        class="{{ $inputClass }}">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-small text-danger-text">{{ $message }}</p>@enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label for="notes" class="{{ $labelClass }}">Catatan Khusus / Preferensi Menu / Pantangan Alergi</label>
                    <textarea id="notes" name="notes" rows="3"
                        placeholder="Contoh: Menu tanpa santan, preferensi sayur dipisah, pengantaran maksimal pukul 11:00 WIB..."
                        class="{{ $inputClass }}">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-small text-danger-text">{{ $message }}</p>@enderror
                </div>

                {{-- Status aktif (default tercentang) --}}
                <div>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center gap-2.5 text-body text-ink">
                        <input type="checkbox" name="is_active" value="1"
                            @checked(old('is_active', true))
                            class="h-4 w-4 rounded border-line text-primary focus:ring-primary">
                        Pelanggan Aktif Memesan Katering
                    </label>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center justify-end gap-3 border-t border-line pt-6">
                    <a href="{{ route('customers.index') }}"
                        class="rounded-input border border-line bg-white px-5 py-2.5 text-label uppercase text-ink transition hover:bg-primary/5">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-input bg-primary px-5 py-2.5 text-label uppercase text-white transition hover:bg-primary-dark">
                        Simpan Pelanggan
                    </button>
                </div>
            </form>
        </x-card>

    </div>
</x-app-layout>