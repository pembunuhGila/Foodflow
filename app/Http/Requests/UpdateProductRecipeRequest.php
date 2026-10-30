<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRecipeRequest extends FormRequest
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
        return [
            'items' => ['nullable', 'array'],
            'items.*.ingredient_id' => ['required', 'distinct', 'exists:ingredients,id'],
            'items.*.quantity_per_unit' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['required', 'string', 'max:50'],
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
            'items' => 'komposisi bahan resep',
            'items.*.ingredient_id' => 'bahan baku',
            'items.*.quantity_per_unit' => 'takaran bahan per porsi',
            'items.*.unit' => 'satuan takaran',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.*.ingredient_id.distinct' => 'Bahan baku tidak boleh dipilih ganda dalam satu komposisi resep.',
            'items.*.ingredient_id.exists' => 'Bahan baku yang dipilih tidak valid atau tidak terdaftar di database.',
            'items.*.quantity_per_unit.gt' => 'Takaran bahan baku harus lebih besar dari 0.',
        ];
    }
}
