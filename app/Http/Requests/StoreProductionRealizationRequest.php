<?php

namespace App\Http\Requests;

use App\Enums\ProductionPlanStatus;
use App\Models\ProductionPlan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductionRealizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        // Hanya role produksi dan admin yang berwenang
        return in_array($user->role?->value, ['admin', 'produksi'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'production_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.detail_id' => ['required', 'integer', 'exists:production_plan_details,id'],
            'items.*.actual_quantity' => ['required', 'integer', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
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
            'production_date' => 'tanggal produksi',
            'notes' => 'catatan',
            'items' => 'daftar menu realisasi',
            'items.*.detail_id' => 'detail menu',
            'items.*.actual_quantity' => 'jumlah porsi aktual',
            'items.*.notes' => 'catatan menu',
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
            'items.required' => 'Setidaknya harus ada satu menu yang dicatat realisasinya.',
            'items.*.actual_quantity.required' => 'Jumlah porsi aktual wajib diisi.',
            'items.*.actual_quantity.integer' => 'Jumlah porsi aktual harus berupa bilangan bulat.',
            'items.*.actual_quantity.min' => 'Jumlah porsi aktual tidak boleh bernilai negatif.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var ProductionPlan|null $plan */
            $plan = $this->route('productionPlan');

            if (! $plan) {
                $validator->errors()->add('plan', 'Rencana produksi tidak ditemukan.');

                return;
            }

            // Rencana produksi harus berstatus in_progress
            $statusValue = $plan->status instanceof ProductionPlanStatus ? $plan->status->value : $plan->status;
            if ($statusValue !== ProductionPlanStatus::InProgress->value) {
                if ($statusValue === ProductionPlanStatus::Completed->value) {
                    $validator->errors()->add('plan', 'Rencana produksi sudah selesai dan tidak dapat menerima pencatatan realisasi baru.');
                } else {
                    $validator->errors()->add('plan', 'Pencatatan realisasi hanya dapat dilakukan pada rencana produksi yang sedang berjalan (in_progress).');
                }

                return;
            }

            // Validasi bahwa seluruh detail_id adalah bagian sah dari rencana produksi ini
            $validDetailIds = $plan->details()->pluck('id')->all();
            $inputItems = $this->input('items', []);

            foreach ($inputItems as $index => $item) {
                $detailId = $item['detail_id'] ?? null;
                if ($detailId && ! in_array((int) $detailId, $validDetailIds, true)) {
                    $validator->errors()->add(
                        "items.{$index}.detail_id",
                        'Detail menu yang dipilih bukan bagian dari rencana produksi ini.'
                    );
                }
            }
        });
    }
}
