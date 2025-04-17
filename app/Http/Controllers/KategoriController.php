<?php

namespace App\Http\Controllers;

use App\Imports\KategoriImport;
use App\Models\KategoriProduk;
use App\Models\ActivityLog; // Import Model ActivityLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class KategoriController extends Controller
{
    /**
     * Menampilkan halaman daftar kategori.
     */
    public function index()
    {
        $user = Auth::user();  // Ambil data user yang sedang login
        Log::info('Memuat halaman daftar kategori.', ['user_id' => $user->id, 'email' => $user->email]);

        // Ambil semua kategori yang belum dihapus (soft delete)
        $kategori = KategoriProduk::orderBy('created_at', 'desc')->whereNull('deleted_at')->get();

        Log::info('Data kategori berhasil diambil.', ['jumlah_kategori' => $kategori->count()]);

        // Simpan aktivitas user ke dalam tabel log aktivitas
        ActivityLog::create([
            'action' => 'Lihat Daftar Kategori',
            'description' => 'User melihat daftar kategori produk',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'jumlah_kategori' => $kategori->count(),
                'waktu' => now(),
            ]
        ]);

        return view('kategori.index', compact('kategori'));
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Log::info('Menyimpan kategori baru.', ['user_id' => $user->id, 'nama_kategori' => $request->nama_kategori]);

        // Simpan ke database
        $kategori = KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        Log::info('Kategori berhasil disimpan.', ['id' => $kategori->id, 'nama_kategori' => $request->nama_kategori]);

        // Catat aktivitas penyimpanan
        ActivityLog::create([
            'action' => 'Tambah Kategori',
            'description' => 'User menambahkan kategori baru',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'kategori_id' => $kategori->id,
                'nama_kategori' => $kategori->nama_kategori,
                'waktu' => now(),
            ]
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Memperbarui data kategori berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
    
        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);
    
        $kategori = KategoriProduk::findOrFail($id);
    
        // Cek apakah ada kategori dengan nama yang sama selain kategori yang sedang diupdate, baik yang masih aktif maupun yang sudah di-soft delete
        $kategoriExist = KategoriProduk::where('nama_kategori', $request->nama_kategori)
                                        ->where('id', '!=', $id)  // Pastikan kategori yang sedang diupdate tidak terhitung
                                        ->exists();
    
        if ($kategoriExist) {
            return redirect()->route('kategori.index')->with('error', 'Nama kategori sudah ada.');
        }
    
        Log::info('Memperbarui kategori.', ['user_id' => $user->id, 'id' => $id, 'nama_lama' => $kategori->nama_kategori, 'nama_baru' => $request->nama_kategori]);
    
        // Update data
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);
    
        Log::info('Kategori berhasil diperbarui.', ['id' => $id, 'nama_kategori' => $request->nama_kategori]);
    
        // Catat aktivitas update
        ActivityLog::create([
            'action' => 'Edit Kategori',
            'description' => 'User memperbarui kategori',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'kategori_id' => $id,
                'nama_lama' => $kategori->nama_kategori,
                'nama_baru' => $request->nama_kategori,
                'waktu' => now(),
            ]
        ]);
    
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }
        

    /**
     * Menghapus kategori berdasarkan ID.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $kategori = KategoriProduk::with('produk')->findOrFail($id);

        // Cek apakah kategori punya produk
        if ($kategori->produk->count() > 0) {
            return redirect()->route('kategori.index')->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        Log::info('Menghapus kategori.', [
            'user_id' => $user->id,
            'id' => $id,
            'nama_kategori' => $kategori->nama_kategori
        ]);

        $kategori->delete(); // Soft delete

        Log::info('Kategori berhasil dihapus.', ['id' => $id]);

        // Simpan aktivitas ke database
        ActivityLog::create([
            'action' => 'Hapus Kategori',
            'description' => 'User menghapus kategori',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'kategori_id' => $id,
                'nama_kategori' => $kategori->nama_kategori,
                'waktu' => now(),
            ]
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Mengimpor data kategori dari file Excel/CSV.
     */
    public function import(Request $request)
    {
        // Validasi file yang diupload
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048', // Hanya menerima file Excel/CSV dan maksimal 2MB
        ]);

        try {
            // Mengimpor file
            Excel::import(new KategoriImport, $request->file('file'));

            return redirect()->route('kategori.index')->with('success', 'Data berhasil diimpor');
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan saat mengimpor
            return redirect()->route('kategori.index')->with('error', 'Terjadi kesalahan saat mengimpor data');
        }
    }
}