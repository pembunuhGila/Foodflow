<x-app-layout>
    <x-page-header eyebrow="Master Data" :title="__('Edit Supplier') . ': ' . $supplier->name">
        <x-slot name="actions">
            <x-button variant="outline" :href="route('suppliers.index')">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali ke Daftar
            </x-button>
        </x-slot>
    </x-page-header>

    <x-card class="sm:p-8">

        <!-- Alert Validasi -->
        @if ($errors->any())
        <div class="mb-6 rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
            <p class="mb-2 font-semibold">Terdapat kesalahan pada formulir:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Nama Supplier -->
                <div class="md:col-span-2">
                    <x-input-label for="name">Nama Supplier / PT / CV / Toko <span class="text-danger-text">*</span></x-input-label>
                    <x-text-input type="text" name="name" id="name" :value="old('name', $supplier->name)" required class="mt-1.5 block" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Telepon -->
                <div>
                    <x-input-label for="phone" value="Nomor Telepon / WhatsApp" />
                    <x-text-input type="text" name="phone" id="phone" :value="old('phone', $supplier->phone)" class="mt-1.5 block" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Alamat Email" />
                    <x-text-input type="email" name="email" id="email" :value="old('email', $supplier->email)" class="mt-1.5 block" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <x-input-label for="address" value="Alamat Lengkap Kantor / Gudang Supplier" />
                    <x-textarea name="address" id="address" rows="3" class="mt-1.5 block">{{ old('address', $supplier->address) }}</x-textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <x-input-label for="notes" value="Catatan Pemasokan / Spesialisasi Bahan" />
                    <x-textarea name="notes" id="notes" rows="3" class="mt-1.5 block">{{ old('notes', $supplier->notes) }}</x-textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center pt-2 md:col-span-2">
                    <label class="inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-muted/50 text-primary focus:ring-primary/20" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                        <span class="ms-2 text-body font-medium text-ink">Supplier Aktif Bekerjasama dalam Pengadaan</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-between border-t border-line pt-6">
                <!-- Tombol Hapus Aman -->
                @php
                $deleteConfirm = [
                'formId' => 'delete-supplier-form',
                'tone' => 'danger',
                'eyebrow' => 'Hapus supplier',
                'title' => 'Hapus supplier ini?',
                'subjectLabel' => 'Supplier',
                'subject' => $supplier->name,
                'message' => 'Apakah Anda yakin ingin menghapus data supplier ini?',
                'detail' => 'Jika supplier sudah memiliki transaksi pengadaan, statusnya akan otomatis dinonaktifkan tanpa menghapus riwayat.',
                'confirmLabel' => 'Ya, Hapus Supplier',
                ];
                @endphp
                <button type="button" @click="$dispatch('open-confirm', @js($deleteConfirm))" class="text-body font-medium text-danger-text transition hover:underline">
                    Hapus Supplier
                </button>

                <div class="flex items-center gap-3">
                    <x-button variant="outline" :href="route('suppliers.index')">Batal</x-button>
                    <x-button type="submit">Simpan Perubahan</x-button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-supplier-form" method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </x-card>
</x-app-layout>