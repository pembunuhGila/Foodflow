<?php

namespace App\Http\Requests;

use App\Models\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'integer', Rule::exists('vendors', 'id')->where(function ($query) {
                $query->where('is_active', true);
            })],
            'order_date' => ['required', 'date'],
            'expected_date' => ['required', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'integer', Rule::exists('ingredients', 'id')->where(function ($query) {
                $query->where('is_active', true);
            }), 'distinct'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Tambahkan validasi khusus setelah aturan standar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            foreach ($items as $index => $item) {
                $ingredient = Ingredient::find($item['ingredient_id'] ?? null);
                if (! $ingredient) {
                    continue;
                }
                $quantityRaw = $item['quantity'] ?? null;
                if ($quantityRaw === null) {
                    continue;
                }
                // Ganti koma dengan titik dan konversi ke float
                $quantity = floatval(str_replace(',', '.', $quantityRaw));
                $isDecimal = $ingredient->allowsDecimalQuantity();
                if ($isDecimal) {
                    if ($quantity < 0.1) {
                        $validator->errors()->add("items.$index.quantity", "Jumlah harus minimal 0,1 untuk satuan {$ingredient->unit}.");
                    }
                } else {
                    // Harus bilangan bulat >=1
                    if (! ctype_digit((string) $quantityRaw) || $quantity < 1) {
                        $validator->errors()->add("items.$index.quantity", "Jumlah harus bilangan bulat minimal 1 untuk satuan {$ingredient->unit}.");
                    }
                }
            }
        });
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vendor_id.required' => 'Vendor/supplier harus dipilih.',
            'vendor_id.exists' => 'Vendor yang dipilih tidak ditemukan di database.',
            'order_date.required' => 'Tanggal pesan harus diisi.',
            'order_date.date' => 'Format tanggal pesan tidak valid.',
            'expected_date.required' => 'Tanggal pengiriman yang diharapkan harus diisi.',
            'expected_date.date' => 'Format tanggal pengiriman tidak valid.',
            'expected_date.after_or_equal' => 'Tanggal pengiriman tidak boleh mendahului tanggal pesan.',
            'items.required' => 'Minimal satu bahan baku harus ditambahkan dalam purchase order.',
            'items.min' => 'Minimal satu bahan baku harus ditambahkan dalam purchase order.',
            'items.*.ingredient_id.required' => 'Bahan baku wajib dipilih.',
            'items.*.ingredient_id.exists' => 'Bahan baku yang dipilih tidak ditemukan di database.',
            'items.*.ingredient_id.distinct' => 'Bahan baku tidak boleh dipilih ganda. Silakan satukan jumlahnya dalam satu baris.',
            'items.*.quantity.required' => 'Jumlah bahan harus diisi.',
            'items.*.quantity.numeric' => 'Jumlah bahan harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah bahan harus lebih dari 0.',
            'items.*.price.required' => 'Perkiraan harga beli harus diisi.',
            'items.*.price.numeric' => 'Perkiraan harga beli harus berupa angka.',
            'items.*.price.min' => 'Perkiraan harga beli harus lebih dari 0.',
        ];
    }
}
