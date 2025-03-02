<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\kategoriController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
// Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
// Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
// Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
// Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
// Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
Route::get('kategori/create', [kategoriController::class, 'create'])->name('kategori.create');
Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
Route::get('kategori/{id}/edit', [kategoriController::class, 'edit'])->name('kategori.edit');
Route::put('kategori/{id}', [kategoriController::class, 'update'])->name('kategori.update');
Route::delete('kategori/{id}', [kategoriController::class, 'destroy'])->name('kategori.destroy');

Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index'); 
Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index'); 
Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');



