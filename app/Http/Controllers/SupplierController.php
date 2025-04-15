<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Import Log facade

class SupplierController extends Controller
{
    /**
     * Menampilkan halaman daftar semua supplier.
     */
    public function index()
    {
        // Ambil semua data supplier yang diurutkan berdasarkan waktu pembuatan terbaru
        $supplier = Supplier::withCount('penerimaanBarang')
            ->orderBy('created_at', 'desc')
            ->get();


        Log::info('Menampilkan daftar supplier', ['total_supplier' => $supplier->count()]);
        ActivityLog::create([
            'action' => 'view',
            'description' => 'Melihat daftar supplier',
            'data' => json_encode(['user_id' => auth()->id(), 'total_supplier' => $supplier->count()])
        ]);

        // Kembalikan ke view dengan data supplier
        return view('supplier.index', compact('supplier'));
    }

    /**
     * Menyimpan data supplier baru ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'nama_supplier' => 'required|string',
            'telepon' => 'required|numeric|digits_between:10,12',
            'email' => 'required',
            'alamat' => 'required',
        ]);

        // Log validasi input yang diterima
        Log::info('Menerima data supplier baru untuk disimpan', ['data' => $validated]);

        // Simpan data supplier ke database
        $supplier = Supplier::create($validated);

        Log::info('Supplier baru berhasil ditambahkan', ['data' => $validated]);
        ActivityLog::create([
            'action' => 'create',
            'description' => 'Menambahkan supplier baru',
            'data' => json_encode(['supplier_id' => $supplier->id, 'nama_supplier' => $supplier->nama_supplier, 'user_id' => auth()->id()])
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * Memperbarui data supplier yang ada berdasarkan ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Ambil data supplier lama
        $supplier = Supplier::findOrFail($id);
        $oldData = $supplier->toArray();

        // Validasi data input yang baru
        $request->validate([
            'nama_supplier' => 'required|string',
            'telepon' => 'required|numeric|digits_between:10,12',
            'email' => 'required',
            'alamat' => 'required',
        ]);

        // Log validasi input yang diterima
        Log::info('Menerima data pembaruan supplier', ['supplier_id' => $id, 'data' => $request->all()]);

        $supplier = Supplier::findOrFail($id);

        // Update data supplier di database
        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'alamat' => $request->alamat
        ]);

        Log::info('Supplier berhasil diperbarui', ['supplier_id' => $id, 'updated_data' => $request->all()]);
        ActivityLog::create([
            'action' => 'update',
            'description' => 'Memperbarui supplier',
            'data' => json_encode([
                'user_id' => auth()->id(),
                'supplier_id' => $supplier->id,
                'before' => $oldData,
                'after' => $supplier->toArray()
            ])
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Menghapus supplier dari database.
     * Jika supplier sudah mengirim barang, maka tidak bisa dihapus.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Cari supplier berdasarkan ID
        $supplier = Supplier::findOrFail($id);

        // Periksa apakah supplier sudah pernah mengirim barang (relasi ke penerimaanBarang)
        if ($supplier->penerimaanBarang()->exists()) {
            // Jika iya, tampilkan error dan catat ke log dan ActivityLog
            Log::warning('Gagal menghapus supplier karena sudah pernah mengirim barang', [
                'supplier_id' => $id,
                'nama_supplier' => $supplier->nama_supplier,
            ]);

            ActivityLog::create([
                'action' => 'delete_failed',
                'description' => 'Gagal menghapus supplier karena sudah ada data pengiriman',
                'data' => json_encode([
                    'supplier_id' => $id,
                    'user_id' => auth()->id()
                ])
            ]);

            return redirect()->route('supplier.index')->with('error', 'Supplier tidak dapat dihapus karena sudah pernah mengirim barang.');
        }

        // Jika belum pernah mengirim, lanjutkan penghapusan (soft delete)
        $supplier->delete();

        // Catat log penghapusan
        Log::info('Supplier berhasil dihapus (soft delete)', [
            'supplier_id' => $id,
            'nama_supplier' => $supplier->nama_supplier
        ]);

        // Simpan ke dalam ActivityLog
        ActivityLog::create([
            'action' => 'delete',
            'description' => 'Menghapus supplier',
            'data' => json_encode([
                'supplier_id' => $supplier->id,
                'nama_supplier' => $supplier->nama_supplier,
                'user_id' => auth()->id()
            ])
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }

}
