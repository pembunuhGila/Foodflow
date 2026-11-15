<?php

namespace App\Services;

use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\Supplier;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * Ekspor query ke format CSV secara streaming dengan konsumsi memori konstan O(1).
     * Menggunakan lazyById() chunking generator sesuai materi Pertemuan 9 (Langkah 3-4).
     *
     * @param  list<string>  $headers
     * @param  Closure(Model): list<mixed>  $rowMapper
     */
    public function streamCsv(string $filename, Builder $query, array $headers, Closure $rowMapper): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $headers, $rowMapper) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, $headers);

            // Chunk reading via generator lazyById() agar memori RAM server tetap datar O(1)
            foreach ($query->lazyById(100) as $record) {
                fputcsv($handle, $rowMapper($record));
            }

            fclose($handle);
        }, "{$filename}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Query builder ekspor pelanggan.
     */
    public function customerQuery(?string $search = null): Builder
    {
        $query = Customer::query()->orderBy('id');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Header kolom ekspor pelanggan.
     *
     * @return list<string>
     */
    public function customerHeaders(): array
    {
        return ['ID', 'Nama Pelanggan', 'No. Telepon', 'Email', 'Alamat', 'Catatan', 'Status', 'Tanggal Terdaftar'];
    }

    /**
     * Pemetaan baris pelanggan.
     *
     * @return list<mixed>
     */
    public function mapCustomer(Customer $c): array
    {
        return [
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

    /**
     * Siapkan dataset ekspor pelanggan.
     */
    public function exportCustomers(?string $search = null): array
    {
        $headers = $this->customerHeaders();
        $rows = [];
        $data = [];

        // Menggunakan lazyById() chunking generator untuk memetakan dataset
        foreach ($this->customerQuery($search)->lazyById(100) as $c) {
            $rows[] = $this->mapCustomer($c);
            $data[] = $c;
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $data];
    }

    /**
     * Query builder ekspor bahan baku.
     */
    public function ingredientQuery(?string $search = null): Builder
    {
        $query = Ingredient::query()->orderBy('id');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Header kolom ekspor bahan baku.
     *
     * @return list<string>
     */
    public function ingredientHeaders(): array
    {
        return ['ID', 'Nama Bahan', 'Kategori', 'Satuan', 'Stok Saat Ini', 'Stok Minimum', 'Status Stok', 'Harga Beli Terakhir'];
    }

    /**
     * Pemetaan baris bahan baku.
     *
     * @return list<mixed>
     */
    public function mapIngredient(Ingredient $i): array
    {
        $status = 'Aman';
        if ($i->current_stock <= 0) {
            $status = 'Habis';
        } elseif ($i->current_stock <= $i->minimum_stock) {
            $status = 'Kritis';
        }

        return [
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

    /**
     * Siapkan dataset ekspor bahan baku.
     */
    public function exportIngredients(?string $search = null): array
    {
        $headers = $this->ingredientHeaders();
        $rows = [];
        $data = [];

        // Menggunakan lazyById() chunking generator untuk memetakan dataset
        foreach ($this->ingredientQuery($search)->lazyById(100) as $i) {
            $rows[] = $this->mapIngredient($i);
            $data[] = $i;
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $data];
    }

    /**
     * Query builder ekspor pesanan katering.
     */
    public function cateringOrderQuery(?string $search = null): Builder
    {
        $query = CateringOrder::query()->with('customer')->orderBy('id');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    /**
     * Header kolom ekspor pesanan katering.
     *
     * @return list<string>
     */
    public function cateringOrderHeaders(): array
    {
        return ['No. Pesanan', 'Pelanggan', 'Tanggal Kirim', 'Total Tagihan', 'Status Pesanan', 'Catatan'];
    }

    /**
     * Pemetaan baris pesanan katering.
     *
     * @return list<mixed>
     */
    public function mapCateringOrder(CateringOrder $o): array
    {
        return [
            $o->order_number,
            $o->customer?->name ?? 'Pelanggan Umum',
            $o->delivery_date?->format('d/m/Y') ?? '-',
            'Rp '.number_format((float) $o->total_amount, 0, ',', '.'),
            $o->status instanceof \BackedEnum ? $o->status->value : (string) $o->status,
            $o->notes ?? '-',
        ];
    }

    /**
     * Siapkan dataset ekspor pesanan katering.
     */
    public function exportCateringOrders(?string $search = null): array
    {
        $headers = $this->cateringOrderHeaders();
        $rows = [];
        $data = [];

        // Menggunakan lazyById() chunking generator untuk memetakan dataset
        foreach ($this->cateringOrderQuery($search)->lazyById(100) as $o) {
            $rows[] = $this->mapCateringOrder($o);
            $data[] = $o;
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $data];
    }

    /**
     * Query builder ekspor supplier.
     */
    public function supplierQuery(?string $search = null): Builder
    {
        $query = Supplier::query()->orderBy('id');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Header kolom ekspor supplier.
     *
     * @return list<string>
     */
    public function supplierHeaders(): array
    {
        return ['ID', 'Nama Supplier', 'No. Telepon', 'Email', 'Alamat', 'Catatan', 'Status'];
    }

    /**
     * Pemetaan baris supplier.
     *
     * @return list<mixed>
     */
    public function mapSupplier(Supplier $s): array
    {
        return [
            $s->id,
            $s->name,
            $s->phone ?? '-',
            $s->email ?? '-',
            $s->address ?? '-',
            $s->notes ?? '-',
            $s->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }

    /**
     * Siapkan dataset ekspor supplier.
     */
    public function exportSuppliers(?string $search = null): array
    {
        $headers = $this->supplierHeaders();
        $rows = [];
        $data = [];

        // Menggunakan lazyById() chunking generator untuk memetakan dataset
        foreach ($this->supplierQuery($search)->lazyById(100) as $s) {
            $rows[] = $this->mapSupplier($s);
            $data[] = $s;
        }

        return ['headers' => $headers, 'rows' => $rows, 'data' => $data];
    }
}
