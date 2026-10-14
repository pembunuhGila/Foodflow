<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            [
                'name' => 'Beras Merah Organik',
                'category' => 'Karbohidrat',
                'unit' => 'kg',
                'minimum_stock' => 25.00,
                'calories' => 356.00,
                'protein' => 7.50,
                'fat' => 2.70,
                'carbohydrates' => 76.20,
                'notes' => 'Beras pecah kulit kualitas premium untuk diet diabetes.',
                'is_active' => true,
            ],
            [
                'name' => 'Dada Ayam Fillet Segar',
                'category' => 'Protein Hewani',
                'unit' => 'kg',
                'minimum_stock' => 20.00,
                'calories' => 165.00,
                'protein' => 31.00,
                'fat' => 3.60,
                'carbohydrates' => 0.00,
                'notes' => 'Ayam broiler tanpa kulit dan lemak berlebih.',
                'is_active' => true,
            ],
            [
                'name' => 'Telur Ayam Ras',
                'category' => 'Protein Hewani',
                'unit' => 'butir',
                'minimum_stock' => 100.00,
                'calories' => 74.00,
                'protein' => 6.30,
                'fat' => 5.00,
                'carbohydrates' => 0.40,
                'notes' => 'Telur grade A bersih tanpa retak.',
                'is_active' => true,
            ],
            [
                'name' => 'Minyak Zaitun Extra Virgin',
                'category' => 'Bumbu & Minyak',
                'unit' => 'liter',
                'minimum_stock' => 5.00,
                'calories' => 884.00,
                'protein' => 0.00,
                'fat' => 100.00,
                'carbohydrates' => 0.00,
                'notes' => 'Untuk dressing salad dan saute rendah kalori.',
                'is_active' => true,
            ],
            [
                'name' => 'Tempe Kedelai Murni',
                'category' => 'Protein Nabati',
                'unit' => 'kg',
                'minimum_stock' => 10.00,
                'calories' => 193.00,
                'protein' => 19.00,
                'fat' => 11.00,
                'carbohydrates' => 9.00,
                'notes' => 'Tempe non-GMO kemasan daun.',
                'is_active' => true,
            ],
            [
                'name' => 'Sayur Bayam Segar',
                'category' => 'Sayuran',
                'unit' => 'ikat',
                'minimum_stock' => 30.00,
                'calories' => 23.00,
                'protein' => 2.90,
                'fat' => 0.40,
                'carbohydrates' => 3.60,
                'notes' => 'Bayam hidroponik bebas pestisida.',
                'is_active' => true,
            ],
            [
                'name' => 'Wortel Berastagi',
                'category' => 'Sayuran',
                'unit' => 'kg',
                'minimum_stock' => 15.00,
                'calories' => 41.00,
                'protein' => 0.90,
                'fat' => 0.20,
                'carbohydrates' => 9.60,
                'notes' => 'Wortel manis segar kaya vitamin A.',
                'is_active' => true,
            ],
            [
                'name' => 'Bawang Putih Kupas',
                'category' => 'Bumbu & Minyak',
                'unit' => 'kg',
                'minimum_stock' => 8.00,
                'calories' => 149.00,
                'protein' => 6.40,
                'fat' => 0.50,
                'carbohydrates' => 33.10,
                'notes' => 'Bawang putih kating bersih siap olah.',
                'is_active' => true,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }
    }
}
