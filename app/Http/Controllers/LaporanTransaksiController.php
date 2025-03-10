<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar dengan relasi
        $query = Penjualan::with(['user', 'member', 'pembayaran', 'detailPenjualan.produk'])
            ->orderBy('tgl_faktur', 'desc');

        // Filter berdasarkan tanggal jika ada input
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $query->whereDate('tgl_faktur', $request->tanggal);
        }

        // Ambil data transaksi setelah filter diterapkan
        $transaksi = $query->get();

        return view('laporan.transaksi', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = Penjualan::with(['detailPenjualan.produk', 'pembayaran', 'member', 'user'])->findOrFail($id);
        return view('laporan.detail_transaksi', compact('transaksi'));
    }

}

