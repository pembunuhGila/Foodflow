<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCateringOrderRequest;
use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CateringOrderController extends Controller
{
    /**
     * Menampilkan daftar seluruh pesanan katering.
     */
    public function index(Request $request): View
    {
        $query = CateringOrder::query()
            ->with(['customer', 'creator', 'details.product'])
            ->withSum('details as total_portions', 'quantity')
            ->withCount('details');

        // 1. Pencarian: No order atau nama pelanggan / nomor telepon
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
            });
        }

        // 2. Filter Status Pesanan (Mendukung kode status sistem maupun istilah bahasa Indonesia)
        if ($request->filled('status')) {
            $statusInput = strtolower(trim((string) $request->input('status')));
            $statusMap = [
                'pending' => 'pending',
                'menunggu konfirmasi' => 'pending',
                'confirmed' => 'confirmed',
                'dikonfirmasi' => 'confirmed',
                'processing' => 'processing',
                'diproses' => 'processing',
                'delivered' => 'delivered',
                'selesai' => 'delivered',
                'terkirim' => 'delivered',
                'cancelled' => 'cancelled',
                'dibatalkan' => 'cancelled',
            ];

            if (isset($statusMap[$statusInput])) {
                $query->where('status', $statusMap[$statusInput]);
            }
        }

        // 3. Filter Rentang Tanggal Kirim
        $startDate = $request->input('start_date') ?? $request->input('date_from');
        $endDate = $request->input('end_date') ?? $request->input('date_to');

        if (! empty($startDate)) {
            $query->whereDate('delivery_date', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $query->whereDate('delivery_date', '<=', $endDate);
        }

        // Backward compatibility jika tanggal dikirim dalam parameter delivery_date tunggal
        if ($request->filled('delivery_date') && empty($startDate) && empty($endDate)) {
            $query->whereDate('delivery_date', $request->input('delivery_date'));
            $startDate = $request->input('delivery_date');
            $endDate = $request->input('delivery_date');
        }

        // Ringkasan jumlah pesanan per status
        $statusCounts = [
            'all' => CateringOrder::count(),
            'pending' => CateringOrder::where('status', 'pending')->count(),
            'confirmed' => CateringOrder::where('status', 'confirmed')->count(),
            'processing' => CateringOrder::where('status', 'processing')->count(),
            'delivered' => CateringOrder::where('status', 'delivered')->count(),
            'cancelled' => CateringOrder::where('status', 'cancelled')->count(),
        ];

        $orders = $query->orderByDesc('delivery_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('catering-orders.index', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'currentSearch' => $request->input('search'),
            'currentStatus' => $request->input('status'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currentDate' => $request->input('delivery_date'),
        ]);
    }

    /**
     * Menampilkan formulir pembuatan pesanan katering baru (FR-14 & UC-07).
     * Hanya role Admin yang berwenang.
     */
    public function create(): View
    {
        $customers = Customer::query()
            ->active()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->active()
            ->orderBy('name')
            ->get();

        $suggestedOrderNumber = CateringOrder::generateOrderNumber();
        $today = Carbon::today()->format('Y-m-d');

        return view('catering-orders.create', [
            'customers' => $customers,
            'products' => $products,
            'suggestedOrderNumber' => $suggestedOrderNumber,
            'today' => $today,
        ]);
    }

    /**
     * Menyimpan data pesanan katering baru beserta rincian item menunya.
     */
    public function store(StoreCateringOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($request, $validated) {
            $productIds = collect($validated['items'])->pluck('product_id')->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);

                $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price']) && (float) $item['unit_price'] >= 0
                    ? (float) $item['unit_price']
                    : (float) $product->selling_price;

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

            $cateringOrder = CateringOrder::create([
                'order_number' => CateringOrder::generateOrderNumber(),
                'customer_id' => $validated['customer_id'],
                'created_by' => $request->user()->id,
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $itemData) {
                $cateringOrder->details()->create($itemData);
            }

            return $cateringOrder;
        });

        return redirect()
            ->route('catering-orders.show', $order)
            ->with('success', "Pesanan katering {$order->order_number} berhasil dibuat.");
    }

    /**
     * Menampilkan detail rincian pesanan katering.
     */
    public function show(CateringOrder $cateringOrder): View
    {
        $cateringOrder->load([
            'customer',
            'creator',
            'details.product',
        ]);

        return view('catering-orders.show', [
            'order' => $cateringOrder,
        ]);
    }

    /**
     * Memperbarui status pesanan katering secara cepat.
     * Hanya role Admin yang berwenang (kelola penuh).
     */
    public function updateStatus(Request $request, CateringOrder $cateringOrder): RedirectResponse
    {
        // Normalisasi input status jika dikirim dalam bahasa Indonesia atau lowercase
        $statusInput = strtolower(trim((string) $request->input('status')));
        $statusMap = [
            'pending' => 'pending',
            'menunggu konfirmasi' => 'pending',
            'confirmed' => 'confirmed',
            'dikonfirmasi' => 'confirmed',
            'processing' => 'processing',
            'diproses' => 'processing',
            'delivered' => 'delivered',
            'selesai' => 'delivered',
            'terkirim' => 'delivered',
            'cancelled' => 'cancelled',
            'dibatalkan' => 'cancelled',
        ];

        if (isset($statusMap[$statusInput])) {
            $request->merge(['status' => $statusMap[$statusInput]]);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,processing,delivered,cancelled'],
        ]);

        $oldStatusLabel = $cateringOrder->status_label;
        $cateringOrder->update([
            'status' => $validated['status'],
        ]);

        $newStatusLabel = $cateringOrder->fresh()->status_label;

        return back()->with('success', "Status pesanan {$cateringOrder->order_number} berhasil diperbarui dari \"{$oldStatusLabel}\" menjadi \"{$newStatusLabel}\".");
    }

    /**
     * Alias untuk pembaruan pesanan katering.
     */
    public function update(Request $request, CateringOrder $cateringOrder): RedirectResponse
    {
        return $this->updateStatus($request, $cateringOrder);
    }
}
