<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        return $user !== null && in_array($user->role, [UserRole::Admin, UserRole::Produksi], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Product|string|int|null $product */
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : $product;

        /** @var User|null $user */
        $user = $this->user();
        $isProduksi = $user !== null && $user->role === UserRole::Produksi;

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId),
            ],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($isProduksi) {
            $rules['calories'] = ['required', 'numeric', 'min:0'];
            $rules['protein'] = ['required', 'numeric', 'min:0'];
            $rules['fat'] = ['required', 'numeric', 'min:0'];
            $rules['carbohydrates'] = ['required', 'numeric', 'min:0'];
            $rules['dietary_fiber'] = ['nullable', 'numeric', 'min:0'];
        } else {
            // Admin tidak mengelola nilai gizi
            $rules['calories'] = ['nullable', 'numeric', 'min:0'];
            $rules['protein'] = ['nullable', 'numeric', 'min:0'];
            $rules['fat'] = ['nullable', 'numeric', 'min:0'];
            $rules['carbohydrates'] = ['nullable', 'numeric', 'min:0'];
            $rules['dietary_fiber'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama menu / produk',
            'description' => 'deskripsi menu',
            'unit' => 'satuan porsi',
            'selling_price' => 'harga jual',
            'is_active' => 'status aktif',
            'calories' => 'kandungan kalori (kkal)',
            'protein' => 'kandungan protein (gram)',
            'fat' => 'kandungan lemak (gram)',
            'carbohydrates' => 'kandungan karbohidrat (gram)',
            'dietary_fiber' => 'serat pangan (gram)',
        ];
    }
}
