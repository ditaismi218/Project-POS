<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\PenerimaanBarang;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Method untuk menampilkan halaman dashboard utama
    public function index()
    {
        $user = Auth::user();

        // Logging ke file log Laravel
        Log::info('Memuat halaman dashboard.', ['user_id' => $user->id, 'email' => $user->email]);

        // Logging ke database ActivityLog
        ActivityLog::create([
            'action' => 'Akses Dashboard',
            'description' => 'User mengakses halaman dashboard',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'waktu' => now(),
            ]
        ]);

        // Hitung jumlah kategori produk
        $jumlahKategori = KategoriProduk::count();
        // Hitung jumlah seluruh produk
        $jumlahProduk = Produk::count();
        // Hitung jumlah supplier
        $jumlahSupplier = Supplier::count();
        // Hitung total harga pembelian semua barang
        $totalHargaBeli = PenerimaanBarang::sum('harga_total');


        // Logging statistik ke Laravel log
        Log::info("Statistik Dashboard", [
            'jumlah_kategori' => $jumlahKategori,
            'jumlah_produk' => $jumlahProduk,
            'jumlah_supplier' => $jumlahSupplier,
            'total_harga_beli' => $totalHargaBeli
        ]);

        // Ambil 5 produk terlaris berdasarkan jumlah penjualan
        $produkTerlaris = DB::table('detail_penjualan')
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->select('produk.nama_barang', DB::raw('SUM(detail_penjualan.qty) as total_terjual'))
            ->groupBy('produk.nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        Log::info('Produk terlaris berhasil diambil.', ['produk_terlaris' => $produkTerlaris->toArray()]);

        // Ambil data penjualan harian
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

        // Simpan aktivitas melihat statistik ke ActivityLog
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

        // Siapkan data untuk grafik
        $labels = $penjualanHarian->pluck('tanggal');
        $dataPenjualan = $penjualanHarian->pluck('total_penjualan');
        $jumlahTransaksi = $penjualanHarian->pluck('jumlah_transaksi')->toArray();

        // Kirim data ke view dashboard
        return view('dashboard', compact('jumlahKategori', 'jumlahProduk', 'jumlahSupplier', 'totalHargaBeli', 'produkTerlaris', 'labels', 'dataPenjualan', 'jumlahTransaksi'));
    }

    // Method untuk memfilter penjualan berdasarkan rentang waktu tertentu
    public function filterPenjualan(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter');

        Log::info("Memfilter data penjualan dengan filter: $filter", ['user_id' => $user->id, 'email' => $user->email]);

        // Query dasar untuk penjualan
        $query = DB::table('penjualan')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_bayar) as total_penjualan'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy('tanggal');

        // Tambahkan filter berdasarkan parameter yang dikirimkan
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

        // Eksekusi query
        $penjualanHarian = $query->orderBy('tanggal', 'asc')->get();

        Log::info('Data penjualan setelah difilter berhasil diambil.', ['filter' => $filter, 'penjualan_harian' => $penjualanHarian->toArray()]);

        // Simpan aktivitas filter penjualan ke database
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

         // Kembalikan data sebagai JSON (biasanya untuk grafik atau dashboard dinamis)
        return response()->json([
            'labels' => $penjualanHarian->pluck('tanggal'),
            'dataPenjualan' => $penjualanHarian->pluck('total_penjualan'),
            'jumlahTransaksi' => $penjualanHarian->pluck('jumlah_transaksi')
        ]);
    }
}
