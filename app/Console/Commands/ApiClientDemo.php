<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApiClientDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:demo 
                            {--base=http://127.0.0.1:8000 : Base URL server FoodFlow}
                            {--email=admin@foodflow.test : Email akun FoodFlow}
                            {--password=password : Password akun FoodFlow}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrasi konsumsi REST API FoodFlow secara eksternal (Materi Pertemuan 10 - Langkah 5)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseUrl = rtrim((string) $this->option('base'), '/');
        $apiUrl = "{$baseUrl}/api/v1";
        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        $this->info("Menghubungkan ke API FoodFlow: {$apiUrl} ...");

        // 1. Uji Login API
        $login = Http::acceptJson()->post("{$apiUrl}/login", [
            'email' => $email,
            'password' => $password,
        ]);

        if ($login->failed()) {
            $msg = $login->json('message') ?? $login->status();
            $this->error("Login gagal: {$msg}");
            $this->warn('Pastikan server FoodFlow berjalan (php artisan serve) dan gunakan opsi --email & --password yang valid.');

            return self::FAILURE;
        }

        $token = $login->json('token');
        $this->info("Login berhasil, token Sanctum: {$token}");

        // 2. Ambil Daftar Bahan Baku
        $ingredientsRes = Http::acceptJson()
            ->withToken($token)
            ->get("{$apiUrl}/ingredients?per_page=5");

        if ($ingredientsRes->successful()) {
            $count = count($ingredientsRes->json('data') ?? []);
            $this->info("Berhasil mengambil data bahan baku: {$count} item terambil.");
        } else {
            $this->warn('Gagal mengambil bahan baku: '.$ingredientsRes->status());
        }

        // 3. Ambil Katalog Produk
        $productsRes = Http::acceptJson()
            ->withToken($token)
            ->get("{$apiUrl}/products?per_page=5");

        if ($productsRes->successful()) {
            $count = count($productsRes->json('data') ?? []);
            $this->info("Berhasil mengambil data menu produk: {$count} menu terambil.");
        }

        // 4. Uji Pembuatan Pesanan Katering dengan Idempotency Key
        $customer = Customer::first();
        $product = Product::first();

        if ($customer && $product) {
            $idempotencyKey = (string) Str::uuid();
            $payload = [
                'customer_id' => $customer->id,
                'delivery_date' => now()->addDays(3)->format('Y-m-d'),
                'notes' => 'Pesanan demo via REST API',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 10],
                ],
            ];

            // Request 1: Buat pesanan baru
            $orderRes1 = Http::acceptJson()
                ->withToken($token)
                ->withHeaders(['Idempotency-Key' => $idempotencyKey])
                ->post("{$apiUrl}/catering-orders", $payload);

            if ($orderRes1->status() === 201) {
                $this->info('Transaksi pesanan baru tersimpan: '.json_encode($orderRes1->json('data')));

                // Request 2: Replay dengan key yang sama persis
                $orderRes2 = Http::acceptJson()
                    ->withToken($token)
                    ->withHeaders(['Idempotency-Key' => $idempotencyKey])
                    ->post("{$apiUrl}/catering-orders", $payload);

                $replayedHeader = $orderRes2->header('Idempotent-Replayed') ?? 'false';
                $this->info("Ulang request dengan key yang sama: status {$orderRes2->status()}, replayed = {$replayedHeader}");
            } else {
                $this->warn('Order API response: '.$orderRes1->status().' - '.$orderRes1->body());
            }
        }

        $this->info('Demonstrasi REST API selesai dengan sukses.');

        return self::SUCCESS;
    }
}
