<?php

namespace App\Http\Controllers;

use App\Models\VoucherDiskon;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $voucher = VoucherDiskon::all();
        return view('voucher.index', compact('voucher'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_voucher' => 'required|unique:voucher',
            'jenis' => 'required',
            'nilai' => 'required',
            'min_belanja' => 'required',
            'berlaku_hingga' => 'required|date',
        ]);

        VoucherDiskon::create($request->all());
        return redirect()->route('voucher.index')->with('success', 'Voucher berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_voucher' => 'required|',
            'jenis' => 'required',
            'nilai' => 'required',
            'min_belanja' => 'required',
            'berlaku_hingga' => 'required|date',
        ]);

        $voucher = VoucherDiskon::findOrFail($id);

        $voucher->update($request->all());

        return redirect()->route('voucher.index')->with('success', 'Voucher berhasil diperbarui');
    }

    public function destroy($id)
    {
        $voucher = VoucherDiskon::findOrFail($id);
        $voucher->delete();

        return redirect()->route('voucher.index')->with('success', 'Voucher berhasil dihapus.');
    }
}
