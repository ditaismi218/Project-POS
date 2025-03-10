<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class LaporanPenjualanController extends Controller
{
    public function laporanPenjualan(Request $request)
    {
        $kategori_id = $request->input('kategori');

        $query = Produk::select(
                'produk.kode_barang', 
                'produk.nama_barang',
                'kategori_produk.nama_kategori'
            )
            ->join('detail_penjualan', 'produk.id', '=', 'detail_penjualan.produk_id')
            ->join('kategori_produk', 'produk.kategori_id', '=', 'kategori_produk.id')
            ->selectRaw('SUM(detail_penjualan.qty) as total_terjual')
            ->groupBy('produk.id', 'produk.kode_barang', 'produk.nama_barang', 'kategori_produk.nama_kategori');

        // Tambahkan filter kategori jika ada
        if (!empty($kategori_id)) {
            $query->where('produk.kategori_id', $kategori_id);
        }

        $laporan = $query->orderByDesc('total_terjual')->get();
        $totalQty = $laporan->sum('total_terjual'); // Total semua qty terjual

        // Ambil daftar kategori untuk dropdown filter
        $kategoriList = KategoriProduk::orderBy('nama_kategori')->get();

        return view('laporan.penjualan', compact('laporan', 'totalQty', 'kategoriList'));
    }
}
