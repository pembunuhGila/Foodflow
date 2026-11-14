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
     * Ekspor data modul ke Excel (CSV), Cetak PDF, atau delegasikan ke background Queue Worker.
     */
    public function export(Request $request, string $module, string $format): StreamedResponse|View|RedirectResponse
    {
        $search = $request->query('search');

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
            default => abort(404, 'Modul ekspor tidak ditemukan.'),
        };

        // Format 1: Background Queue Worker Job
        if ($format === 'queue') {
            ExportDataJob::dispatch($module, 'csv', (int) auth()->id(), $search);

            return back()->with('success', "Permintaan ekspor data {$module} telah dijadwalkan ke Background Worker.");
        }

        // Format 2: Printable PDF View
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

        // Format 3: Excel / Spreadsheet Download
        return $this->exportService->downloadExcel(
            $dataset['filename'],
            $dataset['data']['headers'],
            $dataset['data']['rows']
        );
    }
}
