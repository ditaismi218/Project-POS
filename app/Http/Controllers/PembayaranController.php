<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Penjualan;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with('penjualan')->get();
        // $penjualan = Penjualan::with(['member', 'voucher'])->get();
        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create(Penjualan $penjualan)
    {
        if (!$penjualan) {
            return redirect()->route('penjualan.index')->with('error', 'Data penjualan tidak ditemukan.');
        }
    
        return view('pembayaran.create', compact('penjualan'));
    }    

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string'
        ]);

        $penjualan = Penjualan::find($request->penjualan_id);
        if (!$penjualan) {
            return redirect()->back()->with('error', 'Penjualan tidak ditemukan.');
        }

        // Hitung total yang sudah dibayar sebelumnya
        $totalDibayar = Pembayaran::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
        $jumlah_bayar = $request->jumlah_bayar;

        // Hitung kembalian dengan benar
        $kembalian = ($jumlah_bayar + $totalDibayar) - $penjualan->total_bayar;

        // Cegah pembayaran lebih kecil dari total bayar
        if ($jumlah_bayar + $totalDibayar < $penjualan->total_bayar) {
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total pembayaran.');
        }

        Pembayaran::create([
            'penjualan_id' => $penjualan->id,
            'jumlah_bayar' => $jumlah_bayar,
            'kembalian' => max($kembalian, 0), // Jika negatif, buat jadi 0
            'metode_pembayaran' => $request->metode_pembayaran
        ]);

        // Update total yang sudah dibayar
        $totalDibayar += $jumlah_bayar;

        // Perbarui status jika sudah lunas
        if ($totalDibayar >= $penjualan->total_bayar) {
            $penjualan->update(['status' => 'lunas']);
        }

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil disimpan.');
    }
    

    /**
     * Menghapus pembayaran.
     */
    // public function destroy($id)
    // {
    //     $pembayaran = Pembayaran::findOrFail($id);
    //     $pembayaran->delete();

    //     return back()->with('success', 'Pembayaran berhasil dihapus.');
    // }
}
