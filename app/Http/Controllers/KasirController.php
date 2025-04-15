<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\ActivityLog; // Import Model ActivityLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    // Menampilkan halaman dashboard kasir
    public function index()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        Log::info('Kasir mengakses dashboard.', ['user_id' => $user->id, 'email' => $user->email]);

        // Simpan log aktivitas akses dashboard ke database
        ActivityLog::create([
            'action' => 'Akses Dashboard Kasir',
            'description' => 'Kasir mengakses halaman dashboard kasir',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'waktu' => now(),
            ]
        ]);

        // Hitung jumlah member dan jumlah transaksi penjualan
        $jumlahMember = Member::count();
        $jumlahPenjualan = Penjualan::count();

        // Ambil 5 produk terlaris berdasarkan jumlah qty terbanyak
        $produkTerlaris = DB::table('detail_penjualan')
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->select('produk.nama_barang', DB::raw('SUM(detail_penjualan.qty) as total_terjual'))
            ->groupBy('produk.nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        Log::info('Produk terlaris berhasil diambil.', ['produk_terlaris' => $produkTerlaris->toArray()]);

        // Hitung jumlah penjualan pada hari ini
        $jumlahPenjualanHariIni = Penjualan::whereDate('created_at', today())->count();


        // Ambil data penjualan harian (tanggal, total, dan jumlah transaksi)
        $penjualanHarian = DB::table('penjualan')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_bayar) as total_penjualan'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        Log::info('Data penjualan harian berhasil diambil.', ['penjualan_harian' => $penjualanHarian->toArray()]);

        // Simpan log aktivitas melihat statistik penjualan
        ActivityLog::create([
            'action' => 'Lihat Statistik Kasir',
            'description' => 'Kasir melihat statistik di dashboard kasir',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'waktu' => now(),
                'produk_terlaris' => $produkTerlaris->toArray(),
                'penjualan_harian' => $penjualanHarian->toArray(),
            ]
        ]);

        // Siapkan data untuk chart (grafik)
        $labels = $penjualanHarian->pluck('tanggal');
        $dataPenjualan = $penjualanHarian->pluck('total_penjualan');
        $jumlahTransaksi = $penjualanHarian->pluck('jumlah_transaksi')->toArray();

        // Kirim data ke view kasirDashboard.blade.php
        return view('kasirDashboard', compact('jumlahMember', 'jumlahPenjualan', 'produkTerlaris', 'labels', 'dataPenjualan', 'jumlahPenjualanHariIni', 'jumlahTransaksi'));
    }

    // Filter data penjualan berdasarkan pilihan waktu (hari ini, minggu lalu, dll)
    public function filterPenjualan(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter'); // Ambil nilai filter dari query string

        Log::info("Kasir memfilter penjualan dengan filter: $filter", ['user_id' => $user->id, 'email' => $user->email]);

        // Query dasar untuk penjualan harian
        $query = DB::table('penjualan')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_bayar) as total_penjualan'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy('tanggal');

        // Terapkan filter berdasarkan nilai yang dipilih
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

        // Ambil data hasil filter
        $penjualanHarian = $query->orderBy('tanggal', 'asc')->get();

        Log::info('Data penjualan setelah difilter berhasil diambil.', ['filter' => $filter, 'penjualan_harian' => $penjualanHarian->toArray()]);

        // Simpan log aktivitas filter penjualan
        ActivityLog::create([
            'action' => 'Filter Penjualan Kasir',
            'description' => "Kasir menerapkan filter: $filter",
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'filter' => $filter,
                'penjualan_harian' => $penjualanHarian->toArray(),
                'waktu' => now(),
            ]
        ]);

        // Kirim response JSON untuk ditampilkan di frontend
        return response()->json([
            'labels' => $penjualanHarian->pluck('tanggal'),
            'dataPenjualan' => $penjualanHarian->pluck('total_penjualan'),
            'jumlahTransaksi' => $penjualanHarian->pluck('jumlah_transaksi')
        ]);
    }
}
