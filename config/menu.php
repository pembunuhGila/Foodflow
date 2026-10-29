<?php

/*
 * Menu sidebar per role.
 * - 'route' boleh string atau array nama route. Sidebar memakai nama pertama yang ADA.
 * - Jika tidak ada satu pun route yang ada, menu tampil redup "Segera hadir".
 * - Cek nama route asli dengan: php artisan route:list
 * - 'group' => null berarti grup "Utama" (selalu terbuka).
 */

$dashboard = ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'grid'];

return [

    'admin' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Master Data', 'items' => [
            ['label' => 'Supplier', 'route' => ['suppliers.index'], 'icon' => 'truck'],
            ['label' => 'Pelanggan', 'route' => ['customers.index'], 'icon' => 'user'],
            ['label' => 'Menu & Gizi', 'route' => ['products.index'], 'icon' => 'book'],
            ['label' => 'Bahan Baku', 'route' => ['ingredients.index'], 'icon' => 'package'],
        ]],
        ['title' => 'Penjualan', 'items' => [
            ['label' => 'Pesanan Catering', 'route' => ['catering-orders.index', 'orders.index'], 'icon' => 'cart'],
        ]],
        ['title' => 'Pengadaan', 'items' => [
            ['label' => 'Purchase Order', 'route' => ['purchase-orders.index', 'purchase-orders.create'], 'icon' => 'file'],
            ['label' => 'Penerimaan Bahan', 'route' => ['material-receipts.index'], 'icon' => 'inbox'],
        ]],
        ['title' => 'Pengguna', 'items' => [
            ['label' => 'Kelola Staf', 'route' => ['admin.users.index'], 'icon' => 'users'],
            ['label' => 'Reset Password', 'route' => ['admin.password-resets.index', 'admin.password-reset-requests.index'], 'icon' => 'key'],
        ]],
        ['title' => 'Pemantauan', 'items' => [
            ['label' => 'Monitoring Produksi', 'route' => ['owner.production-monitoring.index'], 'icon' => 'activity'],
            ['label' => 'Gizi & Laporan', 'route' => ['owner.reports.index'], 'icon' => 'printer'],
        ]],
    ],

    'gudang' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Persediaan', 'items' => [
            ['label' => 'Bahan Baku', 'route' => ['ingredients.index'], 'icon' => 'package'],
            ['label' => 'Kartu Stok', 'route' => ['stock-cards.index'], 'icon' => 'bar'],
        ]],
        ['title' => 'Permintaan', 'items' => [
            ['label' => 'Verifikasi Permintaan Bahan', 'route' => ['material-requests.index'], 'icon' => 'check'],
        ]],
        ['title' => 'Pengadaan', 'items' => [
            ['label' => 'Penerimaan Bahan', 'route' => ['material-receipts.index'], 'icon' => 'inbox'],
            ['label' => 'Purchase Order', 'route' => ['purchase-orders.index'], 'icon' => 'file'],
        ]],
    ],

    'produksi' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Dapur', 'items' => [
            ['label' => 'Jadwal Produksi', 'route' => ['production-plans.index'], 'icon' => 'clock'],
            ['label' => 'Pemakaian Bahan Aktual', 'route' => ['production-material-usage.index'], 'icon' => 'clipboard'],
        ]],
        ['title' => 'Resep', 'items' => [
            ['label' => 'Menu & Nilai Gizi', 'route' => ['products.index'], 'icon' => 'book'],
        ]],
        ['title' => 'Permintaan', 'items' => [
            ['label' => 'Permintaan Bahan', 'route' => ['material-requests.index'], 'icon' => 'clipboard'],
        ]],
    ],

    'owner' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Pemantauan', 'items' => [
            ['label' => 'Monitoring Produksi', 'route' => ['owner.production-monitoring.index'], 'icon' => 'activity'],
            ['label' => 'Monitoring Gizi', 'route' => ['owner.nutrition.index'], 'icon' => 'heart'],
            ['label' => 'Kartu Stok', 'route' => ['stock-cards.index'], 'icon' => 'bar'],
            ['label' => 'Purchase Order', 'route' => ['purchase-orders.index'], 'icon' => 'file'],
        ]],
        ['title' => 'Laporan', 'items' => [
            ['label' => 'Cetak Laporan', 'route' => ['owner.reports.index'], 'icon' => 'printer'],
        ]],
    ],

];