<?php

use App\Http\Controllers\Admin\PasswordResetRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
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
        return view('dashboard.gudang');
    })->name('dashboard');
});

// 3. Staff Produksi / Ahli Gizi
Route::middleware(['auth', 'role:produksi'])->prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.produksi');
    })->name('dashboard');
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
