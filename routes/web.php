<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\kategoriController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenerimaanBarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


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
Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
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

Route::get('/users', [UserController::class, 'index'])->name('users.index'); 
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/penerimaan_barang', [PenerimaanBarangController::class, 'index'])->name('penerimaan_barang.index'); 
Route::post('/penerimaan_barang', [PenerimaanBarangController::class, 'store'])->name('penerimaan_barang.store');
Route::post('/penerimaan_barang/edit', [PenerimaanBarangController::class, 'update'])->name('penerimaan_barang.update');
Route::delete('/penerimaan_barang/{id}', [PenerimaanBarangController::class, 'destroy'])->name('penerimaan_barang.destroy');

Route::get('/voucher', [VoucherController::class, 'index'])->name('voucher.index'); 
Route::post('/voucher', [VoucherController::class, 'store'])->name('voucher.store');
Route::put('/voucher/{id}', [VoucherController::class, 'update'])->name('voucher.update');
Route::delete('/voucher/{id}', [VoucherController::class, 'destroy'])->name('voucher.destroy');

Route::get('/member', [MemberController::class, 'index'])->name('member.index'); 
Route::post('/member', [MemberController::class, 'store'])->name('member.store');
Route::put('/member/{id}', [MemberController::class, 'update'])->name('member.update');
Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');


Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');

Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
Route::get('/pembayaran/create/{penjualan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
Route::post('/pembayaran/store', [PembayaranController::class, 'store'])->name('pembayaran.store');
