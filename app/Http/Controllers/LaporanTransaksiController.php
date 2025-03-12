<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use Illuminate\Support\Carbon;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar dengan relasi
        $query = Penjualan::with(['user', 'member', 'pembayaran', 'detailPenjualan.produk'])
            ->orderBy('tgl_faktur', 'desc');

        // Filter berdasarkan tanggal jika ada input
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay(); // 2024-03-08 00:00:00
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay(); // 2024-03-09 23:59:59
        
            $query->whereBetween('tgl_faktur', [$tanggalMulai, $tanggalSelesai]);
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

