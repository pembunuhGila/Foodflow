<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use Illuminate\Console\Command;
use Throwable;

class BenchmarkMemoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:memory {--connection= : Koneksi database khusus (misal: sqlite, mysql, pgsql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji perbandingan pemakaian memori RAM antara get() vs lazyById() (Materi Pertemuan 9 - Langkah 4)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('========================================================================');
        $this->info('  Pengukuran Pemakaian Memori RAM: get() vs lazyById() (Pertemuan 9)');
        $this->info('========================================================================');

        $connection = $this->option('connection');
        $query = Ingredient::query();
        if ($connection) {
            $query = Ingredient::on($connection);
        }

        try {
            $count = $query->count();
        } catch (Throwable $e) {
            $this->warn("Koneksi database default tidak dapat dijangkau ({$e->getMessage()}).");
            $this->line('Menggunakan simulasi benchmark in-memory untuk demonstrasi Pertemuan 9...');
            $this->runSimulation();

            return self::SUCCESS;
        }

        $this->line("Dataset aktif: {$count} row pada tabel `ingredients`");
        $this->newLine();

        $ukur = function (string $label, callable $fn): array {
            gc_collect_cycles();
            if (function_exists('memory_reset_peak_usage')) {
                memory_reset_peak_usage();
            }
            $jumlah = $fn();
            $peakBytes = memory_get_peak_usage();
            $peakMB = round($peakBytes / 1048576, 2);

            return [
                'metode' => $label,
                'row' => $jumlah,
                'peak' => "{$peakMB} MB",
            ];
        };

        // 1. Ukur lazyById()
        $resLazy = $ukur('Ingredient::lazyById()', function () use ($query) {
            $n = 0;
            foreach ($query->clone()->lazyById(100) as $item) {
                $n++;
            }

            return $n;
        });

        // 2. Ukur get()
        $resGet = $ukur('Ingredient::get()', function () use ($query) {
            return $query->clone()->get()->count();
        });

        $this->table(
            ['Cara baca data', 'Jumlah Record', 'Peak memory (MB)'],
            [
                [$resLazy['metode'], $resLazy['row'], $resLazy['peak']],
                [$resGet['metode'], $resGet['row'], $resGet['peak']],
            ]
        );

        $this->displayConclusion();

        return self::SUCCESS;
    }

    /**
     * Jalankan simulasi jika offline / remote db tidak terhubung.
     */
    protected function runSimulation(): void
    {
        $ukur = function (string $label, callable $fn): array {
            gc_collect_cycles();
            if (function_exists('memory_reset_peak_usage')) {
                memory_reset_peak_usage();
            }
            $jumlah = $fn();
            $peakBytes = memory_get_peak_usage();
            $peakMB = round($peakBytes / 1048576, 2);

            return [
                'metode' => $label,
                'row' => $jumlah,
                'peak' => "{$peakMB} MB",
            ];
        };

        $dummyCount = 50000;

        // lazy chunk generator simulasi
        $resLazy = $ukur('Generator Chunk (lazyById)', function () use ($dummyCount) {
            $gen = function () use ($dummyCount) {
                for ($i = 1; $i <= $dummyCount; $i++) {
                    yield ['id' => $i, 'name' => "Bahan {$i}", 'unit' => 'kg', 'current_stock' => 100];
                }
            };
            $n = 0;
            foreach ($gen() as $row) {
                $n++;
            }

            return $n;
        });

        // get all array simulasi
        $resGet = $ukur('Collection Array (get)', function () use ($dummyCount) {
            $data = [];
            for ($i = 1; $i <= $dummyCount; $i++) {
                $data[] = (object) ['id' => $i, 'name' => "Bahan {$i}", 'unit' => 'kg', 'current_stock' => 100];
            }

            return count($data);
        });

        $this->newLine();
        $this->table(
            ['Cara baca data', 'Jumlah Record', 'Peak memory (MB)'],
            [
                [$resLazy['metode'], $resLazy['row'], $resLazy['peak']],
                [$resGet['metode'], $resGet['row'], $resGet['peak']],
            ]
        );

        $this->displayConclusion();
    }

    protected function displayConclusion(): void
    {
        $this->newLine();
        $this->info('Kesimpulan Kelompok FoodFlow:');
        $this->line('1. `lazyById()` / Generator membaca data dari database per-chunk kecil (100 row).');
        $this->line('   Memori server tetap datar O(1) karena model lama langsung di-garbage-collect.');
        $this->line('2. `get()` memuat seluruh record sekaligus ke dalam memory collection RAM (O(N)).');
        $this->line('   Pada dataset besar (>50.000 row), `get()` menghabiskan puluhan hingga ratusan MB');
        $this->line('   dan berisiko fatal menabrak batas `memory_limit` PHP di production.');
        $this->info('========================================================================');
    }
}
