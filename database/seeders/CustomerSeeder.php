<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'SDIT Insan Mandiri',
                'phone' => '082112345001',
                'email' => 'catering@insanmandiri.sch.id',
                'address' => 'Jl. Pendidikan Karakter No. 10, Jakarta Selatan',
                'notes' => 'Langganan menu makan siang bergizi 150 porsi anak sekolah (menu tanpa MSG berlebih).',
                'is_active' => true,
            ],
            [
                'name' => 'PT Citra Pratama Perkasa',
                'phone' => '082112345002',
                'email' => 'ga.citra@perkasa.co.id',
                'address' => 'Gedung Wisma Niaga Lt. 8, Jl. Sudirman Kav. 45, Jakarta Pusat',
                'notes' => 'Katering makan siang harian 80 box karyawan kantor setiap hari kerja.',
                'is_active' => true,
            ],
            [
                'name' => 'Klinik Rawat Inap Medika Sehat',
                'phone' => '082112345003',
                'email' => 'gizi@medikasehat.co.id',
                'address' => 'Jl. Kesehatan Raya No. 22, Bekasi Barat',
                'notes' => 'Pasokan makanan pasien rawat inap dengan spesifikasi khusus rendah garam & tinggi protein.',
                'is_active' => true,
            ],
            [
                'name' => 'Biro Kepegawaian Daerah (BKD)',
                'phone' => '082112345004',
                'email' => 'umum@bkd.daerah.go.id',
                'address' => 'Komplek Balai Kota Gedung B, Depok',
                'notes' => 'Pesanan prasmanan dan snack box berkala untuk pelatihan dinas dan rapat.',
                'is_active' => true,
            ],
            [
                'name' => 'Yayasan Lansia Kasih Ibu',
                'phone' => '082112345005',
                'email' => 'kasihibu@yayasan.org',
                'address' => 'Jl. Cemara Rimbun No. 5, Bogor Selatan',
                'notes' => 'Katering bubur dan menu lunak nutrisi seimbang untuk 45 lansia.',
                'is_active' => true,
            ],
            [
                'name' => 'SMP Negeri Unggulan 01 Malang',
                'phone' => '082112345006',
                'email' => 'mbg@smpn1malang.sch.id',
                'address' => 'Jl. Veteran No. 15, Malang, Jawa Timur',
                'notes' => 'Program Makan Bergizi Gratis (MBG) 200 porsi siswa kelas 7-9.',
                'is_active' => true,
            ],
            [
                'name' => 'SMA Brawijaya Smart School',
                'phone' => '082112345007',
                'email' => 'kantin@bssbrawijaya.sch.id',
                'address' => 'Jl. Cimanuk No. 8, Klojen, Malang',
                'notes' => 'Langganan menu sehat makan siang bento atlet muda dan olimpiade.',
                'is_active' => true,
            ],
            [
                'name' => 'Puskesmas Dinoyo Terpadu',
                'phone' => '082112345008',
                'email' => 'gizi@puskesmasdinoyo.go.id',
                'address' => 'Jl. MT Haryono No. 120, Lowokwaru, Malang',
                'notes' => 'Penyediaan PMT (Pemberian Makanan Tambahan) balita dan ibu hamil KEK.',
                'is_active' => true,
            ],
            [
                'name' => 'PT Telekomunikasi Solusi Digital',
                'phone' => '082112345009',
                'email' => 'hrd@tsdigital.co.id',
                'address' => 'Araya Business Park Blok E-3, Malang',
                'notes' => 'Katering harian makan siang sehat 60 porsi software engineer.',
                'is_active' => true,
            ],
            [
                'name' => 'Rumah Sakit Ibu & Anak Permata',
                'phone' => '082112345010',
                'email' => 'instalasi.gizi@rsia-permata.co.id',
                'address' => 'Jl. Danau Toba No. 44, Sawojajar, Malang',
                'notes' => 'Katering menu higienis pemulihan pasca bersalin.',
                'is_active' => true,
            ],
            [
                'name' => 'Kantor Perwakilan Bank Sentral Malang',
                'phone' => '082112345011',
                'email' => 'logistik@bi-malang.id',
                'address' => 'Jl. Merdeka Utara No. 7, Malang',
                'notes' => 'Pesanan prasmanan meeting eksekutif mingguan.',
                'is_active' => true,
            ],
            [
                'name' => 'SD Bintang Kejora Montessori',
                'phone' => '082112345012',
                'email' => 'info@bintangkejora.sch.id',
                'address' => 'Jl. Ijen Boulevard No. 89, Malang',
                'notes' => 'Makan siang ramah alergi gluten-free & nut-free untuk siswa SD.',
                'is_active' => true,
            ],
            [
                'name' => 'Dinas Pendidikan Kota Malang',
                'phone' => '082112345013',
                'email' => 'sekretariat@disdikmalang.go.id',
                'address' => 'Jl. Sulfat No. 2, Blimbing, Malang',
                'notes' => 'Konsumsi rapat rutin pokja kurikulum dan seminar guru.',
                'is_active' => true,
            ],
            [
                'name' => 'Panti Asuhan Kasih Sayang Sejahtera',
                'phone' => '082112345014',
                'email' => 'kasihsayang@panti.or.id',
                'address' => 'Jl. Terusan Borobudur No. 12, Malang',
                'notes' => 'Katering donatur Jumat Berkah paket nasi bergizi 100 porsi.',
                'is_active' => true,
            ],
            [
                'name' => 'Gym & Fitness Center Vitality Malang',
                'phone' => '082112345015',
                'email' => 'cafe@vitalityfitness.co.id',
                'address' => 'Jl. Soekarno Hatta Ruko Griyashanta No. 5, Malang',
                'notes' => 'Langganan menu pre-workout dan high-protein meal prep atlet gym.',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['name' => $customer['name']],
                $customer
            );
        }
    }
}
