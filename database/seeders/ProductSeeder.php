<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Paket Nasi Beras Merah Dada Ayam Panggang',
                'description' => 'Nasi beras merah organik pulen disajikan dengan fillet dada ayam panggang rosemary, tumis buncis wortel, dan sambal matah rendah minyak.',
                'unit' => 'porsi',
                'selling_price' => 35000,
                'is_active' => true,
                'calories' => 485.00,
                'protein' => 38.50,
                'fat' => 8.50,
                'carbohydrates' => 58.00,
                'dietary_fiber' => 6.20,
            ],
            [
                'name' => 'Sup Ikan Kakap Bening Rempah Herbal',
                'description' => 'Fillet ikan kakap segar kuah bening jahe, serai, daun kemangi, dan tomat ceri segar tanpa santan.',
                'unit' => 'porsi',
                'selling_price' => 32000,
                'is_active' => true,
                'calories' => 240.00,
                'protein' => 32.00,
                'fat' => 4.20,
                'carbohydrates' => 12.50,
                'dietary_fiber' => 2.80,
            ],
            [
                'name' => 'Pepes Tahu Jamur Tiram Kukus Daun Pisang',
                'description' => 'Olahan tahu kedelai murni dan jamur tiram bumbu kunyit kemangi, dikukus higienis berbalut daun pisang.',
                'unit' => 'porsi',
                'selling_price' => 18000,
                'is_active' => true,
                'calories' => 165.00,
                'protein' => 14.20,
                'fat' => 5.10,
                'carbohydrates' => 15.00,
                'dietary_fiber' => 4.60,
            ],
            [
                'name' => 'Tumis Sayur Pelangi (Bayam, Jagung Manis, Wortel)',
                'description' => 'Kombinasi sayur bayam segar, pipilan jagung manis, dan wortel iris ditumis singkat dengan minyak zaitun extra virgin.',
                'unit' => 'porsi',
                'selling_price' => 15000,
                'is_active' => true,
                'calories' => 110.00,
                'protein' => 4.50,
                'fat' => 2.20,
                'carbohydrates' => 18.00,
                'dietary_fiber' => 6.80,
            ],
            [
                'name' => 'Salad Quinoa Campur Edamame & Dressing Lemon',
                'description' => 'Biji quinoa matang dipadu kacang edamame, selada romaine, mentimun jepang, dan dressing olive oil perasan lemon segar.',
                'unit' => 'porsi',
                'selling_price' => 28000,
                'is_active' => true,
                'calories' => 310.00,
                'protein' => 15.80,
                'fat' => 7.80,
                'carbohydrates' => 42.00,
                'dietary_fiber' => 8.50,
            ],
            [
                'name' => 'Pudding Chia Seed Susu Kedelai Stroberi',
                'description' => 'Puding biji chia direndam susu kedelai murni tanpa gula rafinasi dengan potongan buah stroberi segar.',
                'unit' => 'porsi',
                'selling_price' => 16000,
                'is_active' => true,
                'calories' => 145.00,
                'protein' => 6.20,
                'fat' => 4.00,
                'carbohydrates' => 21.00,
                'dietary_fiber' => 5.80,
            ],
            [
                'name' => 'Bento MBG Daging Sapi Lada Hitam Rendah Lemak',
                'description' => 'Irisan daging sapi gandik bumbu lada hitam segar dipadu nasi putih pandan wangi dan rebusan brokoli wortel manis.',
                'unit' => 'porsi',
                'selling_price' => 38000,
                'is_active' => true,
                'calories' => 520.00,
                'protein' => 34.00,
                'fat' => 11.20,
                'carbohydrates' => 65.00,
                'dietary_fiber' => 5.40,
            ],
            [
                'name' => 'Fillet Gurame Panggang Madu Bumbu Kunyit',
                'description' => 'Ikan gurame fillet segar dipanggang dengan olesan madu hutan alami dan perasan jeruk nipis rempah.',
                'unit' => 'porsi',
                'selling_price' => 36000,
                'is_active' => true,
                'calories' => 295.00,
                'protein' => 28.50,
                'fat' => 6.40,
                'carbohydrates' => 18.00,
                'dietary_fiber' => 2.10,
            ],
            [
                'name' => 'Tumis Brokoli Wortel Jamur Tiram Bawang Putih',
                'description' => 'Brokoli hijau renyah, wortel berastagi, dan jamur tiram ditumis cepat dengan minyak kelapa murni (VCO).',
                'unit' => 'porsi',
                'selling_price' => 16000,
                'is_active' => true,
                'calories' => 115.00,
                'protein' => 5.20,
                'fat' => 3.10,
                'carbohydrates' => 17.50,
                'dietary_fiber' => 6.20,
            ],
            [
                'name' => 'Nasi Kotak Ayam Suwir Sambal Matah Bali Sehat',
                'description' => 'Dada ayam kukus disuwir bumbu rempah matah minyak zaitun disajikan bersama nasi beras merah dan urap sayur kelapa muda.',
                'unit' => 'porsi',
                'selling_price' => 32000,
                'is_active' => true,
                'calories' => 450.00,
                'protein' => 35.00,
                'fat' => 7.80,
                'carbohydrates' => 56.00,
                'dietary_fiber' => 7.00,
            ],
            [
                'name' => 'Steak Tempe Bakar Saus Jamur Lada Hitam',
                'description' => 'Olahan tempe kedelai murni dipanggang lembut dengan saus jamur kancing homemade gurih alami.',
                'unit' => 'porsi',
                'selling_price' => 22000,
                'is_active' => true,
                'calories' => 280.00,
                'protein' => 21.00,
                'fat' => 8.50,
                'carbohydrates' => 26.00,
                'dietary_fiber' => 8.00,
            ],
            [
                'name' => 'Sup Sayur Sehat Bakso Tahu Ayam',
                'description' => 'Kuah kaldu ayam kampung bening berisi bakso tahu sutra, wortel, kembang kol, dan daun seledri segar.',
                'unit' => 'porsi',
                'selling_price' => 24000,
                'is_active' => true,
                'calories' => 210.00,
                'protein' => 19.50,
                'fat' => 4.50,
                'carbohydrates' => 16.00,
                'dietary_fiber' => 4.20,
            ],
            [
                'name' => 'Mashed Potato Kentang Dieng Creamy Non-Dairy',
                'description' => 'Puree kentang kuning dieng kukus dipadu susu kedelai segar, minyak zaitun, dan taburan parsley cincang.',
                'unit' => 'porsi',
                'selling_price' => 18000,
                'is_active' => true,
                'calories' => 190.00,
                'protein' => 4.80,
                'fat' => 3.20,
                'carbohydrates' => 35.00,
                'dietary_fiber' => 4.50,
            ],
            [
                'name' => 'Ubi Cilembu Panggang Madu Kayumanis',
                'description' => 'Ubi manis cilembu oven lembut berkaramel alami dengan taburan bubuk kayumanis aromatik.',
                'unit' => 'porsi',
                'selling_price' => 15000,
                'is_active' => true,
                'calories' => 160.00,
                'protein' => 2.40,
                'fat' => 0.50,
                'carbohydrates' => 37.00,
                'dietary_fiber' => 5.00,
            ],
            [
                'name' => 'Rolade Daging Sapi & Telur Kukus Sayur',
                'description' => 'Daging sapi cincang digulung bersama dadar telur tipis dan isian wortel buncis kukus.',
                'unit' => 'porsi',
                'selling_price' => 27000,
                'is_active' => true,
                'calories' => 265.00,
                'protein' => 24.00,
                'fat' => 9.20,
                'carbohydrates' => 14.00,
                'dietary_fiber' => 3.10,
            ],
            [
                'name' => 'Bento Sekolah MBG Nasi Kuning Rempah Telur Suwir',
                'description' => 'Nasi kuning rempah kunyit harum disajikan dengan telur dadar suwir tipis, tempe orek manis kering, dan ketimun segar.',
                'unit' => 'porsi',
                'selling_price' => 25000,
                'is_active' => true,
                'calories' => 460.00,
                'protein' => 22.00,
                'fat' => 8.20,
                'carbohydrates' => 68.00,
                'dietary_fiber' => 4.80,
            ],
            [
                'name' => 'Capcay Kuah Bening Ayam & Seafood Segar',
                'description' => 'Aneka sayuran warna-warni kembang kol, wortel, sawi putih, dan potongan dada ayam dalam kuah kaldu gurih segar.',
                'unit' => 'porsi',
                'selling_price' => 26000,
                'is_active' => true,
                'calories' => 195.00,
                'protein' => 20.00,
                'fat' => 4.10,
                'carbohydrates' => 18.50,
                'dietary_fiber' => 5.60,
            ],
            [
                'name' => 'Sayur Asem Jakarta Bening Jagung Manis',
                'description' => 'Sayur asem kuah bening asam jawa segar dengan labu siam, jagung manis pipil, kacang panjang, dan daun melinjo.',
                'unit' => 'porsi',
                'selling_price' => 14000,
                'is_active' => true,
                'calories' => 95.00,
                'protein' => 3.20,
                'fat' => 1.00,
                'carbohydrates' => 19.50,
                'dietary_fiber' => 4.90,
            ],
            [
                'name' => 'Tahu Bacem Kukus Gula Aren Tradisional',
                'description' => 'Tahu kedelai manis legit dibacem air kelapa dan gula aren asli, lalu dikukus higienis tanpa minyak jenuh.',
                'unit' => 'porsi',
                'selling_price' => 12000,
                'is_active' => true,
                'calories' => 135.00,
                'protein' => 9.50,
                'fat' => 3.40,
                'carbohydrates' => 16.00,
                'dietary_fiber' => 2.90,
            ],
            [
                'name' => 'Bubur Oat Quinoa Ayam Cincang Jahe',
                'description' => 'Bubur oatmeal dan quinoa gurih hangat dengan topping ayam rebus cincang lembut dan minyak wijen aromatik.',
                'unit' => 'porsi',
                'selling_price' => 23000,
                'is_active' => true,
                'calories' => 275.00,
                'protein' => 21.00,
                'fat' => 5.20,
                'carbohydrates' => 36.00,
                'dietary_fiber' => 6.00,
            ],
            [
                'name' => 'Edamame Rebus Garam Himalaya',
                'description' => 'Kacang kedelai edamame segar direbus empuk dengan taburan sedikit garam himalaya murni.',
                'unit' => 'porsi',
                'selling_price' => 12000,
                'is_active' => true,
                'calories' => 125.00,
                'protein' => 11.20,
                'fat' => 5.00,
                'carbohydrates' => 9.20,
                'dietary_fiber' => 5.40,
            ],
            [
                'name' => 'Sari Kedelai Dingin Daun Pandan Asli',
                'description' => 'Minuman sari kedelai murni dibuat segar tiap pagi dengan aroma wangi rebusan daun pandan alami tanpa pemanis buatan.',
                'unit' => 'porsi',
                'selling_price' => 10000,
                'is_active' => true,
                'calories' => 90.00,
                'protein' => 7.00,
                'fat' => 3.50,
                'carbohydrates' => 8.00,
                'dietary_fiber' => 1.50,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }

        // Hubungkan resep bahan baku contoh
        $ayamPanggang = Product::where('name', 'Paket Nasi Beras Merah Dada Ayam Panggang')->first();
        if ($ayamPanggang) {
            $berasMerah = Ingredient::where('name', 'Beras Merah Organik')->first();
            $dadaAyam = Ingredient::where('name', 'Dada Ayam Fillet Segar')->first();
            $minyakZaitun = Ingredient::where('name', 'Minyak Zaitun Extra Virgin')->first();
            $bawangPutih = Ingredient::where('name', 'Bawang Putih Kupas')->first();

            $syncData = [];
            if ($berasMerah) {
                $syncData[$berasMerah->id] = ['quantity_per_unit' => 0.100, 'unit' => 'kg'];
            }
            if ($dadaAyam) {
                $syncData[$dadaAyam->id] = ['quantity_per_unit' => 0.150, 'unit' => 'kg'];
            }
            if ($minyakZaitun) {
                $syncData[$minyakZaitun->id] = ['quantity_per_unit' => 0.010, 'unit' => 'liter'];
            }
            if ($bawangPutih) {
                $syncData[$bawangPutih->id] = ['quantity_per_unit' => 0.015, 'unit' => 'kg'];
            }

            if (! empty($syncData)) {
                $ayamPanggang->ingredients()->sync($syncData);
            }
        }

        $tumisSayur = Product::where('name', 'Tumis Sayur Pelangi (Bayam, Jagung Manis, Wortel)')->first();
        if ($tumisSayur) {
            $bayam = Ingredient::where('name', 'Sayur Bayam Segar')->first();
            $wortel = Ingredient::where('name', 'Wortel Berastagi')->first();
            $bawangPutih = Ingredient::where('name', 'Bawang Putih Kupas')->first();

            $syncData = [];
            if ($bayam) {
                $syncData[$bayam->id] = ['quantity_per_unit' => 1.000, 'unit' => 'ikat'];
            }
            if ($wortel) {
                $syncData[$wortel->id] = ['quantity_per_unit' => 0.100, 'unit' => 'kg'];
            }
            if ($bawangPutih) {
                $syncData[$bawangPutih->id] = ['quantity_per_unit' => 0.010, 'unit' => 'kg'];
            }

            if (! empty($syncData)) {
                $tumisSayur->ingredients()->sync($syncData);
            }
        }
    }
}
