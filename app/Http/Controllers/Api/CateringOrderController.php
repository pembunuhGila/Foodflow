<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CateringOrderResource;
use App\Models\CateringOrder;
use App\Models\CateringOrderDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CateringOrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan katering berpaginasi (Materi Pertemuan 10 - Langkah 3 & 7).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,confirmed,processing,delivered,cancelled'],
            'sort' => ['nullable', 'in:id,-id,order_number,-order_number,delivery_date,-delivery_date,total_amount,-total_amount'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = CateringOrder::query()
            ->with(['customer', 'creator', 'details.product']);

        if ($request->filled('q')) {
            $search = strtolower($request->string('q')->trim());
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(order_number) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $sort = $request->input('sort', '-id');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        $query->orderBy($column, $direction);

        $perPage = (int) $request->input('per_page', 15);
        $orders = $query->paginate($perPage)->withQueryString();

        return CateringOrderResource::collection($orders);
    }

    /**
     * Menampilkan detail pesanan katering beserta menu dan pemesan (Materi Pertemuan 10 - Langkah 3).
     */
    public function show(CateringOrder $cateringOrder): CateringOrderResource
    {
        $cateringOrder->load(['customer', 'creator', 'details.product']);

        return new CateringOrderResource($cateringOrder);
    }

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

        $order->load(['customer', 'creator', 'details.product']);

        return (new CateringOrderResource($order))
            ->additional(['message' => 'Pesanan katering berhasil dicatat.'])
            ->response()
            ->setStatusCode(201);
    }
}
