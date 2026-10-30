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
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['name' => $customer['name']],
                $customer
            );
        }
    }
}
