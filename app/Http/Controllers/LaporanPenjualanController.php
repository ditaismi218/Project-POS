<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LaporanPenjualanController extends Controller
{
    /**
     * Menampilkan laporan penjualan produk.
     * Mendukung filter berdasarkan kategori produk.
     */
    public function laporanPenjualan(Request $request)
    {
        // Mendapatkan data user yang sedang login
        $user = Auth::user();

        // Mendapatkan ID kategori yang dipilih dari input request
        $kategori_id = $request->input('kategori');

        // Simpan log saat user mengakses laporan penjualan
        $this->saveActivityLog('Akses Laporan Penjualan', "User mengakses laporan penjualan.", [
            'user_id' => $user->id,
            'email' => $user->email,
            'kategori_id' => $kategori_id,
        ]);

        // Query untuk mengambil data penjualan produk
        $query = Produk::select(
            'produk.kode_barang', // Kode barang
            'produk.nama_barang', // Nama barang
            'kategori_produk.nama_kategori' // Nama kategori produk
        )
            // Join tabel detail_penjualan untuk menghitung total qty terjual
            ->join('detail_penjualan', 'produk.id', '=', 'detail_penjualan.produk_id')
            // Join tabel kategori_produk untuk mendapatkan kategori produk
            ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.id')
            // Menjumlahkan qty yang terjual untuk setiap produk
            ->selectRaw('SUM(detail_penjualan.qty) as total_terjual')
            // Mengelompokkan data berdasarkan produk
            ->groupBy('produk.id', 'produk.kode_barang', 'produk.nama_barang', 'kategori_produk.nama_kategori');

        // Filter berdasarkan kategori jika kategori_id tersedia
        if (!empty($kategori_id)) {
            $query->where('produk.kategori_id', $kategori_id);

            // Simpan log filter berdasarkan kategori
            $this->saveActivityLog('Filter berdasarkan Kategori', "Filter kategori ID: $kategori_id", [
                'kategori_id' => $kategori_id
            ]);
        }

        // Jalankan query dan ambil hasilnya
        $laporan = $query->orderByDesc('total_terjual')->get();

        // Hitung total kuantitas produk yang terjual
        $totalQty = $laporan->sum('total_terjual');

        // Simpan log terkait hasil laporan
        $this->saveActivityLog('Ambil Laporan Penjualan', "Menampilkan laporan dengan total data: " . $laporan->count(), [
            'total_data' => $laporan->count(),
            'total_qty' => $totalQty,
        ]);

        // Ambil daftar kategori produk untuk digunakan dalam dropdown filter
        $kategoriList = KategoriProduk::orderBy('nama_kategori')->get();

        // Kirim data laporan penjualan ke view
        return view('laporan.penjualan', compact('laporan', 'totalQty', 'kategoriList'));
    }

    /**
     * Helper untuk menyimpan activity log ke database dan Laravel log
     */
    private function saveActivityLog($action, $description, $data = [])
    {
        // Mendapatkan data user yang sedang login
        $user = Auth::user();

        // Menambahkan data user dan waktu ke log
        $data['user_id'] = $user->id;
        $data['email'] = $user->email;
        $data['waktu'] = now();

        // Simpan log ke file log Laravel
        Log::info($description, $data);

        // Simpan log ke database di tabel activity_logs
        ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'data' => $data
        ]);
    }
}