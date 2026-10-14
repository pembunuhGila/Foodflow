<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Requests\StoreIngredientRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IngredientValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_ingredient_validation_passes_with_valid_data(): void
    {
        $request = new StoreIngredientRequest;
        $rules = $request->rules();

        $data = [
            'name' => 'Dada Ayam Segar',
            'category' => 'Protein',
            'unit' => 'kg',
            'minimum_stock' => 10,
            'calories' => 165,
            'protein' => 31,
            'fat' => 3.6,
            'carbohydrates' => 0,
            'is_active' => true,
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->passes());
    }

    public function test_store_ingredient_validation_fails_when_required_fields_missing(): void
    {
        $request = new StoreIngredientRequest;
        $rules = $request->rules();

        $validator = Validator::make([], $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('unit', $validator->errors()->toArray());
        $this->assertArrayHasKey('minimum_stock', $validator->errors()->toArray());
    }

    public function test_only_admin_and_warehouse_are_authorized_for_ingredient_requests(): void
    {
        $admin = new User(['role' => UserRole::Admin]);
        $gudang = new User(['role' => UserRole::Gudang]);
        $produksi = new User(['role' => UserRole::Produksi]);
        $owner = new User(['role' => UserRole::Owner]);

        $request = new StoreIngredientRequest;

        $request->setUserResolver(fn () => $admin);
        $this->assertTrue($request->authorize());

        $request->setUserResolver(fn () => $gudang);
        $this->assertTrue($request->authorize());

        $request->setUserResolver(fn () => $produksi);
        $this->assertFalse($request->authorize());

        $request->setUserResolver(fn () => $owner);
        $this->assertFalse($request->authorize());
    }
}
