<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;

class ProdukController extends Controller
{
    public function index()
    {
        $products = Produk::with('kategori')->get();
        $categories = KategoriProduk::all(); // Untuk modal tambah
        return view('produk.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:produk,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_produk,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        Produk::create($validated);
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    // Mengupdate data produk
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'satuan' => $request->satuan,
        ]);
    
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');
    }
    

    // Menghapus produk
    public function destroy($id)
    {
        $product = Produk::find($id);
        if (!$product) {
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan');
        }

        $product->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
