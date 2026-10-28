<?php

use App\Http\Controllers\ProductionDashboardController;
use App\Http\Controllers\Admin\PasswordResetRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CateringOrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionPlanController;
use App\Http\Controllers\ProductionRealizationController;
use App\Http\Controllers\ProductRecipeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Redirect generic dashboard ke dashboard sesuai role
Route::get('/dashboard', function (Request $request) {
    /** @var User $user */
    $user = $request->user();

    return redirect()->route($user->getDashboardRoute());
})->middleware(['auth'])->name('dashboard');

// 1. Admin / Staff Operasional
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.admin');
    })->name('dashboard');

    // Manajemen Pengguna & Hak Akses (UC-21 & UC-22)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Permintaan Pemulihan Kata Sandi (UC-02)
    Route::get('/password-resets', [PasswordResetRequestController::class, 'index'])->name('password-resets.index');
    Route::put('/password-resets/{passwordReset}/approve', [PasswordResetRequestController::class, 'approve'])->name('password-resets.approve');
    Route::patch('/password-resets/{passwordReset}/reject', [PasswordResetRequestController::class, 'reject'])->name('password-resets.reject');
});

// 2. Staff Gudang / Inventory
Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {
    Route::get('/dashboard', function () {
        $criticalIngredients = Ingredient::active()
            ->lowStock()
            ->orderBy('current_stock')
            ->get();

        return view('dashboard.gudang', [
            'criticalIngredients' => $criticalIngredients,
            'criticalCount' => $criticalIngredients->count(),
        ]);
    })->name('dashboard');
});

// 3. Staff Produksi / Ahli Gizi
Route::middleware(['auth', 'role:produksi'])
    ->prefix('produksi')
    ->name('produksi.')
    ->group(function () {
        Route::get('/dashboard', ProductionDashboardController::class)
            ->name('dashboard');
    });

// 4. Owner / Manager
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.owner');
    })->name('dashboard');
});

// Master Bahan Baku (UC-04: Kelola oleh Admin & Gudang, Lihat oleh Produksi & Owner)
Route::middleware(['auth'])->group(function () {
    // 1. Rute list (semua 4 role)
    Route::get('/ingredients', [IngredientController::class, 'index'])
        ->middleware('role:admin,gudang,produksi,owner')
        ->name('ingredients.index');

    // 2. Form create & store (khusus admin & gudang) - harus sebelum wildcard {ingredient}
    Route::get('/ingredients/create', [IngredientController::class, 'create'])
        ->middleware('role:admin,gudang')
        ->name('ingredients.create');
    Route::post('/ingredients', [IngredientController::class, 'store'])
        ->middleware('role:admin,gudang')
        ->name('ingredients.store');

    // 3. Rute detail view (semua 4 role)
    Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])
        ->middleware('role:admin,gudang,produksi,owner')
        ->name('ingredients.show');

    // 4. Edit, update, toggle-status, destroy (khusus admin & gudang)
    Route::middleware('role:admin,gudang')->group(function () {
        Route::get('/ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit');
        Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
        Route::patch('/ingredients/{ingredient}/toggle-status', [IngredientController::class, 'toggleStatus'])->name('ingredients.toggle-status');
        Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
    });
});

// Master Data Supplier (UC-25: Kelola oleh Admin, Lihat oleh Gudang & Owner, Produksi ditolak)
Route::middleware(['auth'])->group(function () {
    // 1. Rute list (Admin, Gudang, Owner)
    Route::get('/suppliers', [SupplierController::class, 'index'])
        ->middleware('role:admin,gudang,owner')
        ->name('suppliers.index');

    // 2. Form create & store (khusus Admin) - harus sebelum wildcard {supplier}
    Route::get('/suppliers/create', [SupplierController::class, 'create'])
        ->middleware('role:admin')
        ->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])
        ->middleware('role:admin')
        ->name('suppliers.store');

    // 3. Rute detail view (Admin, Gudang, Owner)
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])
        ->middleware('role:admin,gudang,owner')
        ->name('suppliers.show');

    // 4. Edit, update, toggle-status, destroy (khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::patch('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });
});

