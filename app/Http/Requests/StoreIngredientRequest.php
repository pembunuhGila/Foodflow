<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIngredientRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255', 'unique:ingredients,name'],
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
}
