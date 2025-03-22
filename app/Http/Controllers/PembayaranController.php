<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Mengakses halaman daftar pembayaran', ['user_id' => auth()->id()]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Akses Daftar Pembayaran',
            'description' => 'User mengakses halaman daftar pembayaran.',
            'data' => ['user_id' => auth()->id()]
        ]);

        $query = Pembayaran::with('penjualan')->orderBy('created_at', 'desc');

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

            Log::info('Filter tanggal pembayaran diterapkan', [
                'tanggal_mulai' => $tanggalMulai->toDateTimeString(),
                'tanggal_selesai' => $tanggalSelesai->toDateTimeString(),
            ]);

            $query->whereHas('penjualan', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tgl_faktur', [$tanggalMulai, $tanggalSelesai]);
            });
        }

        $pembayaran = $query->get();
        Log::info('Jumlah pembayaran yang ditemukan', ['jumlah_pembayaran' => $pembayaran->count()]);

        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create(Penjualan $penjualan)
    {
        Log::info('Mengakses halaman pembuatan pembayaran', [
            'user_id' => auth()->id(),
            'penjualan_id' => $penjualan->id ?? 'Tidak ditemukan'
        ]);

        if (!$penjualan) {
            Log::warning('Penjualan tidak ditemukan', ['penjualan_id' => $penjualan->id]);
            return redirect()->route('penjualan.index')->with('error', 'Data penjualan tidak ditemukan.');
        }

        $penjualan->load([
            'detailPenjualan.produk.penerimaanBarang' => function ($query) {
                $query->latest('tgl_masuk');
            }
        ]);

        return view('pembayaran.create', compact('penjualan'));
    }

    public function store(Request $request)
    {
        Log::info('Proses pembayaran dimulai', [
            'user_id' => auth()->id(),
            'penjualan_id' => $request->penjualan_id,
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        $request->validate([
            'penjualan_id' => 'required',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string'
        ]);

        $penjualan = Penjualan::find($request->penjualan_id);
        if (!$penjualan) {
            Log::error('Penjualan tidak ditemukan', ['penjualan_id' => $request->penjualan_id]);
            return redirect()->back()->with('error', 'Penjualan tidak ditemukan.');
        }

        $totalDibayar = Pembayaran::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
        $jumlah_bayar = $request->jumlah_bayar;
        $kembalian = ($jumlah_bayar + $totalDibayar) - $penjualan->total_bayar;

        if ($jumlah_bayar + $totalDibayar < $penjualan->total_bayar) {
            Log::warning('Pembayaran kurang dari total bayar', [
                'penjualan_id' => $penjualan->id,
                'total_bayar' => $penjualan->total_bayar,
                'total_dibayar' => $totalDibayar,
                'jumlah_bayar' => $jumlah_bayar,
            ]);
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total pembayaran.');
        }

        $pembayaran = Pembayaran::create([
            'penjualan_id' => $penjualan->id,
            'jumlah_bayar' => $jumlah_bayar,
            'kembalian' => max($kembalian, 0),
            'metode_pembayaran' => $request->metode_pembayaran
        ]);

        Log::info('Pembayaran berhasil', [
            'pembayaran_id' => $pembayaran->id,
            'penjualan_id' => $penjualan->id,
            'jumlah_bayar' => $jumlah_bayar,
            'kembalian' => $pembayaran->kembalian,
        ]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Tambah Pembayaran',
            'description' => "Pembayaran berhasil untuk penjualan ID: {$penjualan->id}",
            'data' => [
                'pembayaran_id' => $pembayaran->id,
                'penjualan_id' => $penjualan->id,
                'jumlah_bayar' => $jumlah_bayar,
                'kembalian' => $pembayaran->kembalian,
                'metode_pembayaran' => $request->metode_pembayaran
            ]
        ]);

        $totalDibayar += $jumlah_bayar;

        if ($totalDibayar >= $penjualan->total_bayar) {
            $penjualan->update(['status' => 'lunas']);
            Log::info('Status penjualan diperbarui menjadi lunas', ['penjualan_id' => $penjualan->id]);

            // Simpan log ke database
            ActivityLog::create([
                'action' => 'Penjualan Lunas',
                'description' => "Penjualan ID {$penjualan->id} telah lunas.",
                'data' => ['penjualan_id' => $penjualan->id]
            ]);
        }

        return back()->with([
            'success' => 'Pembayaran berhasil!',
            'detail_url' => route('transaksi.detail', ['id' => $penjualan->id])
        ]);
    }
}
