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
                'notes' => 'Pemasok utama beras merah organik dan beras putih pandan wangi premium.',
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
            [
                'name' => 'PT Lautan Biru Bahari',
                'phone' => '081234567806',
                'email' => 'lautan.biru@supplier.id',
                'address' => 'Pelabuhan Perikanan Samudera Nizam Zachman, Muara Baru, Jakarta Utara',
                'notes' => 'Pemasok ikan kakap fillet beku, gurame segar, dan udang kupas.',
                'is_active' => true,
            ],
            [
                'name' => 'Koperasi Peternak Sapi Perah Malang',
                'phone' => '081234567807',
                'email' => 'koperasi.sapi@supplier.id',
                'address' => 'Jl. Raya Pujon No. 45, Kabupaten Malang, Jawa Timur',
                'notes' => 'Pemasok susu murni, daging sapi gandik lokal segar potong subuh.',
                'is_active' => true,
            ],
            [
                'name' => 'CV Agri Bromo Sayur Mas',
                'phone' => '081234567808',
                'email' => 'agri.bromo@supplier.id',
                'address' => 'Jl. Raya Tosari No. 18, Pasuruan, Jawa Timur',
                'notes' => 'Pemasok kentang dieng grade A, wortel berastagi, dan kubis segar.',
                'is_active' => true,
            ],
            [
                'name' => 'Sentra Jamur Tiram Organik Sejahtera',
                'phone' => '081234567809',
                'email' => 'jamur.sejahtera@supplier.id',
                'address' => 'Desa Junrejo RT 02 RW 04, Kota Batu, Jawa Timur',
                'notes' => 'Pemasok jamur tiram putih segar petik harian.',
                'is_active' => true,
            ],
            [
                'name' => 'Pabrik Tahu Sutra Murni Sumber Rejeki',
                'phone' => '081234567810',
                'email' => 'tahu.sumberrejeki@supplier.id',
                'address' => 'Jl. Industri Kedungkandang No. 29, Malang',
                'notes' => 'Pabrik tahu kedelai higienis tanpa pewarna tekstil & formalin.',
                'is_active' => true,
            ],
            [
                'name' => 'Distributor Rempah Bawang Brebes Makmur',
                'phone' => '081234567811',
                'email' => 'bawang.brebes@supplier.id',
                'address' => 'Pasar Induk Gadang Blok D No. 8, Malang',
                'notes' => 'Penyuplai bawang merah Brebes asli dan bawang putih kating kupas.',
                'is_active' => true,
            ],
            [
                'name' => 'PT Superfood Organik Indonesia',
                'phone' => '081234567812',
                'email' => 'order@superfoodorganik.co.id',
                'address' => 'Kawasan Industri Sentul Kav. 22, Bogor',
                'notes' => 'Pemasok biji quinoa impor bersertifikat organik dan chia seeds grade A.',
                'is_active' => true,
            ],
            [
                'name' => 'CV Kelapa Hijau VCO Sehat Nusantara',
                'phone' => '081234567813',
                'email' => 'vco.sehat@supplier.id',
                'address' => 'Jl. Lingkar Selatan No. 71, Kebumen, Jawa Tengah',
                'notes' => 'Pemasok minyak kelapa murni VCO dan minyak goreng kelapa non-sawit.',
                'is_active' => true,
            ],
            [
                'name' => 'Gudang Garam Herbal & Mineral Alami',
                'phone' => '081234567814',
                'email' => 'garam.mineral@supplier.id',
                'address' => 'Pergudangan Margomulyo Permai Blok G-11, Surabaya',
                'notes' => 'Pemasok resmi garam pink himalaya food grade dan garam dapur beriodium.',
                'is_active' => true,
            ],
            [
                'name' => 'UD Tani Jagung & Palawija Jaya',
                'phone' => '081234567815',
                'email' => 'tani.palawija@supplier.id',
                'address' => 'Jl. Raya Singosari No. 102, Malang',
                'notes' => 'Pemasok jagung manis pipil segar, kacang tanah, dan edamame lokal.',
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
