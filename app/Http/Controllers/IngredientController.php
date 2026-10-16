<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    /**
     * Categories suggestion for dropdowns.
     *
     * @var list<string>
     */
    public const COMMON_CATEGORIES = [
        'Karbohidrat',
        'Protein Hewani',
        'Protein Nabati',
        'Sayuran',
        'Buah',
        'Bumbu & Rempah',
        'Minyak & Lemak Sehat',
        'Susu & Olahan',
        'Lainnya',
    ];

    /**
     * Common measurement units suggestion.
     *
     * @var list<string>
     */
    public const COMMON_UNITS = [
        'kg',
        'gram',
        'liter',
        'ml',
        'pcs',
        'butir',
        'ikat',
        'bungkus',
        'kaleng',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Ingredient::query();

        if ($request->filled('search')) {
            $search = strtolower($request->string('search')->trim());
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(category) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(notes) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('stock_status')) {
            $stockStatus = $request->string('stock_status')->toString();
            if ($stockStatus === 'low_stock') {
                $query->lowStock();
            } elseif ($stockStatus === 'out_of_stock') {
                $query->outOfStock();
            } elseif ($stockStatus === 'sufficient') {
                $query->sufficientStock();
            }
        }

        $ingredients = $query->orderBy('name')->paginate(10)->withQueryString();

        $categories = Ingredient::query()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $criticalCount = Ingredient::active()->lowStock()->count();

        return view('ingredients.index', [
            'ingredients' => $ingredients,
            'categories' => $categories,
            'commonCategories' => self::COMMON_CATEGORIES,
            'criticalCount' => $criticalCount,
            'filters' => [
                'search' => $request->query('search', ''),
                'category' => $request->query('category', ''),
                'status' => $request->query('status', ''),
                'stock_status' => $request->query('stock_status', ''),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('ingredients.create', [
            'commonCategories' => self::COMMON_CATEGORIES,
            'commonUnits' => self::COMMON_UNITS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIngredientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $ingredient = Ingredient::create($data);

        return redirect()
            ->route('ingredients.index')
            ->with('success', "Bahan baku \"{$ingredient->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient): View
    {
        return view('ingredients.show', [
            'ingredient' => $ingredient,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient): View
    {
        return view('ingredients.edit', [
            'ingredient' => $ingredient,
            'commonCategories' => self::COMMON_CATEGORIES,
            'commonUnits' => self::COMMON_UNITS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $ingredient->update($data);

        return redirect()
            ->route('ingredients.index')
            ->with('success', "Bahan baku \"{$ingredient->name}\" berhasil diperbarui.");
    }

    /**
     * Toggle active status of the specified ingredient.
     */
    public function toggleStatus(Ingredient $ingredient): RedirectResponse
    {
        $newStatus = ! $ingredient->is_active;
        $ingredient->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Bahan baku \"{$ingredient->name}\" berhasil {$statusText}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $name = $ingredient->name;
        $ingredient->delete();

        return redirect()
            ->route('ingredients.index')
            ->with('success', "Bahan baku \"{$name}\" berhasil dihapus.");
    }
}
