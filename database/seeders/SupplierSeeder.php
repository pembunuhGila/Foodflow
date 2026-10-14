<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'CV Beras Tani Makmur',
                'phone' => '081234567801',
                'email' => 'tani.makmur@supplier.id',
                'address' => 'Jl. Raya Pertanian No. 12, Karawang, Jawa Barat',
                'notes' => 'Pemasok utama beras merah organik dan beras putih premium.',
                'is_active' => true,
            ],
            [
                'name' => 'PT Unggas Sejahtera Nusantara',
                'phone' => '081234567802',
                'email' => 'unggas.sejahtera@supplier.id',
                'address' => 'Kawasan Industri Peternakan Blok C-4, Bogor',
                'notes' => 'Pemasok daging ayam broiler fillet tanpa kulit dan telur ayam ras grade A.',
                'is_active' => true,
            ],
            [
                'name' => 'CV Sayur Segar Hidroponik',
                'phone' => '081234567803',
                'email' => 'sayur.segar@supplier.id',
                'address' => 'Jl. Kebun Sejuk No. 88, Lembang, Bandung Barat',
                'notes' => 'Pemasok bayam, wortel, brokoli, dan sayuran organik bersertifikat.',
                'is_active' => true,
            ],
            [
                'name' => 'PT Pangan Sehat Lestari',
                'phone' => '081234567804',
                'email' => 'pangan.lestari@supplier.id',
                'address' => 'Jl. Industri Rungkut No. 45, Surabaya',
                'notes' => 'Pemasok minyak zaitun extra virgin, kedelai non-GMO, dan tempe murni.',
                'is_active' => true,
            ],
            [
                'name' => 'UD Rempah Nusantara',
                'phone' => '081234567805',
                'email' => 'rempah.nusantara@supplier.id',
                'address' => 'Pasar Induk Kramat Jati Kios B-12, Jakarta Timur',
                'notes' => 'Pemasok aneka bawang kupas, garam dapur beriodium, dan bumbu rempah tradisional.',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }
    }
}
