<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Support\Facades\Storage;

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
           
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
            'satuan' => 'required|string|max:50',
        ]);

        // Simpan gambar ke storage jika ada
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create($validated);
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    // Mengupdate data produk
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
    
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:produk,kode_barang,' . $id,
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_produk,id',
          
            
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
            'satuan' => 'required|string|max:50',
        ]);
    
        // Cek apakah ada gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }
            // Simpan gambar baru
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }
    
        $produk->update($validated);
    
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');
    }    
    

    // Menghapus produk
    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan');
        }
    
        // Hapus gambar dari storage jika ada
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }
    
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
    
}
