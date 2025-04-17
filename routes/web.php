<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\kategoriController;
use App\Http\Controllers\LaporanPenerimaanBarangController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\LaporanTransaksiController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenerimaanBarangController;
use App\Http\Controllers\PengajuanBarang;
use App\Http\Controllers\PengajuanBarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('dashboard', function () {
    if (Auth::check()) {

        switch (Auth::user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'kasir':
                return redirect()->route('kasir.dashboard');
            default:
                return view('dashboard');
        }
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/dashboard/filter-penjualan', [DashboardController::class, 'filterPenjualan']);
    Route::get('admin/dashboard/penjualan', [DashboardController::class, 'getPenjualanData']);

    Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
    Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
    Route::put('kategori/{id}', [kategoriController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{id}', [kategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::post('/kategori/import', [KategoriController::class, 'import'])->name('kategori.import');

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    Route::post('/supplier/import', [SupplierController::class, 'import'])->name('supplier.import');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/penerimaan_barang', [PenerimaanBarangController::class, 'index'])->name('penerimaan_barang.index');
    Route::get('/penerimaan_barang/create', [PenerimaanBarangController::class, 'create'])->name('penerimaan_barang.create');
    Route::post('/penerimaan_barang', [PenerimaanBarangController::class, 'store'])->name('penerimaan_barang.store');
    Route::get('/penerimaan_barang/{id}', [PenerimaanBarangController::class, 'show'])->name('penerimaan_barang.show');
    // Route::post('/penerimaan_barang/edit', [PenerimaanBarangController::class, 'update'])->name('penerimaan_barang.update');
    Route::delete('/penerimaan_barang/{id}', [PenerimaanBarangController::class, 'destroy'])->name('penerimaan_barang.destroy');
    Route::put('/penerimaan-barang/{id}', [PenerimaanBarangController::class, 'update'])->name('penerimaan_barang.update');

    Route::get('/laporan/penjualan', [LaporanPenjualanController::class, 'laporanPenjualan'])->name('laporan.penjualan');
    Route::get('/laporan-penjualan/export', [LaporanPenjualanController::class, 'export'])->name('laporan.penjualan.export');
    Route::get('/laporan/penerimaan_barang', [LaporanPenerimaanBarangController::class, 'index'])->name('laporan.penerimaan_barang');
    // Route::get('/laporan/transaksi', [LaporanTransaksiController::class, 'index'])->name('laporan.transaksi');
    // Route::get('/laporan/transaksi/{id}', [LaporanTransaksiController::class, 'show'])->name('transaksi.detail');
    Route::get('/laporan/transaksi/print-struk/{id}', [LaporanTransaksiController::class, 'printStruk'])->name('struk.print');


    Route::get('/pengajuan_barang', [PengajuanBarangController::class, 'index'])->name('pengajuan_barang.index');
    Route::post('/pengajuan_barang/store', [PengajuanBarangController::class, 'store'])->name('pengajuan_barang.store');
    Route::put('/pengajuan_barang/update/{id}', [PengajuanBarangController::class, 'update'])->name('pengajuan_barang.update');
    Route::delete('/pengajuan_barang/destroy/{id}', [PengajuanBarangController::class, 'destroy'])->name('pengajuan_barang.destroy');
    Route::post('/pengajuan-barang/update-terpenuhi/{id}', [PengajuanBarangController::class, 'updateTerpenuhi'])->name('pengajuan.update-terpenuhi');


    // member
    Route::get('/member', [MemberController::class, 'index'])->name('member.index');
    Route::post('/member', [MemberController::class, 'store'])->name('member.store');
    Route::put('/member/{id}', [MemberController::class, 'update'])->name('member.update');
    Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');
    Route::post('/members/import', [MemberController::class, 'import'])->name('members.import');

    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

    // Halaman utama absensi (index)
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
    Route::post('/absensi/update-status', [AbsensiController::class, 'updateStatus'])->name('absensi.updateStatus');
    Route::post('/absensi/selesaikan', [AbsensiController::class, 'selesaikan'])->name('absensi.selesaikan');
    Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
    Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.exportPdf');
    Route::post('/absensi/import', [AbsensiController::class, 'import'])->name('absensi.import');
    Route::get('/absensi/export-excel', [AbsensiController::class, 'exportExcel'])->name('absensi.export');


});

Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/laporan/transaksi', [LaporanTransaksiController::class, 'index'])->name('laporan.transaksi');
    Route::get('/laporan/transaksi/{id}', [LaporanTransaksiController::class, 'show'])->name('transaksi.detail');
    Route::get('/laporan/transaksi/print-struk/{id}', [LaporanTransaksiController::class, 'printStruk'])->name('struk.print');

    
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::get('/invoice/{id}', [PenjualanController::class, 'invoice'])->name('invoice.show');



    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create/{penjualan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/store', [PembayaranController::class, 'store'])->name('pembayaran.store');

});


Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasirHome', [KasirController::class, 'index'])->name('kasir.dashboard');
    Route::get('kasirHome/filter-penjualan', [KasirController::class, 'filterPenjualan']);

    // Route::get('/laporan/transaksi', [LaporanTransaksiController::class, 'index'])->name('laporan.transaksi');
    // Route::get('/laporan/transaksi/{id}', [LaporanTransaksiController::class, 'show'])->name('transaksi.detail');

    // Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    // Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    // Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    // Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
    // Route::get('/invoice/{id}', [PenjualanController::class, 'invoice'])->name('invoice.show');



    // Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    // Route::get('/pembayaran/create/{penjualan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
    // Route::post('/pembayaran/store', [PembayaranController::class, 'store'])->name('pembayaran.store');

    // Route::get('/invoice/cetak/{id}', [InvoiceController::class, 'cetak'])->name('invoice.cetak');
});


// Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
// Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
// Route::put('kategori/{id}', [kategoriController::class, 'update'])->name('kategori.update');
// Route::delete('kategori/{id}', [kategoriController::class, 'destroy'])->name('kategori.destroy');

// Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index'); 
// Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
// Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
// Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

// Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index'); 
// Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
// Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
// Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

// Route::get('/users', [UserController::class, 'index'])->name('users.index'); 
// Route::post('/users', [UserController::class, 'store'])->name('users.store');
// Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
// Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Route::get('/penerimaan_barang', [PenerimaanBarangController::class, 'index'])->name('penerimaan_barang.index'); 
// Route::get('/penerimaan_barang/create', [PenerimaanBarangController::class, 'create'])->name('penerimaan_barang.create'); 
// Route::post('/penerimaan_barang', [PenerimaanBarangController::class, 'store'])->name('penerimaan_barang.store');
// Route::get('/penerimaan_barang/{id}', [PenerimaanBarangController::class, 'show'])->name('penerimaan_barang.show');
// Route::post('/penerimaan_barang/edit', [PenerimaanBarangController::class, 'update'])->name('penerimaan_barang.update');
// Route::delete('/penerimaan_barang/{id}', [PenerimaanBarangController::class, 'destroy'])->name('penerimaan_barang.destroy');

// Route::get('/member', [MemberController::class, 'index'])->name('member.index'); 
// Route::post('/member', [MemberController::class, 'store'])->name('member.store');
// Route::put('/member/{id}', [MemberController::class, 'update'])->name('member.update');
// Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');


// Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
// Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
// Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
// Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
// Route::post('/penjualan/get-harga-terbaru', [PenjualanController::class, 'getHargaTerbaru'])->name('penjualan.getHargaTerbaru');
// Route::get('/invoice/{id}', [PenjualanController::class, 'invoice'])->name('invoice.show');


// Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
// Route::get('/pembayaran/create/{penjualan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
// Route::post('/pembayaran/store', [PembayaranController::class, 'store'])->name('pembayaran.store');

// Route::get('/invoice/cetak/{id}', [InvoiceController::class, 'cetak'])->name('invoice.cetak');

// Route::get('/laporan/penjualan', [LaporanPenjualanController::class, 'laporanPenjualan'])->name('laporan.penjualan');
// Route::get('/laporan/transaksi', [LaporanTransaksiController::class, 'index'])->name('laporan.transaksi');
// Route::get('/laporan/transaksi/{id}', [LaporanTransaksiController::class, 'show'])->name('transaksi.detail');



