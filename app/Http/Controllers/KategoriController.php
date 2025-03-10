<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use App\Models\Produk;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriProduk::orderBy('created_at', 'desc')->get();
        return view('kategori.index', compact('kategori'));
    }

    // protected $fillable = [
    //     'kode_voucher',
    //     'jenis',
    //     'nilai',
    //     'min_belanja',
    //     'berlaku_hingga'
    // ];
   
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        // dd($request->all());

        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
        ]);
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = KategoriProduk::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriProduk::findOrFail($id);
        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
