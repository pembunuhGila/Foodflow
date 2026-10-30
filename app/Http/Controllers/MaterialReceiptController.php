<?php

namespace App\Http\Controllers;

use App\Enums\MaterialCondition;
use App\Enums\PurchaseOrderStatus;
use App\Models\Ingredient;
use App\Models\MaterialReceipt;
use App\Models\PurchaseOrder;
use App\Services\MaterialReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialReceiptController extends Controller
{
    public function __construct(
        protected MaterialReceiptService $receiptService
    ) {}

    /**
     * Menampilkan riwayat daftar penerimaan fisik bahan baku.
     */
    public function index(Request $request): View
    {
        $query = MaterialReceipt::query()
            ->with(['purchaseOrder.vendor', 'receiver', 'details.ingredient'])
            ->withCount('details');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('purchaseOrder', function ($poQuery) use ($search) {
                        $poQuery->where('order_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('receipt_date', $request->input('date'));
        }

        $receipts = $query->orderByDesc('receipt_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('material-receipts.index', [
            'receipts' => $receipts,
            'currentSearch' => $request->input('search'),
            'currentDate' => $request->input('date'),
        ]);
    }

    /**
     * Menampilkan formulir pencatatan penerimaan fisik bahan baku dari supplier.
     */
    public function create(Request $request): View
    {
        $selectedPoId = $request->query('purchase_order_id');

        $purchaseOrders = PurchaseOrder::query()
            ->with(['vendor', 'details.ingredient'])
            ->whereIn('status', [PurchaseOrderStatus::Ordered, PurchaseOrderStatus::Draft])
            ->orderByDesc('order_date')
            ->get();

        $activeIngredients = Ingredient::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'current_stock']);

        return view('material-receipts.create', [
            'purchaseOrders' => $purchaseOrders,
            'activeIngredients' => $activeIngredients,
            'selectedPoId' => $selectedPoId,
            'conditions' => MaterialCondition::cases(),
            'today' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Menyimpan data penerimaan dan mencatat mutasi stok masuk.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'receipt_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.condition' => ['required', 'string', Rule::enum(MaterialCondition::class)],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $receipt = $this->receiptService->recordReceipt($validated, $request->user());

        return redirect()
            ->route('material-receipts.show', $receipt)
            ->with('success', "Penerimaan bahan {$receipt->receipt_number} berhasil dicatat dan stok bahan layak telah masuk ke gudang.");
    }

    /**
     * Menampilkan detail penerimaan fisik dan kondisi bahan.
     */
    public function show(MaterialReceipt $materialReceipt): View
    {
        $materialReceipt->load([
            'purchaseOrder.vendor',
            'receiver',
            'details.ingredient',
        ]);

        return view('material-receipts.show', [
            'receipt' => $materialReceipt,
        ]);
    }
}
