<?php
$dashboard = ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'grid'];

$kartuStok = ['label' => 'Kartu Stok', 'route' => ['stock-cards.index'], 'icon' => 'bar'];
$purchaseOrder = ['label' => 'Purchase Order', 'route' => ['purchase-orders.index', 'purchase-orders.create'], 'icon' => 'file'];
$penerimaan = ['label' => 'Penerimaan Bahan', 'route' => ['material-receipts.index'], 'icon' => 'inbox'];
$monitoring = ['label' => 'Monitoring Produksi', 'route' => ['owner.production-monitoring.index', 'owner.monitoring.index'], 'icon' => 'activity'];
$laporan = ['label' => 'Cetak Laporan', 'route' => ['owner.reports.index'], 'icon' => 'printer'];

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
        ['title' => 'Pengadaan', 'items' => [$purchaseOrder, $penerimaan, $kartuStok]],
        ['title' => 'Pengguna', 'items' => [
            ['label' => 'Kelola Staf', 'route' => ['admin.users.index'], 'icon' => 'users'],
            ['label' => 'Reset Password', 'route' => ['admin.password-resets.index', 'admin.password-reset-requests.index'], 'icon' => 'key'],
        ]],
        ['title' => 'Pemantauan', 'items' => [$monitoring, $laporan]],
    ],

    'gudang' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Persediaan', 'items' => [
            ['label' => 'Bahan Baku', 'route' => ['ingredients.index'], 'icon' => 'package'],
            $kartuStok,
        ]],
        ['title' => 'Permintaan', 'items' => [
            ['label' => 'Verifikasi Permintaan Bahan', 'route' => ['material-requests.index'], 'icon' => 'check'],
        ]],
        ['title' => 'Pengadaan', 'items' => [$penerimaan, $purchaseOrder]],
    ],

    'produksi' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Dapur', 'items' => [
            ['label' => 'Rencana Produksi', 'route' => ['production-plans.index'], 'icon' => 'clock'],
            ['label' => 'Realisasi Masak', 'route' => ['productions.index'], 'icon' => 'clipboard'],
        ]],
        ['title' => 'Resep', 'items' => [
            ['label' => 'Menu & Nilai Gizi', 'route' => ['products.index'], 'icon' => 'book'],
        ]],
        ['title' => 'Permintaan', 'items' => [
            ['label' => 'Permintaan Bahan', 'route' => ['material-requests.index'], 'icon' => 'check'],
        ]],
    ],

    'owner' => [
        ['title' => 'Utama', 'items' => [$dashboard]],
        ['title' => 'Pemantauan', 'items' => [$monitoring, $kartuStok, $purchaseOrder]],
        ['title' => 'Laporan', 'items' => [$laporan]],
    ],

];
