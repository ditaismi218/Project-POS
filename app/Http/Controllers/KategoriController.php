<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\ActivityLog; // Import Model ActivityLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        Log::info('Memuat halaman daftar kategori.', ['user_id' => $user->id, 'email' => $user->email]);

        $kategori = KategoriProduk::orderBy('created_at', 'desc')->get();

        Log::info('Data kategori berhasil diambil.', ['jumlah_kategori' => $kategori->count()]);

        // Simpan aktivitas ke database
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

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Log::info('Menyimpan kategori baru.', ['user_id' => $user->id, 'nama_kategori' => $request->nama_kategori]);

        $kategori = KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        Log::info('Kategori berhasil disimpan.', ['id' => $kategori->id, 'nama_kategori' => $request->nama_kategori]);

        // Simpan aktivitas ke database
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

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = KategoriProduk::findOrFail($id);

        Log::info('Memperbarui kategori.', ['user_id' => $user->id, 'id' => $id, 'nama_lama' => $kategori->nama_kategori, 'nama_baru' => $request->nama_kategori]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        Log::info('Kategori berhasil diperbarui.', ['id' => $id, 'nama_kategori' => $request->nama_kategori]);

        // Simpan aktivitas ke database
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

    public function destroy($id)
    {
        $user = Auth::user();
        $kategori = KategoriProduk::findOrFail($id);

        Log::info('Menghapus kategori.', ['user_id' => $user->id, 'id' => $id, 'nama_kategori' => $kategori->nama_kategori]);

        $kategori->delete();

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
}