// Master Data Pelanggan (UC-26: Kelola oleh Admin, Lihat oleh Owner, Gudang & Produksi ditolak)
Route::middleware(['auth'])->group(function () {
    // 1. Rute list (Admin & Owner)
    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('role:admin,owner')
        ->name('customers.index');

    // 2. Form create & store (khusus Admin) - harus sebelum wildcard {customer}
    Route::get('/customers/create', [CustomerController::class, 'create'])
        ->middleware('role:admin')
        ->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('role:admin')
        ->name('customers.store');

    // 3. Rute detail view (Admin & Owner)
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])
        ->middleware('role:admin,owner')
        ->name('customers.show');

    // 4. Edit, update, toggle-status, destroy (khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });
});

// Master Data Menu Makanan & Rekap Gizi (UC-23, FR-11, FR-12: Kelola oleh Admin & Produksi, Lihat oleh Gudang & Owner)
Route::middleware(['auth'])->group(function () {
    // 1. Rute list (semua 4 role)
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('products.index');

    // 2. Form create & store (khusus Admin & Produksi) - sebelum wildcard {product}
    Route::get('/products/create', [ProductController::class, 'create'])
        ->middleware('role:admin,produksi')
        ->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('role:admin,produksi')
        ->name('products.store');

    // 3. Rute detail view (semua 4 role)
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('products.show');

    // 4. Edit, update, toggle-status, destroy (khusus Admin & Produksi)
    Route::middleware('role:admin,produksi')->group(function () {
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Komposisi Resep Bahan Baku (Recipe Builder - FR-08)
        Route::get('/products/{product}/recipe', [ProductRecipeController::class, 'edit'])->name('products.recipe.edit');
        Route::put('/products/{product}/recipe', [ProductRecipeController::class, 'update'])->name('products.recipe.update');
    });
});

