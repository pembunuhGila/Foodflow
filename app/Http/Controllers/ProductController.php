<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Satuan takaran menu standar.
     *
     * @var list<string>
     */
    public const COMMON_UNITS = [
        'porsi',
        'box',
        'pax',
        'paket',
        'gelas',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = strtolower($request->string('search')->trim());
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'filters' => [
                'search' => $request->query('search', ''),
                'status' => $request->query('status', ''),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('products.create', [
            'commonUnits' => self::COMMON_UNITS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isProduksi = $user !== null && $user->role === UserRole::Produksi;

        if ($isProduksi) {
            $data = $request->validated();
        } else {
            // Jika dibuat oleh Admin, nilai gizi diset ke 0 (menunggu diisi bagian produksi)
            $data = $request->safe()->only(['name', 'description', 'unit', 'selling_price']);
            $data['calories'] = $request->filled('calories') ? $request->input('calories') : 0;
            $data['protein'] = $request->filled('protein') ? $request->input('protein') : 0;
            $data['fat'] = $request->filled('fat') ? $request->input('fat') : 0;
            $data['carbohydrates'] = $request->filled('carbohydrates') ? $request->input('carbohydrates') : 0;
            $data['dietary_fiber'] = $request->filled('dietary_fiber') ? $request->input('dietary_fiber') : 0;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', "Menu makanan \"{$product->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'commonUnits' => self::COMMON_UNITS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();
        $isProduksi = $user !== null && $user->role === UserRole::Produksi;

        if ($isProduksi) {
            // Staff Produksi / Ahli Gizi dapat memperbarui data menu dan data gizi
            $data = $request->validated();
        } else {
            // Admin hanya boleh mengedit informasi umum menu, TIDAK MENGUBAH nilai gizi yang sudah ada
            $data = $request->safe()->only(['name', 'description', 'unit', 'selling_price']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', "Data menu \"{$product->name}\" berhasil diperbarui.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        $statusText = $product->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()
            ->route('products.index')
            ->with('success', "Status menu \"{$product->name}\" berhasil {$statusText}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $hasRelations = false;

        if (Schema::hasTable('catering_order_details') && DB::table('catering_order_details')->where('product_id', $product->id)->exists()) {
            $hasRelations = true;
        }

        if (Schema::hasTable('product_ingredients') && DB::table('product_ingredients')->where('product_id', $product->id)->exists()) {
            $hasRelations = true;
        }

        if (Schema::hasTable('production_plan_details') && DB::table('production_plan_details')->where('product_id', $product->id)->exists()) {
            $hasRelations = true;
        }

        if (Schema::hasTable('productions') && DB::table('productions')->where('product_id', $product->id)->exists()) {
            $hasRelations = true;
        }

        if ($hasRelations) {
            $product->update(['is_active' => false]);

            return redirect()
                ->route('products.index')
                ->with('warning', "Menu \"{$product->name}\" memiliki riwayat pesanan/produksi terkait sehingga dinonaktifkan (bukan dihapus) agar riwayat operasional tetap utuh.");
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', "Menu \"{$product->name}\" berhasil dihapus.");
    }
}
