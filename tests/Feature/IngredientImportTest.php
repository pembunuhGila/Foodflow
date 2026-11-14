<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IngredientImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $produksi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'status' => UserStatus::Aktif,
            'password' => Hash::make('password'),
        ]);

        $this->produksi = User::factory()->create([
            'role' => UserRole::Produksi,
            'status' => UserStatus::Aktif,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_admin_can_import_ingredients_csv_idempotently(): void
    {
        $csvContent = "name,category,unit,minimum_stock,current_stock,price_per_unit\n".
            "Beras Ramos,Karbohidrat,kg,50,200,14000\n".
            "Daging Sapi Segar,Protein Hewani,kg,20,45,120000\n".
            "Telur Ayam Negeri,Protein Hewani,butir,100,500,2000\n";

        $file = UploadedFile::fake()->createWithContent('bahan_baku.csv', $csvContent);

        $response = $this->actingAs($this->admin)
            ->post(route('ingredients.import'), ['csv' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Beras Ramos',
            'unit' => 'kg',
            'minimum_stock' => 50,
        ]);

        $this->assertDatabaseCount('ingredients', 3);

        // Upload berkas yang sama sekali lagi (Uji Idempotensi - tidak boleh dobel)
        $fileAgain = UploadedFile::fake()->createWithContent('bahan_baku.csv', $csvContent);
        $this->actingAs($this->admin)
            ->post(route('ingredients.import'), ['csv' => $fileAgain]);

        $this->assertDatabaseCount('ingredients', 3);
    }

    public function test_import_skips_invalid_and_corrupt_rows(): void
    {
        $csvContent = "name,category,unit,minimum_stock\n".
            "Bayam Segar,Sayuran,ikat,10\n".
            "Baris Cacat Tanpa Kolom Lengkap\n".
            ",Kategori Tanpa Nama,kg,5\n".
            "Wortel Lokal,Sayuran,kg,15\n";

        $file = UploadedFile::fake()->createWithContent('campuran.csv', $csvContent);

        $response = $this->actingAs($this->admin)
            ->post(route('ingredients.import'), ['csv' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Hanya Bayam dan Wortel yang valid
        $this->assertDatabaseCount('ingredients', 2);
        $this->assertDatabaseHas('ingredients', ['name' => 'Bayam Segar']);
        $this->assertDatabaseHas('ingredients', ['name' => 'Wortel Lokal']);
    }

    public function test_non_authorized_roles_cannot_import_ingredients(): void
    {
        $csvContent = "name,category,unit\nBahan Terlarang,Umum,kg\n";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        $response = $this->actingAs($this->produksi)
            ->post(route('ingredients.import'), ['csv' => $file]);

        $response->assertForbidden();
    }
}
