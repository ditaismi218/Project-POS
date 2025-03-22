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
    public function laporanPenjualan(Request $request)
    {
        $user = Auth::user();
        $kategori_id = $request->input('kategori');

        // Simpan log akses laporan
        $this->saveActivityLog('Akses Laporan Penjualan', "User mengakses laporan penjualan.", [
            'user_id' => $user->id,
            'email' => $user->email,
            'kategori_id' => $kategori_id,
        ]);

        // Query untuk mengambil data penjualan
        $query = Produk::select(
                'produk.kode_barang', 
                'produk.nama_barang',
                'kategori_produk.nama_kategori'
            )
            ->join('detail_penjualan', 'produk.id', '=', 'detail_penjualan.produk_id')
            ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.id')
            ->selectRaw('SUM(detail_penjualan.qty) as total_terjual')
            ->groupBy('produk.id', 'produk.kode_barang', 'produk.nama_barang', 'kategori_produk.nama_kategori');

        if (!empty($kategori_id)) {
            $query->where('produk.kategori_id', $kategori_id);
            $this->saveActivityLog('Filter berdasarkan Kategori', "Filter kategori ID: $kategori_id", [
                'kategori_id' => $kategori_id
            ]);
        }

        // Jalankan query dan ambil hasilnya
        $laporan = $query->orderByDesc('total_terjual')->get();
        $totalQty = $laporan->sum('total_terjual');

        // Simpan log hasil laporan
        $this->saveActivityLog('Ambil Laporan Penjualan', "Menampilkan laporan dengan total data: " . $laporan->count(), [
            'total_data' => $laporan->count(),
            'total_qty' => $totalQty,
        ]);

        // Ambil daftar kategori untuk dropdown filter
        $kategoriList = KategoriProduk::orderBy('nama_kategori')->get();

        return view('laporan.penjualan', compact('laporan', 'totalQty', 'kategoriList'));
    }

    /**
     * Helper untuk menyimpan activity log ke database dan Laravel log
     */
    private function saveActivityLog($action, $description, $data = [])
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['email'] = $user->email;
        $data['waktu'] = now();

        // Simpan ke Laravel log
        Log::info($description, $data);

        // Simpan ke database
        ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'data' => $data
        ]);
    }
}
