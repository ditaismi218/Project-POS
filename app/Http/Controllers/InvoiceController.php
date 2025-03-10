<?php
namespace App\Http\Controllers;

use App\Models\Penjualan;
use PDF; // Pastikan sudah install dompdf
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function cetak($id)
    {
        $penjualan = Penjualan::with('detailPenjualan.produk')->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\PDF::loadView('invoice.cetak', compact('penjualan'));
        return $pdf->stream("invoice_{$penjualan->no_faktur}.pdf");
    }
}
