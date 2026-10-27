<?php

namespace App\Services;

use App\Enums\MaterialCondition;
use App\Enums\PurchaseOrderStatus;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\MaterialReceipt;
use App\Models\MaterialReceiptDetail;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MaterialReceiptService
{
    /**
     * Menyimpan penerimaan bahan baku supplier dan mencatatkan mutasi stok masuk secara atomic.
     *
     * @param array{
     *     purchase_order_id?: int|null,
     *     receipt_date: string,
     *     notes?: string|null,
     *     items: list<array{
     *         ingredient_id: int,
     *         quantity: float|int|numeric-string,
     *         unit: string,
     *         condition: string,
     *         notes?: string|null
     *     }>
     * } $data
     */
    public function recordReceipt(array $data, User $receiver): MaterialReceipt
    {
        return DB::transaction(function () use ($data, $receiver) {
            $receiptDate = Carbon::parse($data['receipt_date']);
            $receiptNumber = MaterialReceipt::generateReceiptNumber($receiptDate);

            $receipt = MaterialReceipt::create([
                'purchase_order_id' => ! empty($data['purchase_order_id']) ? (int) $data['purchase_order_id'] : null,
                'received_by' => $receiver->id,
                'receipt_number' => $receiptNumber,
                'receipt_date' => $receiptDate->format('Y-m-d'),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $condition = MaterialCondition::tryFrom($item['condition']) ?? MaterialCondition::Baik;
                $quantity = (float) $item['quantity'];

                $detail = MaterialReceiptDetail::create([
                    'material_receipt_id' => $receipt->id,
                    'ingredient_id' => (int) $item['ingredient_id'],
                    'quantity' => $quantity,
                    'unit' => $item['unit'],
                    'condition' => $condition,
                    'notes' => $item['notes'] ?? null,
                ]);

                // BR-04: Hanya bahan berstatus Baik yang menambah saldo stok tersedia
                if ($condition->isAccepted() && $quantity > 0) {
                    IngredientStockTransaction::create([
                        'ingredient_id' => $detail->ingredient_id,
                        'transaction_type' => 'in',
                        'quantity' => $quantity,
                        'transaction_date' => $receipt->receipt_date->format('Y-m-d'),
                        'reference_type' => 'material_receipt',
                        'reference_id' => $receipt->id,
                        'notes' => 'Penerimaan bahan masuk dari supplier ('.$receipt->receipt_number.')',
                        'created_by' => $receiver->id,
                    ]);

                    // Update current_stock pada tabel ingredients
                    $ingredient = Ingredient::lockForUpdate()->find($detail->ingredient_id);
                    if ($ingredient) {
                        $newStock = ((float) ($ingredient->current_stock ?? 0)) + $quantity;
                        $ingredient->update(['current_stock' => $newStock]);
                    }
                }
            }

            // Jika penerimaan ini merujuk ke Purchase Order, periksa apakah status PO dapat diubah menjadi received
            if ($receipt->purchase_order_id) {
                $po = PurchaseOrder::with('details')->find($receipt->purchase_order_id);
                if ($po && $po->status !== PurchaseOrderStatus::Cancelled) {
                    $po->update(['status' => PurchaseOrderStatus::Received]);
                }
            }

            return $receipt;
        });
    }
}
