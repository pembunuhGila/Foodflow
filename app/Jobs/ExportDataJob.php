<?php

namespace App\Jobs;

use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     */
    public function handle(ExportService $exportService): void
    {
        Log::info("Worker memulai proses latar belakang ekspor {$this->module} ({$this->format}) untuk user ID {$this->userId}");

        $result = match ($this->module) {
            'customers' => $exportService->exportCustomers($this->search),
            'ingredients' => $exportService->exportIngredients($this->search),
            'catering-orders' => $exportService->exportCateringOrders($this->search),
            'suppliers' => $exportService->exportSuppliers($this->search),
            default => null,
        };

        if (! $result) {
            Log::warning("Modul ekspor {$this->module} tidak ditemukan oleh worker.");

            return;
        }

        $filename = "exports/{$this->module}-".now()->format('Ymd-His').'.csv';

        // Format baris CSV
        $content = "\xEF\xBB\xBF";
        $content .= implode(',', array_map(fn ($h) => '"'.str_replace('"', '""', $h).'"', $result['headers']))."\n";

        foreach ($result['rows'] as $row) {
            $content .= implode(',', array_map(fn ($col) => '"'.str_replace('"', '""', (string) $col).'"', $row))."\n";
        }

        Storage::disk('local')->put($filename, $content);

        Log::info("Worker berhasil menyelesaikan ekspor berkas: {$filename}");
    }
}
