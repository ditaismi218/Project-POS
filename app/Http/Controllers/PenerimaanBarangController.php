<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'user_id' => 'required',
            'supplier_id' => 'required',
            'produk_id' => 'required',
            // 'kode_penerimaan' => 'required',
            'tgl_masuk' => 'required',
            'harga_jual' => 'required',
            'harga_satuan' => 'required',
            'qty' => 'required',
            // 'harga_total' => 'required',
            'expired_date' => 'required',
        ]);
        // dd(date('dmyhms'));
        
        PenerimaanBarang::create([
            'user_id' => $request->user_id,
            'supplier_id' => $request->supplier_id,
            'produk_id' => $request->produk_id,
            'kode_penerimaan' => date('dmyhms'),
            'tgl_masuk' => $request->tgl_masuk,
            'harga_jual' => $request->harga_jual,
            'harga_satuan' => $request->harga_satuan,
            'qty' => $request->qty,
            'harga_total' => $request->qty * $request->harga_satuan,
            'expired_date' => $request->expired_date,
        ]);
        
        return redirect()->route('penerimaan_barang.index')->with('success', 'Penerimaan barang berhasil ditambahkan');
    }
    

    public function update(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'user_id' => 'required',
            'supplier_id' => 'required',
            'produk_id' => 'required',
            // 'kode_penerimaan' => 'required',
            'tgl_masuk' => 'required',
            'harga_jual' => 'required',
            'harga_satuan' => 'required',
            'qty' => 'required',
            // 'harga_total' => 'required',
            'expired_date' => 'required',
        ]);
        
        // dd($request->all());

        $penerimaan = PenerimaanBarang::findOrFail($request->id);
        $penerimaan->update([
            'user_id' => $request->user_id,
            'supplier_id' => $request->supplier_id,
            'produk_id' => $request->produk_id,
            'tgl_masuk' => $request->tgl_masuk,
            'harga_jual' => $request->harga_jual,
            'harga_satuan' => $request->harga_satuan,
            'qty' => $request->qty,
            'harga_total' => $request->qty * $request->harga_satuan,
            'expired_date' => $request->expired_date,
        ]);

        return redirect()->route('penerimaan_barang.index')->with('success', 'Penerimaan barang berhasil diperbarui');
    
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
