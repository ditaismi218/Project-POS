<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Tambahkan Log Facade

class ProdukController extends Controller
{
    public function index()
    {
        $products = Produk::with('kategori')->orderBy('created_at', 'desc')->get();
        $categories = KategoriProduk::all(); // Untuk modal tambah
        
        Log::info('Menampilkan produk dan kategori', ['total_produk' => $products->count(), 'total_kategori' => $categories->count()]);

        ActivityLog::create([
            'action' => 'view',
            'description' => 'Melihat daftar produk',
            'data' => json_encode(['user_id' => auth()->id(), 'total_produk' => $products->count()])
        ]);

        return view('produk.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:produk,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_produk,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'satuan' => 'required|string|max:50',
        ]);

        Log::info('Validasi input produk berhasil', ['input' => $validated]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
            Log::info('Gambar produk disimpan', ['gambar' => $validated['gambar']]);
        }

        $produk = Produk::create($validated);

        Log::info('Produk baru berhasil ditambahkan', ['produk' => $validated]);

        // Catat ke dalam ActivityLog
        ActivityLog::create([
            'action' => 'create',
            'description' => 'Menambahkan produk baru',
            'data' => json_encode(['produk_id' => $produk->id, 'nama_barang' => $produk->nama_barang, 'user_id' => auth()->id()])
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $oldData = $produk->toArray(); // Simpan data lama sebelum diupdate

        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:produk,kode_barang,' . $id,
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_produk,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
            'satuan' => 'required|string|max:50',
        ]);

        Log::info('Validasi input produk untuk pembaruan berhasil', ['input' => $validated]);

        // Cek apakah ada gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
                Log::info('Gambar lama dihapus', ['gambar' => $produk->gambar]);
            }
            // Simpan gambar baru
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
            Log::info('Gambar baru disimpan', ['gambar' => $validated['gambar']]);
        }

        $produk->update($validated);

        Log::info('Produk berhasil diperbarui', ['produk_id' => $produk->id]);

        // Catat perubahan di ActivityLog
        ActivityLog::create([
            'action' => 'update',
            'description' => 'Memperbarui produk',
            'data' => json_encode([
                'user_id' => auth()->id(),
                'produk_id' => $produk->id,
                'before' => $oldData,
                'after' => $produk->toArray()
            ])
        ]);
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            Log::warning('Produk tidak ditemukan untuk dihapus', ['produk_id' => $id]);
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan');
        }

        // Hapus gambar dari storage jika ada
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
            Log::info('Gambar produk dihapus dari storage', ['gambar' => $produk->gambar]);
        }

        $produk->delete();

        Log::info('Produk berhasil dihapus', ['produk_id' => $produk->id]);
        ActivityLog::create([
            'action' => 'delete',
            'description' => 'Menghapus produk',
            'data' => json_encode(['produk_id' => $produk->id, 'nama_barang' => $produk->nama_barang, 'user_id' => auth()->id()])
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
