<?php

namespace App\Jobs;

use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Konfigurasi ketahanan antrean job (Materi Pertemuan 9 - Langkah 8).
     */
    public int $tries = 3;

    public array $backoff = [10, 60];

    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $module,
        public string $format,
        public ?int $userId = null,
        public ?string $search = null,
    ) {}

    /**
     * Execute the job via Laravel Queue Worker.
     * Menggunakan lazyById() chunking untuk penulisan berkas bertahap dengan konsumsi memori konstan O(1).
     */
    public function handle(ExportService $exportService): void
    {
        Log::info("Worker memulai proses latar belakang ekspor {$this->module} ({$this->format}) untuk user ID {$this->userId}");

        $config = match ($this->module) {
            'customers' => [
                'query' => $exportService->customerQuery($this->search),
                'headers' => $exportService->customerHeaders(),
                'mapper' => fn ($c) => $exportService->mapCustomer($c),
            ],
            'ingredients' => [
                'query' => $exportService->ingredientQuery($this->search),
                'headers' => $exportService->ingredientHeaders(),
                'mapper' => fn ($i) => $exportService->mapIngredient($i),
            ],
            'catering-orders' => [
                'query' => $exportService->cateringOrderQuery($this->search),
                'headers' => $exportService->cateringOrderHeaders(),
                'mapper' => fn ($o) => $exportService->mapCateringOrder($o),
            ],
            'suppliers' => [
                'query' => $exportService->supplierQuery($this->search),
                'headers' => $exportService->supplierHeaders(),
                'mapper' => fn ($s) => $exportService->mapSupplier($s),
            ],
            default => null,
        };

        if (! $config) {
            Log::warning("Modul ekspor {$this->module} tidak ditemukan oleh worker.");

            return;
        }

        $filename = "exports/{$this->module}-".now()->format('Ymd-His').'.csv';

        // Tulis CSV baris demi baris menggunakan lazyById() chunking ke file sementara
        $tempPath = tempnam(sys_get_temp_dir(), 'foodflow_export_');
        if (! $tempPath) {
            Log::error("Gagal membuat temporary file untuk ekspor {$this->module}");

            return;
        }

        $handle = fopen($tempPath, 'w');

        // UTF-8 BOM untuk Microsoft Excel
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $config['headers']);

        /** @var Builder $query */
        $query = $config['query'];
        $mapper = $config['mapper'];

        // Streaming per-chunk 100 baris dengan lazyById()
        foreach ($query->lazyById(100) as $record) {
            fputcsv($handle, $mapper($record));
        }

        fclose($handle);

        // Pindahkan berkas hasil ekspor ke storage aplikasi
        Storage::disk('local')->putFileAs('exports', new File($tempPath), basename($filename));
        @unlink($tempPath);

        Log::info("Worker berhasil menyelesaikan ekspor berkas: {$filename}");
    }

    /**
     * Penanganan kegagalan job antrean.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ExportDataJob gagal untuk modul {$this->module}: ".$exception->getMessage());
    }
}
