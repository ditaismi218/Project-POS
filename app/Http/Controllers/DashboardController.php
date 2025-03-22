<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\PenerimaanBarang;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\ActivityLog; // Import Model ActivityLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        Log::info('Memuat halaman dashboard.', ['user_id' => $user->id, 'email' => $user->email]);

        // Simpan ke activity log database
        ActivityLog::create([
            'action' => 'Akses Dashboard',
            'description' => 'User mengakses halaman dashboard',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'waktu' => now(),
            ]
        ]);

        $jumlahKategori = KategoriProduk::count();
        $jumlahProduk = Produk::count();
        $jumlahSupplier = Supplier::count();
        $totalHargaBeli = PenerimaanBarang::sum('harga_total');

        Log::info("Statistik Dashboard", [
            'jumlah_kategori' => $jumlahKategori,
            'jumlah_produk' => $jumlahProduk,
            'jumlah_supplier' => $jumlahSupplier,
            'total_harga_beli' => $totalHargaBeli
        ]);

        $produkTerlaris = DB::table('detail_penjualan')
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->select('produk.nama_barang', DB::raw('SUM(detail_penjualan.qty) as total_terjual'))
            ->groupBy('produk.nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        Log::info('Produk terlaris berhasil diambil.', ['produk_terlaris' => $produkTerlaris->toArray()]);

        $penjualanHarian = DB::table('penjualan')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_bayar) as total_penjualan'),
                DB::raw('COUNT(id) as jumlah_transaksi')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        Log::info('Data penjualan harian berhasil diambil.', ['penjualan_harian' => $penjualanHarian->toArray()]);

        ActivityLog::create([
            'action' => 'Lihat Statistik Dashboard',
            'description' => 'User melihat statistik di dashboard',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'waktu' => now(),
                'produk_terlaris' => $produkTerlaris->toArray(),
                'penjualan_harian' => $penjualanHarian->toArray(),
            ]
        ]);

        $labels = $penjualanHarian->pluck('tanggal');
        $dataPenjualan = $penjualanHarian->pluck('total_penjualan');
        $jumlahTransaksi = $penjualanHarian->pluck('jumlah_transaksi')->toArray();

        return view('dashboard', compact('jumlahKategori', 'jumlahProduk', 'jumlahSupplier', 'totalHargaBeli', 'produkTerlaris', 'labels', 'dataPenjualan', 'jumlahTransaksi'));
    }

    public function filterPenjualan(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter');

        Log::info("Memfilter data penjualan dengan filter: $filter", ['user_id' => $user->id, 'email' => $user->email]);

        $query = DB::table('penjualan')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_bayar) as total_penjualan'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy('tanggal');

        if ($filter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($filter === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($filter === 'last_7_days') {
            $query->whereDate('created_at', '>=', today()->subDays(7));
        } elseif ($filter === 'last_30_days') {
            $query->whereDate('created_at', '>=', today()->subDays(30));
        } elseif ($filter === 'current_month') {
            $query->whereMonth('created_at', today()->month)
                ->whereYear('created_at', today()->year);
        } elseif ($filter === 'last_month') {
            $query->whereMonth('created_at', today()->subMonth()->month)
                ->whereYear('created_at', today()->subMonth()->year);
        }

        $penjualanHarian = $query->orderBy('tanggal', 'asc')->get();

        Log::info('Data penjualan setelah difilter berhasil diambil.', ['filter' => $filter, 'penjualan_harian' => $penjualanHarian->toArray()]);

        ActivityLog::create([
            'action' => 'Filter Penjualan',
            'description' => "User menerapkan filter: $filter",
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'filter' => $filter,
                'penjualan_harian' => $penjualanHarian->toArray(),
                'waktu' => now(),
            ]
        ]);

        return response()->json([
            'labels' => $penjualanHarian->pluck('tanggal'),
            'dataPenjualan' => $penjualanHarian->pluck('total_penjualan'),
            'jumlahTransaksi' => $penjualanHarian->pluck('jumlah_transaksi')
        ]);
    }
}
