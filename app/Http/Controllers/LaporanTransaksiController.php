<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class LaporanTransaksiController extends Controller
{
    /**
     * Menampilkan laporan transaksi berdasarkan filter tanggal (jika ada).
     */
    public function index(Request $request)
    {
        // Log permintaan yang diterima dengan parameter tanggal mulai dan tanggal selesai
        Log::info('Menerima permintaan laporan transaksi', [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        // Simpan log aktivitas ke database untuk mencatat bahwa user mengakses laporan transaksi
        ActivityLog::create([
            'action' => 'Akses Laporan Transaksi',
            'description' => 'User mengakses laporan transaksi.',
            'data' => [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai
            ]
        ]);

        // Query dasar untuk mengambil data transaksi dengan relasi yang diperlukan
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

        // Kembalikan view dengan data transaksi yang telah diambil
        return view('laporan.transaksi', compact('transaksi'));
    }

    /**
     * Menampilkan detail transaksi berdasarkan ID transaksi.
     */
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

        // Ambil data detail transaksi beserta relasi yang diperlukan
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

        // Kembalikan view dengan data detail transaksi
        return view('laporan.detail_transaksi', compact('transaksi'));
    }

    public function printStruk($id)
    {
        $transaksi = Penjualan::with(['detailPenjualan.produk', 'user', 'member', 'pembayaran'])->findOrFail($id);

        try {
            // Ganti dengan nama printer kamu (lihat di Control Panel -> Devices and Printers)
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("GoMart\n");
            $printer->setEmphasis(false);
            $printer->text("0812-3456-7890\nJl. Contoh No. 123\n");
            $printer->feed();

            // Informasi Transaksi
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No Faktur : " . $transaksi->no_faktur . "\n");
            $printer->text("Tanggal   : " . $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') . "\n");
            $printer->text("Pelanggan : " . ($transaksi->member->nama ?? 'Umum') . "\n");
            $printer->text("Kasir     : " . $transaksi->user->name . "\n");
            $printer->feed();

            // Garis Pemisah
            $printer->text("--------------------------------\n");

            // Daftar Produk
            foreach ($transaksi->detailPenjualan as $item) {
                $hargaJual = number_format($item->harga_jual, 0, ',', '.');
                $subtotal = number_format($item->sub_total, 0, ',', '.');

                // Format Produk: Nama Produk, Qty, Harga Jual, Subtotal
                $printer->text("{$item->produk->nama_barang}\n");
                $printer->text("{$item->qty} x {$hargaJual}   Rp {$subtotal}\n");

                // Margin bawah setiap produk
                $printer->feed(1);
            }

            // Garis Pemisah
            $printer->text("--------------------------------\n");

            // Total Bayar, Jumlah Bayar, Kembalian
            $printer->setEmphasis(true);
            $printer->text("Total Bayar : Rp " . number_format($transaksi->total_bayar, 0, ',', '.') . "\n");
            $printer->setEmphasis(false);
            $printer->text("Jumlah Bayar: Rp " . number_format($transaksi->pembayaran->jumlah_bayar ?? 0, 0, ',', '.') . "\n");
            $printer->text("Kembalian   : Rp " . number_format($transaksi->pembayaran->kembalian ?? 0, 0, ',', '.') . "\n");
            $printer->text("Metode      : " . ucfirst($transaksi->pembayaran->metode_pembayaran ?? '-') . "\n");

            // Footer
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih telah berbelanja\n");
            $printer->pulse();
            $printer->cut();
            $printer->close();

            return back()->with('success', 'Struk berhasil dicetak!');
        } catch (\Exception $e) {
            Log::error('Gagal mencetak struk.', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mencetak: ' . $e->getMessage());
        }
    }


}