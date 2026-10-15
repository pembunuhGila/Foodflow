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
