<?php

namespace App\Http\Requests;

use App\Enums\ProductionPlanStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductionPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan_date' => ['required', 'date'],
            'production_date' => ['required', 'date', 'after_or_equal:plan_date'],
            'status' => ['required', new Enum(ProductionPlanStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'items.*.planned_quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plan_date.required' => 'Tanggal perencanaan harus diisi.',
            'plan_date.date' => 'Format tanggal perencanaan tidak valid.',
            'production_date.required' => 'Tanggal jadwal masak / produksi harus diisi.',
            'production_date.date' => 'Format tanggal produksi tidak valid.',
            'production_date.after_or_equal' => 'Tanggal jadwal produksi tidak boleh mendahului tanggal perencanaan.',
            'status.required' => 'Status rencana produksi wajib ditentukan.',
            'items.required' => 'Minimal satu menu hidangan harus dipilih dalam rencana produksi.',
            'items.min' => 'Minimal satu menu hidangan harus dipilih dalam rencana produksi.',
            'items.*.product_id.required' => 'Menu hidangan wajib dipilih.',
            'items.*.product_id.exists' => 'Menu hidangan yang dipilih tidak ditemukan di database.',
            'items.*.product_id.distinct' => 'Menu hidangan tidak boleh dipilih ganda. Silakan satukan porsinya dalam satu baris.',
            'items.*.planned_quantity.required' => 'Jumlah porsi wajib diisi.',
            'items.*.planned_quantity.integer' => 'Jumlah porsi harus berupa bilangan bulat.',
            'items.*.planned_quantity.min' => 'Jumlah porsi minimal 1 porsi.',
        ];
    }
}
