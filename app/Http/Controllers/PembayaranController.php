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
    /**
     * Menampilkan daftar pembayaran.
     * Jika terdapat filter tanggal, hanya menampilkan pembayaran dalam rentang tersebut.
     */
    public function index(Request $request)
    {
        // Logging awal akses halaman
        Log::info('Mengakses halaman daftar pembayaran', ['user_id' => auth()->id()]);

        // Simpan aktivitas akses ke log database
        ActivityLog::create([
            'action' => 'Akses Daftar Pembayaran',
            'description' => 'User mengakses halaman daftar pembayaran.',
            'data' => ['user_id' => auth()->id()]
        ]);

        // Inisialisasi query pembayaran dengan relasi penjualan
        $query = Pembayaran::with('penjualan')->orderBy('created_at', 'desc');

        // Jika pengguna mengisi filter tanggal, lakukan filter berdasarkan tanggal faktur
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

            Log::info('Filter tanggal pembayaran diterapkan', [
                'tanggal_mulai' => $tanggalMulai->toDateTimeString(),
                'tanggal_selesai' => $tanggalSelesai->toDateTimeString(),
            ]);

            // Filter berdasarkan tanggal faktur dari penjualan
            $query->whereHas('penjualan', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tgl_faktur', [$tanggalMulai, $tanggalSelesai]);
            });
        }

        $pembayaran = $query->get();
        Log::info('Jumlah pembayaran yang ditemukan', ['jumlah_pembayaran' => $pembayaran->count()]);

        return view('pembayaran.index', compact('pembayaran'));
    }

    /**
     * Menampilkan halaman form untuk membuat pembayaran baru.
     * Data penjualan yang berkaitan akan diload untuk ditampilkan.
     */
    public function create(Penjualan $penjualan)
    {
        Log::info('Mengakses halaman pembuatan pembayaran', [
            'user_id' => auth()->id(),
            'penjualan_id' => $penjualan->id ?? 'Tidak ditemukan'
        ]);

        // Jika penjualan tidak ditemukan, kembalikan ke halaman utama
        if (!$penjualan) {
            Log::warning('Penjualan tidak ditemukan', ['penjualan_id' => $penjualan->id]);
            return redirect()->route('penjualan.index')->with('error', 'Data penjualan tidak ditemukan.');
        }
        // Load data produk dari detail penjualan dan penerimaan barang terbaru
        $penjualan->load([
            'detailPenjualan.produk.penerimaanBarang' => function ($query) {
                $query->latest('tgl_masuk');
            }
        ]);

        return view('pembayaran.create', compact('penjualan'));
    }

    /**
     * Menyimpan data pembayaran baru ke database.
     * Termasuk perhitungan kembalian dan update status penjualan.
     */
    public function store(Request $request)
    {
        Log::info('Proses pembayaran dimulai', [
            'user_id' => auth()->id(),
            'penjualan_id' => $request->penjualan_id,
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

         // Validasi input form
        $request->validate([
            'penjualan_id' => 'required',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string'
        ]);

         // Cari penjualan terkait
        $penjualan = Penjualan::find($request->penjualan_id);
        if (!$penjualan) {
            Log::error('Penjualan tidak ditemukan', ['penjualan_id' => $request->penjualan_id]);
            return redirect()->back()->with('error', 'Penjualan tidak ditemukan.');
        }

         // Hitung total pembayaran sebelumnya
        $totalDibayar = Pembayaran::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
        $jumlah_bayar = $request->jumlah_bayar;

        // Hitung kembalian jika ada
        $kembalian = ($jumlah_bayar + $totalDibayar) - $penjualan->total_bayar;

          // Jika pembayaran kurang dari total, kembalikan error
        if ($jumlah_bayar + $totalDibayar < $penjualan->total_bayar) {
            Log::warning('Pembayaran kurang dari total bayar', [
                'penjualan_id' => $penjualan->id,
                'total_bayar' => $penjualan->total_bayar,
                'total_dibayar' => $totalDibayar,
                'jumlah_bayar' => $jumlah_bayar,
            ]);
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total pembayaran.');
        }

         // Simpan data pembayaran
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

        // Jika pembayaran sudah lunas, update status penjualan
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
