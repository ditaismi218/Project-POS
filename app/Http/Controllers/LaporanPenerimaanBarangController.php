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
    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $supplierId = $request->input('supplier_id');

        // Simpan log aktivitas
        $this->saveActivityLog('Akses Laporan Penerimaan Barang', "User mengakses laporan dengan filter.", [
            'user_id' => $user->id,
            'email' => $user->email,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'supplier_id' => $supplierId,
        ]);

        // Query data penerimaan barang
        $query = PenerimaanBarang::with(['produk', 'supplier']);

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_masuk', [$startDate, $endDate]);
            $this->saveActivityLog('Filter berdasarkan Tanggal', "Filter dari $startDate sampai $endDate", [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
            $this->saveActivityLog('Filter berdasarkan Supplier', "Filter berdasarkan supplier ID: $supplierId", [
                'supplier_id' => $supplierId
            ]);
        }

        $laporan = $query->orderBy('tgl_masuk', 'desc')->get();

        // Hitung total qty dan total harga
        $totalQty = $laporan->sum('qty');
        $totalHarga = $laporan->sum('harga_total');

        // Simpan log jumlah data dan total nilai
        $this->saveActivityLog('Ambil Laporan Penerimaan Barang', "Menampilkan laporan dengan total data: " . $laporan->count(), [
            'total_data' => $laporan->count(),
            'total_qty' => $totalQty,
            'total_harga' => $totalHarga,
        ]);

        $supplierList = Supplier::all();

        return view('laporan.penerimaan_barang', compact('laporan', 'totalQty', 'totalHarga', 'supplierList'));
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
