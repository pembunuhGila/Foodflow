<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCateringOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Hanya role Admin yang berwenang menginput pesanan katering baru (FR-14 & UC-07).
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        return $user !== null && $user->role === UserRole::Admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
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
            'customer_id.required' => 'Pelanggan pemesan wajib dipilih.',
            'customer_id.integer' => 'Data pelanggan tidak valid.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak terdaftar dalam sistem.',
            'order_date.required' => 'Tanggal pemesanan wajib diisi.',
            'order_date.date' => 'Format tanggal pemesanan tidak valid.',
            'delivery_date.required' => 'Tanggal pengiriman / acara wajib diisi.',
            'delivery_date.date' => 'Format tanggal pengiriman tidak valid.',
            'delivery_date.after_or_equal' => 'Tanggal pengiriman tidak boleh sebelum tanggal pemesanan.',
            'notes.max' => 'Catatan pesanan maksimal 1000 karakter.',
            'items.required' => 'Minimal satu menu makanan wajib ditambahkan ke pesanan katering.',
            'items.min' => 'Minimal satu menu makanan wajib ditambahkan ke pesanan katering.',
            'items.*.product_id.required' => 'Menu hidangan wajib dipilih.',
            'items.*.product_id.integer' => 'Menu hidangan tidak valid.',
            'items.*.product_id.exists' => 'Menu makanan yang dipilih tidak ditemukan di database.',
            'items.*.product_id.distinct' => 'Menu makanan tidak boleh duplikat. Satukan jumlah porsinya dalam satu baris.',
            'items.*.quantity.required' => 'Jumlah porsi wajib diisi.',
            'items.*.quantity.integer' => 'Jumlah porsi harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah porsi minimal 1 porsi.',
            'items.*.unit_price.numeric' => 'Harga satuan harus berupa angka.',
            'items.*.unit_price.min' => 'Harga satuan tidak boleh bernilai negatif.',
            'items.*.notes.max' => 'Catatan khusus menu maksimal 255 karakter.',
        ];
    }

    /**
     * Custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'pelanggan',
            'order_date' => 'tanggal pemesanan',
            'delivery_date' => 'tanggal pengiriman / acara',
            'notes' => 'catatan pesanan',
            'items' => 'daftar menu pesanan',
            'items.*.product_id' => 'menu hidangan',
            'items.*.quantity' => 'jumlah porsi',
            'items.*.unit_price' => 'harga satuan',
            'items.*.notes' => 'catatan menu',
        ];
    }
}
