<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Log permintaan yang diterima
        Log::info('Menerima permintaan laporan transaksi', [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Akses Laporan Transaksi',
            'description' => 'User mengakses laporan transaksi.',
            'data' => [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai
            ]
        ]);

        // Query dasar dengan relasi
        $query = Penjualan::with(['user', 'member', 'pembayaran', 'detailPenjualan.produk'])
            ->orderBy('tgl_faktur', 'desc');

        // Filter berdasarkan tanggal jika ada input
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

            // Log filter tanggal yang diterapkan
            Log::info('Menerapkan filter tanggal untuk laporan transaksi', [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai
            ]);

            // Simpan log ke database
            ActivityLog::create([
                'action' => 'Filter Laporan Transaksi',
                'description' => "Filter transaksi dari $tanggalMulai sampai $tanggalSelesai",
                'data' => [
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai
                ]
            ]);

            $query->whereBetween('tgl_faktur', [$tanggalMulai, $tanggalSelesai]);
        }

        // Ambil data transaksi setelah filter diterapkan
        $transaksi = $query->get();

        // Log jumlah transaksi yang ditemukan
        Log::info('Laporan transaksi berhasil diambil', [
            'total_transaksi' => $transaksi->count()
        ]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Ambil Laporan Transaksi',
            'description' => "Menampilkan laporan transaksi dengan total: " . $transaksi->count(),
            'data' => ['total_transaksi' => $transaksi->count()]
        ]);

        return view('laporan.transaksi', compact('transaksi'));
    }

    public function show($id)
    {
        // Log permintaan detail transaksi
        Log::info('Menerima permintaan detail transaksi', ['transaksi_id' => $id]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Akses Detail Transaksi',
            'description' => "User melihat detail transaksi dengan ID: $id",
            'data' => ['transaksi_id' => $id]
        ]);

        $transaksi = Penjualan::with(['detailPenjualan.produk', 'pembayaran', 'member', 'user'])->findOrFail($id);

        // Log setelah detail transaksi berhasil diambil
        Log::info('Detail transaksi berhasil diambil', [
            'transaksi_id' => $transaksi->id,
            'member_id' => $transaksi->member_id,
            'total_bayar' => $transaksi->total_bayar
        ]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Detail Transaksi Berhasil Diambil',
            'description' => "Detail transaksi ID: $id berhasil ditampilkan.",
            'data' => [
                'transaksi_id' => $transaksi->id,
                'member_id' => $transaksi->member_id,
                'total_bayar' => $transaksi->total_bayar
            ]
        ]);

        return view('laporan.detail_transaksi', compact('transaksi'));
    }
}