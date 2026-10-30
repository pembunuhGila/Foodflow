<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = strtolower($request->string('search')->trim());
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(notes) LIKE ?', ["%{$search}%"]);
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

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('suppliers.index', [
            'suppliers' => $suppliers,
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
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $supplier = Supplier::create($data);

        return redirect()
            ->route('suppliers.index')
            ->with('success', "Data supplier \"{$supplier->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View
    {
        return view('suppliers.show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $supplier->update($data);

        return redirect()
            ->route('suppliers.index')
            ->with('success', "Data supplier \"{$supplier->name}\" berhasil diperbarui.");
    }

    /**
     * Toggle active status of the specified supplier.
     */
    public function toggleStatus(Supplier $supplier): RedirectResponse
    {
        $newStatus = ! $supplier->is_active;
        $supplier->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status supplier \"{$supplier->name}\" berhasil {$statusText}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        // Periksa apakah supplier sudah terikat dengan pesanan pengadaan (purchase_orders)
        $hasOrders = Schema::hasTable('purchase_orders')
            && DB::table('purchase_orders')->where('vendor_id', $supplier->id)->exists();

        if ($hasOrders) {
            // Sesuai BR-02: jangan hapus fisik jika sudah ada transaksi, cukup nonaktifkan
            $supplier->update(['is_active' => false]);

            return redirect()
                ->route('suppliers.index')
                ->with('success', "Supplier \"{$supplier->name}\" telah memiliki riwayat pengadaan sehingga statusnya dinonaktifkan (data riwayat tetap terjaga).");
        }

        $name = $supplier->name;
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', "Supplier \"{$name}\" berhasil dihapus.");
    }
}
