<?php

use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
