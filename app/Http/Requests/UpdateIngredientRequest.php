<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        return $user !== null && in_array($user->role, [UserRole::Admin, UserRole::Gudang], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Ingredient|string|int|null $ingredient */
        $ingredient = $this->route('ingredient');
        $ingredientId = $ingredient instanceof Ingredient ? $ingredient->id : $ingredient;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients', 'name')->ignore($ingredientId),
            ],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama bahan baku',
            'category' => 'kategori bahan',
            'unit' => 'satuan unit',
            'minimum_stock' => 'stok minimum',
            'current_stock' => 'stok saat ini',
            'notes' => 'catatan',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.unique' => 'Nama bahan baku sudah terdaftar dalam sistem, silakan gunakan nama lain.',
            'name.max' => 'Nama bahan baku maksimal 255 karakter.',
            'unit.required' => 'Satuan ukuran bahan baku wajib dipilih.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
            'minimum_stock.numeric' => 'Batas stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Batas stok minimum tidak boleh bernilai negatif (minimal 0).',
            'current_stock.numeric' => 'Saldo stok awal harus berupa angka.',
            'current_stock.min' => 'Saldo stok awal tidak boleh bernilai negatif (minimal 0).',
        ];
    }
}