// Perencanaan Produksi & Jadwal Masak (UC-14, UC-15, FR-18)
Route::middleware(['auth'])->group(function () {
    // 1. Rute list (khusus Admin & Produksi)
    Route::get('/production-plans', [ProductionPlanController::class, 'index'])
        ->middleware('role:admin,produksi')
        ->name('production-plans.index');

    // 2. Form create & store (khusus Admin & Produksi) - sebelum wildcard {productionPlan}
    Route::get('/production-plans/create', [ProductionPlanController::class, 'create'])
        ->middleware('role:admin,produksi')
        ->name('production-plans.create');
    Route::post('/production-plans', [ProductionPlanController::class, 'store'])
        ->middleware('role:admin,produksi')
        ->name('production-plans.store');

    // 2.1 Preview kalkulasi kebutuhan bahan baku (simulasi interaktif)
    Route::post('/production-plans/calculate-preview', [ProductionPlanController::class, 'calculatePreview'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('production-plans.calculate-preview');

    // 3. Rute detail view (semua 4 role)
    Route::get('/production-plans/{productionPlan}', [ProductionPlanController::class, 'show'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('production-plans.show');

    // 3.1 Rute lembar kebutuhan bahan baku & logistik dapur (semua 4 role)
    Route::get('/production-plans/{productionPlan}/ingredients', [ProductionPlanController::class, 'ingredients'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('production-plans.ingredients');

    // 4. Edit, update, destroy (khusus Admin & Produksi)
    Route::middleware('role:admin,produksi')->group(function () {
        Route::get('/production-plans/{productionPlan}/edit', [ProductionPlanController::class, 'edit'])->name('production-plans.edit');
        Route::put('/production-plans/{productionPlan}', [ProductionPlanController::class, 'update'])->name('production-plans.update');
        Route::delete('/production-plans/{productionPlan}', [ProductionPlanController::class, 'destroy'])->name('production-plans.destroy');
    });
});

// Pesanan Katering (UC-07, FR-14: Input oleh Admin, Lihat oleh Admin, Produksi, Gudang, Owner)
Route::middleware(['auth'])->group(function () {
    // 1. Rute list (semua 4 role)
    Route::get('/catering-orders', [CateringOrderController::class, 'index'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('catering-orders.index');

    // 2. Form create & store (khusus Admin) - sebelum wildcard {cateringOrder}
    Route::get('/catering-orders/create', [CateringOrderController::class, 'create'])
        ->middleware('role:admin')
        ->name('catering-orders.create');
    Route::post('/catering-orders', [CateringOrderController::class, 'store'])
        ->middleware('role:admin')
        ->name('catering-orders.store');

    // 3. Rute detail view (semua 4 role)
    Route::get('/catering-orders/{cateringOrder}', [CateringOrderController::class, 'show'])
        ->middleware('role:admin,produksi,gudang,owner')
        ->name('catering-orders.show');

    // 4. Edit form & update (khusus Admin)
    Route::get('/catering-orders/{cateringOrder}/edit', [CateringOrderController::class, 'edit'])
        ->middleware('role:admin')
        ->name('catering-orders.edit');
    Route::put('/catering-orders/{cateringOrder}', [CateringOrderController::class, 'update'])
        ->middleware('role:admin');
    Route::patch('/catering-orders/{cateringOrder}', [CateringOrderController::class, 'update'])
        ->middleware('role:admin')
        ->name('catering-orders.update');

    // 5. Aksi perubahan status cepat (khusus Admin: kelola penuh, Owner: read-only)
    Route::patch('/catering-orders/{cateringOrder}/status', [CateringOrderController::class, 'updateStatus'])
        ->middleware('role:admin')
        ->name('catering-orders.update-status');
    Route::put('/catering-orders/{cateringOrder}/status', [CateringOrderController::class, 'updateStatus'])
        ->middleware('role:admin');
});

// Purchase Order Pengadaan Bahan Baku (UC-27, FR-27: Khusus Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
});

// Pengajuan Permintaan Bahan Dapur & Verifikasi Stok Keluar Gudang (URUTAN 13, BR-05)
Route::middleware(['auth'])->group(function () {
    // 1. Tampilan daftar seluruh permintaan bahan (semua 4 role)
    Route::get('/material-requests', [MaterialRequestController::class, 'index'])
        ->middleware('role:admin,gudang,produksi,owner')
        ->name('material-requests.index');

    // 2. Formulir & submit permintaan bahan dapur (khusus Admin & Produksi)
    Route::get('/material-requests/create', [MaterialRequestController::class, 'create'])
        ->middleware('role:admin,produksi')
        ->name('material-requests.create');
    Route::post('/material-requests', [MaterialRequestController::class, 'store'])
        ->middleware('role:admin,produksi')
        ->name('material-requests.store');
    Route::post('/production-plans/{productionPlan}/material-requests', [MaterialRequestController::class, 'storeFromPlan'])
        ->middleware('role:admin,produksi')
        ->name('production-plans.material-requests.store');

    // 3. Detail permintaan bahan (semua 4 role)
    Route::get('/material-requests/{materialRequest}', [MaterialRequestController::class, 'show'])
        ->middleware('role:admin,gudang,produksi,owner')
        ->name('material-requests.show');

    // 4. Verifikasi Permintaan Bahan & Transaksi Stok Keluar (KHUSUS Gudang & Admin - BR-05)
    Route::post('/material-requests/{materialRequest}/verify', [MaterialRequestController::class, 'verify'])
        ->middleware('role:admin,gudang')
        ->name('material-requests.verify');
});

// Pencatatan Realisasi Porsi Masak & Status Selesai Rencana Produksi (URUTAN 14, FR-33, FR-34, UC-17)
Route::middleware(['auth', 'role:admin,produksi'])->group(function () {
    Route::get('/productions', [ProductionRealizationController::class, 'index'])
        ->name('productions.index');
    Route::get('/productions/create', [ProductionRealizationController::class, 'create'])
        ->name('productions.create');
    Route::get('/production-plans/{productionPlan}/realization', [ProductionRealizationController::class, 'create'])
        ->name('production-plans.realization.create');
    Route::post('/production-plans/{productionPlan}/realization', [ProductionRealizationController::class, 'store'])
        ->name('production-plans.realization.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
