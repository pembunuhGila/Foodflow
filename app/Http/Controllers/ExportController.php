<?php

namespace App\Http\Controllers;

use App\Jobs\ExportDataJob;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        protected ExportService $exportService
    ) {}

    /**
     * Ekspor data modul ke CSV (Streaming lazyById), Cetak PDF, atau delegasikan ke background Queue Worker.
     * Mengimplementasikan materi Pertemuan 9 (Langkah 3: Streaming CSV O(1), Langkah 8: Queue Worker).
     */
    public function export(Request $request, string $module, string $format): StreamedResponse|View|RedirectResponse
    {
        $search = $request->query('search');

        // Validasi modul yang didukung
        if (! in_array($module, ['customers', 'ingredients', 'catering-orders', 'suppliers'], true)) {
            abort(404, 'Modul ekspor tidak ditemukan.');
        }

        // Format 1: Background Queue Worker Job (Materi Pertemuan 9 - Langkah 8)
        if ($format === 'queue') {
            ExportDataJob::dispatch($module, 'csv', (int) auth()->id(), $search);

            return back()->with('success', "Permintaan ekspor data {$module} telah dijadwalkan ke Background Worker.");
        }

        // Format 2: Streaming CSV via lazyById() generator (Materi Pertemuan 9 - Langkah 3 & 4)
        if ($format === 'csv') {
            return match ($module) {
                'customers' => $this->exportService->streamCsv(
                    'data-pelanggan-'.now()->format('Ymd'),
                    $this->exportService->customerQuery($search),
                    $this->exportService->customerHeaders(),
                    fn ($c) => $this->exportService->mapCustomer($c)
                ),
                'ingredients' => $this->exportService->streamCsv(
                    'stok-bahan-baku-'.now()->format('Ymd'),
                    $this->exportService->ingredientQuery($search),
                    $this->exportService->ingredientHeaders(),
                    fn ($i) => $this->exportService->mapIngredient($i)
                ),
                'catering-orders' => $this->exportService->streamCsv(
                    'pesanan-katering-'.now()->format('Ymd'),
                    $this->exportService->cateringOrderQuery($search),
                    $this->exportService->cateringOrderHeaders(),
                    fn ($o) => $this->exportService->mapCateringOrder($o)
                ),
                'suppliers' => $this->exportService->streamCsv(
                    'data-supplier-'.now()->format('Ymd'),
                    $this->exportService->supplierQuery($search),
                    $this->exportService->supplierHeaders(),
                    fn ($s) => $this->exportService->mapSupplier($s)
                ),
            };
        }

        // Siapkan dataset untuk PDF & Excel
        $dataset = match ($module) {
            'customers' => [
                'title' => 'Laporan Rekapitulasi Data Pelanggan',
                'filename' => 'data-pelanggan-'.now()->format('Ymd'),
                'data' => $this->exportService->exportCustomers($search),
            ],
            'ingredients' => [
                'title' => 'Laporan Rekapitulasi Stok Bahan Baku Gudang',
                'filename' => 'stok-bahan-baku-'.now()->format('Ymd'),
                'data' => $this->exportService->exportIngredients($search),
            ],
            'catering-orders' => [
                'title' => 'Laporan Rekapitulasi Pesanan Katering',
                'filename' => 'pesanan-katering-'.now()->format('Ymd'),
                'data' => $this->exportService->exportCateringOrders($search),
            ],
            'suppliers' => [
                'title' => 'Laporan Rekapitulasi Mitra Supplier',
                'filename' => 'data-supplier-'.now()->format('Ymd'),
                'data' => $this->exportService->exportSuppliers($search),
            ],
        };

        // Format 3: Printable PDF View (Materi Pertemuan 9 - Langkah 6)
        if ($format === 'pdf') {
            return view('exports.print', [
                'title' => $dataset['title'],
                'module' => $module,
                'headers' => $dataset['data']['headers'],
                'rows' => $dataset['data']['rows'],
                'printedAt' => now(),
                'user' => auth()->user(),
            ]);
        }

        // Format 4: Excel / Spreadsheet Download (Materi Pertemuan 9 - Langkah 5)
        return $this->exportService->downloadExcel(
            $dataset['filename'],
            $dataset['data']['headers'],
            $dataset['data']['rows']
        );
    }
}
