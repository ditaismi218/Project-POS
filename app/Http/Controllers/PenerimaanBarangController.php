<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenerimaanBarangController extends Controller
{
    public function index()
    {
        Log::info('Menampilkan semua data penerimaan barang.');

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Akses Data Penerimaan Barang',
            'description' => 'User mengakses daftar penerimaan barang.',
            'data' => ['user_id' => auth()->id()]
        ]);

        $penerimaan = PenerimaanBarang::selectRaw('
            produk_id, 
            supplier_id, 
            SUM(qty) as total_qty, 
            SUM(harga_total) as total_harga
        ')
            ->groupBy('produk_id', 'supplier_id')
            ->with(['produk', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->get();

        $suppliers = Supplier::all();
        $products = Produk::all();

        return view('penerimaan_barang.index', compact('penerimaan', 'suppliers', 'products'));
    }

    public function create()
    {
        Log::info('Menampilkan halaman pembuatan penerimaan barang.');

        $suppliers = Supplier::all();
        $products = Produk::all();

        return view('penerimaan_barang.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        Log::info('Menerima request untuk menambahkan penerimaan barang.', ['data' => $request->all()]);

        $request->validate([
            'supplier_id' => 'required',
            'tgl_masuk' => 'required|date',
            'produk_id' => 'required|array',
            'qty' => 'required|array',
            'harga_jual' => 'required|array',
            'harga_satuan' => 'required|array',
            'expired_date' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $lastPenerimaan = DB::table('penerimaan_barang')
                ->whereDate('created_at', now()->toDateString())
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $newNumber = $lastPenerimaan ? str_pad((int)substr($lastPenerimaan->kode_penerimaan, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
            $kodePenerimaan = 'PNR-' . now()->format('Ymd') . '-' . $newNumber;

            foreach ($request->produk_id as $key => $produk_id) {
                PenerimaanBarang::create([
                    'kode_penerimaan' => $kodePenerimaan++,
                    'user_id' => auth()->id(),
                    'supplier_id' => $request->supplier_id,
                    'tgl_masuk' => $request->tgl_masuk,
                    'produk_id' => $produk_id,
                    'qty' => intval($request->qty[$key]),
                    'harga_jual' => floatval($request->harga_jual[$key]),
                    'harga_satuan' => floatval($request->harga_satuan[$key]),
                    'harga_total' => intval($request->qty[$key]) * floatval($request->harga_satuan[$key]),
                    'expired_date' => $request->expired_date[$key],
                ]);
            }

            DB::commit();
            Log::info('Penerimaan barang berhasil disimpan.', ['kode_penerimaan' => $kodePenerimaan]);

            // Simpan log ke database
            ActivityLog::create([
                'action' => 'Tambah Penerimaan Barang',
                'description' => "Penerimaan barang berhasil disimpan dengan kode: {$kodePenerimaan}",
                'data' => ['kode_penerimaan' => $kodePenerimaan, 'user_id' => auth()->id()]
            ]);

            return redirect()->route('penerimaan_barang.index')->with('success', 'Penerimaan barang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan penerimaan barang.', ['error' => $e->getMessage(), 'data' => $request->all()]);

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        Log::info('Menampilkan detail penerimaan barang.', ['produk_id' => $id]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Lihat Detail Penerimaan Barang',
            'description' => "User melihat detail penerimaan barang untuk produk ID: {$id}",
            'data' => ['produk_id' => $id, 'user_id' => auth()->id()]
        ]);

        $penerimaan = PenerimaanBarang::with('user', 'supplier', 'produk')->where('produk_id', $id)->get();
        $suppliers = Supplier::all();
        $products = Produk::all();

        return view('penerimaan_barang.show', compact('penerimaan', 'suppliers', 'products'));
    }

    public function destroy($id)
    {
        try {
            $penerimaan_barang = PenerimaanBarang::findOrFail($id);
            $penerimaan_barang->delete();

            Log::info('Berhasil menghapus penerimaan barang.', ['id' => $id]);

            // Simpan log ke database
            ActivityLog::create([
                'action' => 'Hapus Penerimaan Barang',
                'description' => "Penerimaan barang dengan ID: {$id} telah dihapus.",
                'data' => ['penerimaan_barang_id' => $id, 'user_id' => auth()->id()]
            ]);

            return redirect()->route('penerimaan_barang.index')->with('success', 'Penerimaan barang berhasil dihapus (soft delete)');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus penerimaan barang.', ['id' => $id, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
