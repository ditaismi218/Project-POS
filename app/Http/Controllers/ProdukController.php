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
    /**
     * Menampilkan daftar produk beserta kategorinya.
     */
    public function index()
    {
        // Ambil semua produk beserta relasi kategorinya, urutkan berdasarkan tanggal terbaru
        $products = Produk::with('kategori')->orderBy('created_at', 'desc')->get();
        // Ambil semua kategori untuk keperluan modal tambah
        $categories = KategoriProduk::all();

        Log::info('Menampilkan produk dan kategori', ['total_produk' => $products->count(), 'total_kategori' => $categories->count()]);

        ActivityLog::create([
            'action' => 'view',
            'description' => 'Melihat daftar produk',
            'data' => json_encode(['user_id' => auth()->id(), 'total_produk' => $products->count()])
        ]);

        return view('produk.index', compact('products', 'categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            // 'kode_barang' => 'required|string|unique:produk,kode_barang',
            'kode_barang' => 'nullable',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_produk,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'satuan' => 'required|string|max:50',
        ]);

        Log::info('Validasi input produk berhasil', ['input' => $validated]);

        // Jika ada gambar, simpan ke penyimpanan dan tambahkan ke data
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
            Log::info('Gambar produk disimpan', ['gambar' => $validated['gambar']]);
        }

        // Tambahkan barcode otomatis dari kode_barang
        $validated['barcode'] = $validated['kode_barang'];
        
        // Simpan data produk ke database
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

    /**
     * Memperbarui data produk berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        // Temukan produk berdasarkan ID, jika tidak ditemukan akan throw 404
        $produk = Produk::findOrFail($id);
        $oldData = $produk->toArray(); // Simpan data lama sebelum diupdate

        // Validasi input
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

        // Update data produk
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

    /**
     * Menghapus produk berdasarkan ID.
     */
    public function destroy($id)
    {
        // Temukan produk berdasarkan ID
        $produk = Produk::find($id);

        // Jika tidak ditemukan, log dan redirect
        if (!$produk) {
            Log::warning('Produk tidak ditemukan untuk dihapus', ['produk_id' => $id]);
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan');
        }

        // Jika produk punya gambar, hapus dari storage
        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
            Log::info('Gambar produk dihapus dari storage', ['gambar' => $produk->gambar]);
        }

        // Hapus produk dari database
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
