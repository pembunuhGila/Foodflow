<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockCardController extends Controller
{
    /**
     * Menampilkan buku kartu mutasi stok bahan baku (FR-24, FR-29, FR-38).
     */
    public function index(Request $request): View
    {
        $ingredients = Ingredient::active()->orderBy('name')->get(['id', 'name', 'unit', 'current_stock']);

        $query = IngredientStockTransaction::query()
            ->with(['ingredient', 'creator'])
            ->orderBy('transaction_date')
            ->orderBy('id');

        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->input('ingredient_id'));
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->input('transaction_type'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->input('to_date'));
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Hitung running balance untuk transaksi yang ditampilkan jika filter bahan spesifik dipilih
        $selectedIngredient = null;
        if ($request->filled('ingredient_id')) {
            $selectedIngredient = $ingredients->firstWhere('id', (int) $request->input('ingredient_id'));
        }

        return view('stock-cards.index', [
            'transactions' => $transactions,
            'ingredients' => $ingredients,
            'selectedIngredient' => $selectedIngredient,
            'selectedIngredientId' => $request->input('ingredient_id'),
            'selectedType' => $request->input('transaction_type'),
            'fromDate' => $request->input('from_date'),
            'toDate' => $request->input('to_date'),
        ]);
    }

    /**
     * Menampilkan formulir penyesuaian stok fisik / stock adjustment (FR-26).
     */
    public function createAdjustment(): View
    {
        $ingredients = Ingredient::active()->orderBy('name')->get(['id', 'name', 'unit', 'current_stock']);

        return view('stock-cards.adjustment', [
            'ingredients' => $ingredients,
            'today' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    /**
     * Menyimpan penyesuaian stok fisik dan mencatat mutasi penyesuaian (BR-03, FR-26).
     */
    public function storeAdjustment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'physical_count' => ['required', 'numeric', 'min:0'],
            'adjustment_date' => ['required', 'date'],
            'notes' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'notes.required' => 'Alasan penyesuaian stok wajib diisi (misal: susut bobot, selisih opname fisik, rusak tersenggol, dll).',
        ]);

        $ingredient = Ingredient::lockForUpdate()->findOrFail($validated['ingredient_id']);
        $currentStock = (float) ($ingredient->current_stock ?? 0);
        $physicalCount = (float) $validated['physical_count'];
        $difference = round($physicalCount - $currentStock, 3);

        DB::transaction(function () use ($ingredient, $physicalCount, $currentStock, $difference, $validated, $request) {
            // Catat record transaksi adjustment
            IngredientStockTransaction::create([
                'ingredient_id' => $ingredient->id,
                'transaction_type' => 'adjustment',
                'quantity' => $difference,
                'transaction_date' => $validated['adjustment_date'],
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'notes' => "Penyesuaian stok fisik dari {$currentStock} {$ingredient->unit} menjadi {$physicalCount} {$ingredient->unit} (Selisih: ".($difference >= 0 ? "+{$difference}" : "{$difference}")." {$ingredient->unit}). Alasan: ".$validated['notes'],
                'created_by' => $request->user()->id,
            ]);

            // Update current_stock bahan baku
            $ingredient->update([
                'current_stock' => $physicalCount,
            ]);
        });

        $diffFormatted = ($difference >= 0 ? "+{$difference}" : "{$difference}")." {$ingredient->unit}";

        return redirect()
            ->route('stock-cards.index', ['ingredient_id' => $ingredient->id])
            ->with('success', "Penyesuaian stok {$ingredient->name} berhasil disimpan. Saldo stok kini {$physicalCount} {$ingredient->unit} ({$diffFormatted}).");
    }
}
