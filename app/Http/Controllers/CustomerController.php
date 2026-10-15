<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Customer::query();

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

        $customers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
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
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $customer = Customer::create($data);

        return redirect()
            ->route('customers.index')
            ->with('success', "Data pelanggan \"{$customer->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        return view('customers.show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $customer->update($data);

        return redirect()
            ->route('customers.index')
            ->with('success', "Data pelanggan \"{$customer->name}\" berhasil diperbarui.");
    }

    /**
     * Toggle active status of the specified customer.
     */
    public function toggleStatus(Customer $customer): RedirectResponse
    {
        $newStatus = ! $customer->is_active;
        $customer->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status pelanggan \"{$customer->name}\" berhasil {$statusText}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        // Periksa apakah pelanggan sudah memiliki pesanan (catering_orders atau orders)
        $hasCateringOrders = Schema::hasTable('catering_orders')
            && DB::table('catering_orders')->where('customer_id', $customer->id)->exists();

        $hasOrders = Schema::hasTable('orders')
            && DB::table('orders')->where('customer_id', $customer->id)->exists();

        if ($hasCateringOrders || $hasOrders) {
            // Sesuai BR-02 & UC-26: jangan hapus fisik jika sudah ada transaksi, cukup nonaktifkan
            $customer->update(['is_active' => false]);

            return redirect()
                ->route('customers.index')
                ->with('success', "Pelanggan \"{$customer->name}\" telah memiliki riwayat pesanan katering sehingga statusnya dinonaktifkan (data riwayat transaksi tetap terjaga).");
        }

        $name = $customer->name;
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', "Pelanggan \"{$name}\" berhasil dihapus.");
    }
}
