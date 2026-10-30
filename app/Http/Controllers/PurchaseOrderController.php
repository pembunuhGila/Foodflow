<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    /**
     * Menampilkan daftar seluruh purchase order pengadaan bahan baku.
     */
    public function index(Request $request): View
    {
        $query = PurchaseOrder::query()
            ->with(['vendor', 'creator'])
            ->withCount('details');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->status($request->input('status'));
        }

        if ($request->filled('order_date')) {
            $query->orderDate($request->input('order_date'));
        }

        $orders = $query->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('purchase-orders.index', [
            'orders' => $orders,
            'statuses' => PurchaseOrderStatus::cases(),
            'currentSearch' => $request->input('search'),
            'currentStatus' => $request->input('status'),
            'currentDate' => $request->input('order_date'),
        ]);
    }

    /**
     * Menampilkan formulir pembuatan purchase order baru.
     */
    public function create(): View
    {
        $vendors = Supplier::query()
            ->active()
            ->orderBy('name')
            ->get();

        $ingredients = Ingredient::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'unit']);

        $suggestedOrderNumber = PurchaseOrder::generateOrderNumber();
        $today = Carbon::today()->format('Y-m-d');

        return view('purchase-orders.create', [
            'vendors' => $vendors,
            'ingredients' => $ingredients,
            'suggestedOrderNumber' => $suggestedOrderNumber,
            'today' => $today,
        ]);
    }

    /**
     * Menyimpan purchase order beserta detail bahan baku dalam satu transaksi DB.
     */
    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Ensure the vendor is active (validation already checks existence)
        $vendor = Supplier::where('id', $validated['vendor_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Preload active ingredients used in the order to avoid N+1 queries
        $ingredientIds = collect($validated['items'])->pluck('ingredient_id')->unique();
        $ingredients = Ingredient::whereIn('id', $ingredientIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $order = null;
        // Retry generation of unique order_number up to 3 times
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $orderNumber = PurchaseOrder::generateOrderNumber(
                    Carbon::parse($validated['order_date'])
                );

                $order = DB::transaction(function () use (
                    $request,
                    $validated,
                    $ingredients,
                    $orderNumber
                ) {
                    $purchaseOrder = PurchaseOrder::create([
                        'vendor_id' => $validated['vendor_id'],
                        'created_by' => $request->user()->id,
                        'order_number' => $orderNumber,
                        'order_date' => $validated['order_date'],
                        'expected_date' => $validated['expected_date'],
                        'status' => PurchaseOrderStatus::Draft,
                        'notes' => $validated['notes'] ?? null,
                    ]);

                    $total = 0.0;
                    foreach ($validated['items'] as $item) {
                        $ingredient = $ingredients->get($item['ingredient_id']);
                        // Safety fallback; should never happen because of validation
                        if (! $ingredient) {
                            throw new ValidationException(
                                Validator::make([], [])
                            );
                        }
                        $unit = $ingredient->unit;
                        $quantity = (float) $item['quantity'];
                        $price = (float) $item['price'];
                        $subtotal = round($quantity * $price, 2);
                        $total += $subtotal;

                        $purchaseOrder->details()->create([
                            'ingredient_id' => $item['ingredient_id'],
                            'quantity' => $quantity,
                            'unit' => $unit,
                            'price' => $price,
                            'subtotal' => $subtotal,
                        ]);
                    }

                    $purchaseOrder->total_amount = $total;
                    $purchaseOrder->save();

                    return $purchaseOrder;
                });

                break; // success, exit retry loop
            } catch (QueryException $e) {
                // If unique constraint violation on order_number, retry
                if (Str::contains($e->getMessage(), 'duplicate')) {
                    continue;
                }
                throw $e;
            }
        }

        return redirect()
            ->route('purchase-orders.show', $order)
            ->with('success', "Purchase Order {$order->order_number} berhasil dibuat.");
    }

    /**
     * Menampilkan detail purchase order beserta rincian bahan baku.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load([
            'vendor',
            'creator',
            'details.ingredient',
        ]);

        return view('purchase-orders.show', [
            'order' => $purchaseOrder,
        ]);
    }

    /**
     * Memperbarui status purchase order pengadaan (FR-20).
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::enum(PurchaseOrderStatus::class)],
        ]);

        $purchaseOrder->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->back()
            ->with('success', "Status Purchase Order {$purchaseOrder->order_number} berhasil diubah menjadi {$purchaseOrder->status->label()}.");
    }
}
