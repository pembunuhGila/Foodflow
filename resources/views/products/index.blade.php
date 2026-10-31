<x-app-layout>
    @php
        /* ==========================================================================
           PENGATURAN: sesuaikan HANYA bagian ini dengan nama di proyekmu.
           ========================================================================== */
        $role = auth()->user()->role instanceof \BackedEnum ? auth()->user()->role->value : (string) auth()->user()->role;
        $canManage = in_array($role, ['admin', 'produksi'], true);

        $list = $products ?? $menus ?? $items ?? collect();

        $routes = [
            'index'  => ['products.index'],
            'create' => ['products.create'],
            'show'   => ['products.show'],
            'recipe' => ['products.recipe.edit', 'products.recipe.show', 'products.recipe'],
            'edit'   => ['products.edit'],
            'toggle' => ['products.toggle-status', 'products.toggle'],
            'delete' => ['products.destroy'],
        ];
        $toggleMethod = 'PATCH';
        $statusValues = ['active', 'inactive'];

        $fields = [
            'price'    => ['price', 'selling_price', 'unit_price', 'harga'],
            'calories' => ['calories', 'energy', 'energy_kcal', 'kalori'],
            'protein'  => ['protein', 'protein_g'],
            'fat'      => ['fat', 'fat_g', 'lemak'],
            'carbs'    => ['carbohydrate', 'carbohydrates', 'carbs', 'karbohidrat'],
        ];
        /* ======================== akhir pengaturan ======================== */

        $resolve = function (array $names) {
            foreach ($names as $n) {
                if (\Illuminate\Support\Facades\Route::has($n)) {
                    return $n;
                }
            }
            return null;
        };
        $r = collect($routes)->map(fn ($names) => $resolve($names))->all();

        $pick = function ($model, array $keys) {
            foreach ($keys as $k) {
                if (array_key_exists($k, $model->getAttributes()) || $model->hasGetMutator($k)) {
                    $v = $model->getAttribute($k);
                    if ($v !== null) {
                        return $v;
                    }
                }
            }
            return null;
        };
        $isActive = function ($m) {
            if (method_exists($m, 'isActive')) {
                return (bool) $m->isActive();
            }
            if (array_key_exists('is_active', $m->getAttributes())) {
                return (bool) $m->is_active;
            }
            $s = $m->status ?? null;
            $s = $s instanceof \BackedEnum ? $s->value : $s;
            return in_array($s, ['active', 'aktif', 1, '1', true], true);
        };
        $num = fn ($v, $d = 1) => number_format((float) $v, $d, ',', '.');

        $indexRoute = $r['index'] ?? 'products.index';
        $search = (string) request('search', '');
        $status = (string) request('status', '');
        $total = method_exists($list, 'total') ? $list->total() : null;
        $tabLink = fn ($s) => route($indexRoute, array_filter(['search' => $search, 'status' => $s]));
        $tabs = [
            ['label' => 'Semua',    'href' => $tabLink(''),               'active' => $status === ''],
            ['label' => 'Aktif',    'href' => $tabLink($statusValues[0]), 'active' => $status === $statusValues[0]],
            ['label' => 'Nonaktif', 'href' => $tabLink($statusValues[1]), 'active' => $status === $statusValues[1]],
        ];
        foreach ($tabs as $i => $tab) {
            if ($tab['active'] && $total !== null) {
                $tabs[$i]['count'] = $total;
            }
        }
    @endphp

    <x-page-header eyebrow="Master Data" :title="__('Master Menu Makanan & Rekap Gizi')">
        <x-slot name="actions">
            @if ($canManage && $r['create'])
                <x-button :href="route($r['create'])">
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Menu Baru
                </x-button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="space-y-6">

        <!-- Session Alert -->
        @if (session('success'))
            <div class="flex items-center gap-2 rounded-input border border-primary/20 bg-primary/10 p-4 text-body text-primary" role="status">
                <x-icon name="check" class="h-5 w-5 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text" role="alert">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter & Search Bar -->
        <x-filter-bar :action="route($indexRoute)" :search="$search"
                      placeholder="Cari nama menu..."
                      :tabs="$tabs" :hidden="['status' => $status]">
            @if ($search !== '' || $status !== '')
                <a href="{{ route($indexRoute) }}" class="text-small font-medium text-primary hover:text-primary-dark">Reset filter</a>
            @endif
        </x-filter-bar>

        <!-- Tabel Menu -->
        <x-card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-body">
                    <thead class="bg-primary/5">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Menu Masakan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Harga Satuan</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Kandungan Gizi / Porsi</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-label uppercase text-muted">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-label uppercase text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @forelse ($list as $item)
                            @php
                                $active = $isActive($item);
                                $price = $pick($item, $fields['price']);
                                $kcal  = $pick($item, $fields['calories']);
                                $prot  = $pick($item, $fields['protein']);
                                $fat   = $pick($item, $fields['fat']);
                                $carb  = $pick($item, $fields['carbs']);
                                $hasNutrition = collect([$kcal, $prot, $fat, $carb])->contains(fn ($v) => (float) $v > 0);
                            @endphp
                            <tr class="transition hover:bg-primary/5">
                                <!-- Nama menu -->
                                <td class="px-6 py-4 font-medium text-ink">{{ $item->name }}</td>

                                <!-- Harga -->
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-ink">
                                    {{ $price !== null ? 'Rp ' . $num($price, 0) : '-' }}
                                </td>

                                <!-- Gizi -->
                                <td class="px-6 py-4">
                                    @if ($hasNutrition)
                                        <div class="font-medium text-ink">{{ $num($kcal, 0) }} kkal</div>
                                        <div class="mt-0.5 text-small text-muted">
                                            Protein {{ $num($prot) }} g
                                            <span aria-hidden="true">&middot;</span>
                                            Lemak {{ $num($fat) }} g
                                            <span aria-hidden="true">&middot;</span>
                                            Karbo {{ $num($carb) }} g
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Status: Aktif violet, Nonaktif abu-abu -->
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($active)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-small font-medium text-primary">
                                            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-small font-medium text-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi (teks, bukan tombol) -->
                                <td class="whitespace-nowrap px-6 py-4 text-right text-small font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        @if ($r['show'])
                                            <a href="{{ route($r['show'], $item) }}" class="text-primary transition hover:text-primary-dark">Detail</a>
                                        @endif

                                        @if ($canManage)
                                            @if ($r['recipe'])
                                                <a href="{{ route($r['recipe'], $item) }}" class="text-primary transition hover:text-primary-dark">Resep</a>
                                            @endif
                                            @if ($r['edit'])
                                                <a href="{{ route($r['edit'], $item) }}" class="text-primary transition hover:text-primary-dark">Edit</a>
                                            @endif

                                            @if ($r['toggle'])
                                                @php
                                                    $toggleConfirm = [
                                                        'formId' => 'toggle-product-' . $item->getKey(),
                                                        'tone' => $active ? 'danger' : 'primary',
                                                        'eyebrow' => 'Ubah status menu',
                                                        'title' => $active ? 'Nonaktifkan menu ini?' : 'Aktifkan menu ini?',
                                                        'subjectLabel' => 'Menu',
                                                        'subject' => $item->name,
                                                        'message' => 'Apakah Anda yakin ingin mengubah status menu ini?',
                                                        'detail' => '',
                                                        'confirmLabel' => $active ? 'Ya, Nonaktifkan Menu' : 'Ya, Aktifkan Menu',
                                                    ];
                                                @endphp
                                                <form id="toggle-product-{{ $item->getKey() }}" method="POST" action="{{ route($r['toggle'], $item) }}" class="inline-block">
                                                    @csrf
                                                    @method($toggleMethod)
                                                    <button type="button" @click="$dispatch('open-confirm', @js($toggleConfirm))"
                                                            class="text-small font-medium transition {{ $active ? 'text-danger-text hover:underline' : 'text-primary hover:text-primary-dark' }}">
                                                        {{ $active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($r['delete'])
                                                @php
                                                    $deleteConfirm = [
                                                        'formId' => 'delete-product-' . $item->getKey(),
                                                        'tone' => 'danger',
                                                        'eyebrow' => 'Hapus menu',
                                                        'title' => 'Hapus menu ini?',
                                                        'subjectLabel' => 'Menu',
                                                        'subject' => $item->name,
                                                        'message' => 'Apakah Anda yakin ingin menghapus menu ini?',
                                                        'detail' => '',
                                                        'confirmLabel' => 'Ya, Hapus Menu',
                                                    ];
                                                @endphp
                                                <form id="delete-product-{{ $item->getKey() }}" method="POST" action="{{ route($r['delete'], $item) }}" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" @click="$dispatch('open-confirm', @js($deleteConfirm))"
                                                            class="text-small font-medium text-danger-text transition hover:underline">Hapus</button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-muted">
                                    <div class="flex flex-col items-center justify-center">
                                        <p class="font-medium text-ink">Tidak ada menu yang ditemukan.</p>
                                        <p class="mt-1 text-small text-muted">Coba sesuaikan kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (method_exists($list, 'hasPages') && $list->hasPages())
                <div class="border-t border-line bg-primary/5 px-6 py-4">
                    {{ $list->links() }}
                </div>
            @endif
        </x-card>

    </div>
</x-app-layout>