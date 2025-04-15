<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LaporanPenerimaanBarangController extends Controller
{
    /**
     * Menampilkan halaman laporan penerimaan barang.
     * Mendukung filter berdasarkan tanggal dan supplier.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil data filter dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $supplierId = $request->input('supplier_id');

        // Simpan log saat user mengakses laporan
        $this->saveActivityLog('Akses Laporan Penerimaan Barang', "User mengakses laporan dengan filter.", [
            'user_id' => $user->id,
            'email' => $user->email,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'supplier_id' => $supplierId,
        ]);

        // Mulai query data penerimaan barang beserta relasi produk dan supplier
        $query = PenerimaanBarang::with(['produk', 'supplier']);

        // Filter berdasarkan rentang tanggal jika tersedia
        if ($startDate && $endDate) {
            $query->whereBetween('tgl_masuk', [$startDate, $endDate]);

            // Log filter berdasarkan tanggal
            $this->saveActivityLog('Filter berdasarkan Tanggal', "Filter dari $startDate sampai $endDate", [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        // Filter berdasarkan supplier jika tersedia
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);

            // Log filter berdasarkan supplier
            $this->saveActivityLog('Filter berdasarkan Supplier', "Filter berdasarkan supplier ID: $supplierId", [
                'supplier_id' => $supplierId
            ]);
        }

        // Ambil hasil query dan urutkan berdasarkan tanggal masuk
        $laporan = $query->orderBy('tgl_masuk', 'desc')->get();

        // Hitung total kuantitas dan total harga dari semua penerimaan// Hitung total qty dan total harga
        $totalQty = $laporan->sum('qty');
        $totalHarga = $laporan->sum('harga_total');

        // Simpan log terkait jumlah data dan nilai total
        $this->saveActivityLog('Ambil Laporan Penerimaan Barang', "Menampilkan laporan dengan total data: " . $laporan->count(), [
            'total_data' => $laporan->count(),
            'total_qty' => $totalQty,
            'total_harga' => $totalHarga,
        ]);

        // Ambil daftar semua supplier untuk digunakan pada filter di view
        $supplierList = Supplier::all();

        // Kirim data ke view laporan
        return view('laporan.penerimaan_barang', compact('laporan', 'totalQty', 'totalHarga', 'supplierList'));
    }

    /**
     * Menyimpan log aktivitas ke dalam tabel ActivityLog dan file log Laravel.
     *
     * @param string $action Aksi yang dilakukan
     * @param string $description Deskripsi aktivitas
     * @param array $data Data tambahan yang ingin dicatat
     */
    private function saveActivityLog($action, $description, $data = [])
    {
        $user = Auth::user();

        // Tambahkan data user dan waktu ke log
        $data['user_id'] = $user->id;
        $data['email'] = $user->email;
        $data['waktu'] = now();

        // Simpan ke file log Laravel
        Log::info($description, $data);

        // Simpan ke database (tabel activity_logs)
        ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'data' => $data
        ]);
    }
}
