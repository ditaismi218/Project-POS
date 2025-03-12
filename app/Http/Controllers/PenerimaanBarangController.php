<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenerimaanBarangController extends Controller
{
    public function index()
    {
        $penerimaan = PenerimaanBarang::selectRaw('
            produk_id, 
            supplier_id, 
            SUM(qty) as total_qty, 
            SUM(harga_total) as total_harga
        ')
            ->groupBy('produk_id', 'supplier_id')
            ->with(['produk', 'supplier'])
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan waktu terbaru
            ->get();

        $suppliers = Supplier::all();
        $products = Produk::all();

        return view('penerimaan_barang.index', compact('penerimaan', 'suppliers', 'products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Produk::all();

        return view('penerimaan_barang.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'tgl_masuk' => 'required|date',
            'produk_id' => 'required|array',
            'qty' => 'required|array',
            'harga_jual' => 'required|array',
            'harga_satuan' => 'required|array',
            'expired_date' => 'required|array',
        ]);
        // dd($request->all());

        try {
            DB::beginTransaction(); // Mulai transaksi
    
            // Kunci tabel supaya tidak ada transaksi lain yang mengambil nomor urut bersamaan
            $lastPenerimaan = DB::table('penerimaan_barang')
                ->whereDate('created_at', now()->toDateString()) // Lebih akurat untuk pencarian tanggal
                ->orderBy('id', 'desc')
                ->lockForUpdate() // Kunci baris ini agar tidak ada transaksi lain yang mengambil data bersamaan
                ->first();
    
            if ($lastPenerimaan) {
                $lastNumber = (int) substr($lastPenerimaan->kode_penerimaan, -3);
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }
    
            $kodePenerimaan = 'PNR-' . now()->format('Ymd') . '-' . $newNumber;
    
            foreach ($request->produk_id as $key => $produk_id) {
                PenerimaanBarang::create([
                    'kode_penerimaan' => $kodePenerimaan ++,
                    'user_id' => auth()->id(), // Menggunakan auth()->id() agar lebih aman
                    'supplier_id' => $request->supplier_id,
                    'tgl_masuk' => $request->tgl_masuk,
                    'produk_id' => $produk_id,
                    'qty' => intval($request->qty[$key] ?? 0), // Pastikan angka
                    'harga_jual' => floatval($request->harga_jual[$key] ?? 0),
                    'harga_satuan' => floatval($request->harga_satuan[$key] ?? 0),
                    'harga_total' => intval($request->qty[$key] ?? 0) * floatval($request->harga_satuan[$key] ?? 0),
                    'expired_date' => $request->expired_date[$key] ?? null,
                ]);
            }
    
            DB::commit(); // Simpan transaksi
    
            return redirect()->route('penerimaan_barang.index')->with('success', 'Penerimaan barang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            
            // Log error
            Log::error('Error saat menyimpan penerimaan barang: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all() // Log data request yang dikirim
            ]);
    
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
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
            return redirect()->route('penerimaan_barang.index')->with('success', 'Penerimaan barang berhasil dihapus (soft delete)');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
