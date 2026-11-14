<?php

namespace App\Jobs;

use App\Models\Ingredient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProcessIngredientImport implements ShouldQueue
{
    use Queueable;

    /**
     * Konfigurasi ketahanan antrean job (Materi Pertemuan 9 - Langkah 8).
     */
    public int $tries = 3;

    public array $backoff = [10, 60];

    public int $timeout = 60;

    public function __construct(
        public string $csvPath,
        public ?int $userId = null
    ) {}

    /**
     * Eksekusi import bahan baku di latar belakang secara idempotent.
     */
    public function handle(): void
    {
        $fullPath = Storage::path($this->csvPath);

        if (! file_exists($fullPath)) {
            Log::warning("File CSV import tidak ditemukan: {$fullPath}");

            return;
        }

        $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            Storage::delete($this->csvPath);

            return;
        }

        $rows = array_map('str_getcsv', $lines);
        $header = array_shift($rows);

        // Bersihkan header dari spasi & BOM
        $header = array_map(fn ($h) => trim(str_replace("\xEF\xBB\xBF", '', (string) $h)), $header);

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            if (count($row) !== count($header)) {
                continue; // Lewati baris cacat / kolom tidak sesuai
            }

            $rowData = array_combine($header, $row);

            $validator = Validator::make($rowData, [
                'name' => ['required', 'string', 'max:255'],
                'category' => ['nullable', 'string', 'max:100'],
                'unit' => ['required', 'string', 'max:50'],
                'minimum_stock' => ['nullable', 'numeric', 'min:0'],
                'current_stock' => ['nullable', 'numeric', 'min:0'],
                'price_per_unit' => ['nullable', 'numeric', 'min:0'],
            ]);

            if ($validator->fails()) {
                continue; // Lewati baris tidak valid tanpa menghentikan batch
            }

            $validated = $validator->validated();

            $ingredient = Ingredient::updateOrCreate(
                ['name' => trim($validated['name'])],
                [
                    'category' => ! empty($validated['category']) ? trim($validated['category']) : 'Umum',
                    'unit' => trim($validated['unit']),
                    'minimum_stock' => isset($validated['minimum_stock']) ? (float) $validated['minimum_stock'] : 0,
                    'current_stock' => isset($validated['current_stock']) ? (float) $validated['current_stock'] : 0,
                    'price_per_unit' => isset($validated['price_per_unit']) ? (float) $validated['price_per_unit'] : null,
                    'is_active' => true,
                ]
            );

            $ingredient->wasRecentlyCreated ? $created++ : $updated++;
        }

        Storage::delete($this->csvPath);

        Log::info("ProcessIngredientImport selesai: {$created} baru, {$updated} diperbarui (User ID: {$this->userId})");
    }

    /**
     * Penanganan kegagalan job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessIngredientImport gagal: '.$exception->getMessage());
        Storage::delete($this->csvPath);
    }
}
