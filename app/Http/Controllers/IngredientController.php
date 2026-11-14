<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Jobs\ProcessIngredientImport;
use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    /**
     * Import master data bahan baku dari berkas CSV (Materi Pertemuan 9 - Langkah 7 & 8).
     * Aman diulang (idempotent) dan mendukung pemrosesan latar belakang via queue.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv.required' => 'Berkas CSV bahan baku wajib dipilih.',
            'csv.mimes' => 'Format berkas harus berupa CSV (.csv atau .txt).',
            'csv.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);

        if ($request->boolean('queue')) {
            $path = $request->file('csv')->store('imports');
            ProcessIngredientImport::dispatch($path, (int) auth()->id());

            return back()->with('success', 'Berkas CSV berhasil diunggah dan sedang diproses di antrean latar belakang.');
        }

        $lines = file($request->file('csv')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            return back()->withErrors(['csv' => 'Berkas CSV kosong atau tidak memiliki baris data.']);
        }

        $rows = array_map('str_getcsv', $lines);
        $header = array_shift($rows);
        $header = array_map(fn ($h) => trim(str_replace("\xEF\xBB\xBF", '', (string) $h)), $header);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (count($row) !== count($header)) {
                $skipped++;

                continue;
            }

            $rowData = array_combine($header, $row);

            $validator = Validator::make($rowData, [
                'name' => ['required', 'string', 'max:255'],
                'category' => ['nullable', 'string', 'max:100'],
                'unit' => ['required', 'string', 'max:50'],
                'minimum_stock' => ['nullable', 'numeric', 'min:0'],
                'current_stock' => ['nullable', 'numeric', 'min:0'],
                'price_per_unit' => ['nullable', 'numeric', 'min:0'],
            ]);

            if ($validator->fails()) {
                $skipped++;

                continue;
            }

            $validated = $validator->validated();

            $ingredient = Ingredient::updateOrCreate(
                ['name' => trim($validated['name'])],
                [
                    'category' => ! empty($validated['category']) ? trim($validated['category']) : 'Umum',
                    'unit' => trim($validated['unit']),
                    'minimum_stock' => isset($validated['minimum_stock']) ? (float) $validated['minimum_stock'] : 0,
                    'current_stock' => isset($validated['current_stock']) ? (float) $validated['current_stock'] : 0,
                    'price_per_unit' => isset($validated['price_per_unit']) ? (float) $validated['price_per_unit'] : null,
                    'is_active' => true,
                ]
            );

            $ingredient->wasRecentlyCreated ? $created++ : $updated++;
        }

        $skippedMsg = $skipped > 0 ? " ({$skipped} baris dilewati karena format tidak sesuai)" : '';

        return back()->with('success', "Import berhasil: {$created} bahan baku baru, {$updated} diperbarui{$skippedMsg}.");
    }
}
