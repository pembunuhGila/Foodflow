<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CateringOrder;
use App\Models\CateringOrderDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CateringOrderController extends Controller
{
    /**
     * Membuat pesanan katering baru via REST API dengan Idempotency Key (Materi Pertemuan 10 - Langkah 4 & 8).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'customer_id.required' => 'Pelanggan pemesan wajib dipilih.',
            'items.required' => 'Daftar menu pesanan wajib diisi minimal satu menu.',
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            $productIds = collect($validated['items'])->pluck('product_id')->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                $unitPrice = (float) ($product->selling_price ?? $product->price ?? 0);
                $quantity = (int) $item['quantity'];
                $subtotal = $unitPrice * $quantity;
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $orderNumber = CateringOrder::generateOrderNumber();

            $order = CateringOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'],
                'created_by' => $request->user()?->id,
                'order_date' => Carbon::today()->format('Y-m-d'),
                'delivery_date' => $validated['delivery_date'],
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $detail) {
                $detail['catering_order_id'] = $order->id;
                CateringOrderDetail::create($detail);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Pesanan katering berhasil dicatat.',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'delivery_date' => $order->delivery_date?->format('Y-m-d'),
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
