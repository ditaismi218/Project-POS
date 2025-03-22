<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Import Log facade

class SupplierController extends Controller
{
    public function index()
    {
        $supplier = Supplier::orderBy('created_at', 'desc')->get();
        
        Log::info('Menampilkan daftar supplier', ['total_supplier' => $supplier->count()]);
        ActivityLog::create([
            'action' => 'view',
            'description' => 'Melihat daftar supplier',
            'data' => json_encode(['user_id' => auth()->id(), 'total_supplier' => $supplier->count()])
        ]);

        return view('supplier.index', compact('supplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string',
            'telepon' => 'required|numeric|digits_between:10,12',
            'email' => 'required',
            'alamat' => 'required',
        ]);

        // Log validasi input yang diterima
        Log::info('Menerima data supplier baru untuk disimpan', ['data' => $validated]);

        $supplier = Supplier::create($validated);


        Log::info('Supplier baru berhasil ditambahkan', ['data' => $validated]);
        ActivityLog::create([
            'action' => 'create',
            'description' => 'Menambahkan supplier baru',
            'data' => json_encode(['supplier_id' => $supplier->id, 'nama_supplier' => $supplier->nama_supplier, 'user_id' => auth()->id()])
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $oldData = $supplier->toArray();

        $request->validate([
            'nama_supplier' => 'required|string',
            'telepon' => 'required|numeric|digits_between:10,12',
            'email' => 'required',
            'alamat' => 'required',
        ]);

        // Log validasi input yang diterima
        Log::info('Menerima data pembaruan supplier', ['supplier_id' => $id, 'data' => $request->all()]);

        $supplier = Supplier::findOrFail($id);
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

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        // Log penghapusan supplier
        Log::info('Supplier akan dihapus', ['supplier_id' => $id, 'nama_supplier' => $supplier->nama_supplier]);
        ActivityLog::create([
            'action' => 'delete_failed',
            'description' => 'Gagal menghapus supplier - Supplier tidak ditemukan',
            'data' => json_encode(['supplier_id' => $id, 'user_id' => auth()->id()])
        ]);

        $supplier->delete();

        Log::info('Supplier berhasil dihapus', ['supplier_id' => $id, 'nama_supplier' => $supplier->nama_supplier]);
        ActivityLog::create([
            'action' => 'delete',
            'description' => 'Menghapus supplier',
            'data' => json_encode(['supplier_id' => $supplier->id, 'nama_supplier' => $supplier->nama_supplier, 'user_id' => auth()->id()])
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
