<?php

namespace App\Services;

use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Ekspor data ke format Excel / CSV yang kompatibel penuh dengan Microsoft Excel.
     * Menggunakan UTF-8 BOM agar aksen dan karakter Indonesia terbaca sempurna tanpa corrupt.
     *
     * @param  list<string>  $headers
     * @param  iterable<array-key, list<mixed>>  $rows
     */
    public function downloadExcel(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        $headersResponse = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, $headers);

            // Baris data
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headersResponse);
    }

    /**
     * Siapkan dataset ekspor pelanggan.
     */
    public function exportCustomers(?string $search = null): array
    {
        $query = Customer::query()->latest();

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();

        $headers = ['ID', 'Nama Pelanggan', 'No. Telepon', 'Email', 'Alamat', 'Catatan', 'Status', 'Tanggal Terdaftar'];
        $rows = [];

        foreach ($customers as $c) {
            $rows[] = [
                $c->id,
                $c->name,
                $c->phone ?? '-',
                $c->email ?? '-',
                $c->address ?? '-',
                $c->notes ?? '-',
                $c->is_active ? 'Aktif' : 'Non-Aktif',
                $c->created_at?->format('d/m/Y H:i') ?? '-',
            ];
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $customers];
    }

    /**
     * Siapkan dataset ekspor bahan baku.
     */
    public function exportIngredients(?string $search = null): array
    {
        $query = Ingredient::query()->orderBy('name');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $ingredients = $query->get();

        $headers = ['ID', 'Nama Bahan', 'Kategori', 'Satuan', 'Stok Saat Ini', 'Stok Minimum', 'Status Stok', 'Harga Beli Terakhir'];
        $rows = [];

        foreach ($ingredients as $i) {
            $status = 'Aman';
            if ($i->current_stock <= 0) {
                $status = 'Habis';
            } elseif ($i->current_stock <= $i->minimum_stock) {
                $status = 'Kritis';
            }

            $rows[] = [
                $i->id,
                $i->name,
                $i->category ?? 'Umum',
                $i->unit,
                $i->current_stock,
                $i->minimum_stock,
                $status,
                $i->last_purchase_price ? 'Rp '.number_format((float) $i->last_purchase_price, 0, ',', '.') : '-',
            ];
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $ingredients];
    }

    /**
     * Siapkan dataset ekspor pesanan katering.
     */
    public function exportCateringOrders(?string $search = null): array
    {
        $query = CateringOrder::query()->with('customer')->latest();

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->get();

        $headers = ['No. Pesanan', 'Pelanggan', 'Tanggal Kirim', 'Total Tagihan', 'Status Pesanan', 'Catatan'];
        $rows = [];

        foreach ($orders as $o) {
            $rows[] = [
                $o->order_number,
                $o->customer?->name ?? 'Pelanggan Umum',
                $o->delivery_date?->format('d/m/Y') ?? '-',
                'Rp '.number_format((float) $o->total_amount, 0, ',', '.'),
                $o->status instanceof \BackedEnum ? $o->status->value : (string) $o->status,
                $o->notes ?? '-',
            ];
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $orders];
    }

    /**
     * Siapkan dataset ekspor supplier.
     */
    public function exportSuppliers(?string $search = null): array
    {
        $query = Supplier::query()->latest();

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->get();

        $headers = ['ID', 'Nama Supplier', 'No. Telepon', 'Email', 'Alamat', 'Catatan', 'Status'];
        $rows = [];

        foreach ($suppliers as $s) {
            $rows[] = [
                $s->id,
                $s->name,
                $s->phone ?? '-',
                $s->email ?? '-',
                $s->address ?? '-',
                $s->notes ?? '-',
                $s->is_active ? 'Aktif' : 'Non-Aktif',
            ];
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $suppliers];
    }
}
