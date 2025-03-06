<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $supplier = Supplier::all();
        return view('supplier.index', compact('supplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string',
           'telepon' => 'required|numeric|digits_between:10,12',
            'email' =>'required',
            'alamat' =>'required',
        ]);

        Supplier::create($validated);
        return redirect()->route('supplier.index')->with('success', 'supplier berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string',
            'telepon' => 'required|numeric|digits_between:10,12',
            'email' =>'required',
            'alamat' =>'required',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'alamat' => $request->alamat
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }

}
