<?php

namespace App\Http\Controllers;

use App\Exports\PengajuanBarangExport;
use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\PenerimaanBarang;
use App\Models\PengajuanBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PengajuanBarangController extends Controller
{
   /**
     * Menampilkan halaman utama pengajuan barang.
     * Fitur:
     * - Filter berdasarkan tanggal
     * - Export data ke PDF dan Excel
     */
    public function index(Request $request)
    {
        Log::info('Menerima permintaan daftar pengajuan barang', [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        $namaPengajuEnum = Member::pluck('nama');
        $query = PengajuanBarang::query();

         // Filter berdasarkan tanggal pengajuan
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            try {
                $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
                $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();

                if ($tanggalMulai > $tanggalSelesai) {
                    return back()->withErrors(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.']);
                }

                $query->whereBetween('tanggal_pengajuan', [$tanggalMulai, $tanggalSelesai]);
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Format tanggal tidak valid.']);
            }
        }

        $pengajuanBarang = $query->get();

        // Export ke PDF
        if ($request->has('export_pdf')) {
            $pdf = Pdf::loadView('pengajuan_barang.pdf', [
                'pengajuans' => $pengajuanBarang,
                'members' => $namaPengajuEnum
            ]);
            return $pdf->download('pengajuan_barang.pdf');
        }

        // Export ke Excel 
        if ($request->has('export_excel')) {
            return Excel::download(new PengajuanBarangExport($pengajuanBarang), 'pengajuan_barang.xlsx');
        }

        return view('pengajuan_barang.index', compact('pengajuanBarang', 'namaPengajuEnum'));
    }

    /**
     * Menyimpan pengajuan barang baru dari modal.
     */
    public function store(Request $request)
    {
        // Log permintaan penyimpanan pengajuan barang
        Log::info('Menerima permintaan untuk menyimpan pengajuan barang', [
            'nama_pengaju' => $request->nama_pengaju,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty
        ]);

        // Validasi input
        $request->validate([
            'nama_pengaju' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        // Cek apakah nama barang sudah ada di penerimaan_barang
        $barangSudahDiterima = DB::table('penerimaan_barang')
            ->join('produk', 'penerimaan_barang.produk_id', '=', 'produk.id')
            ->pluck('produk.nama_barang')
            ->toArray();

        // Log pengecekan apakah barang sudah diterima
        Log::info('Memeriksa apakah nama barang sudah ada di penerimaan barang', [
            'nama_barang' => $request->nama_barang,
            'barang_terpenuhi' => in_array($request->nama_barang, $barangSudahDiterima) ? 'Ya' : 'Tidak'
        ]);

        // Jika nama barang sudah ada di penerimaan_barang, batalkan pengajuan
        if (in_array($request->nama_barang, $barangSudahDiterima)) {
            Log::warning('Pengajuan barang dibatalkan karena barang sudah diterima', [
                'nama_barang' => $request->nama_barang
            ]);
            return redirect()->route('pengajuan_barang.index')
                ->with('error', 'Nama barang sudah ada di penerimaan barang, tidak bisa diajukan lagi.');
        }

        // Simpan data pengajuan barang
        $pengajuanBarang = PengajuanBarang::create([
            'nama_pengaju' => $request->nama_pengaju,
            'nama_barang' => $request->nama_barang,
            'tanggal_pengajuan' => now(),
            'qty' => $request->qty,
            'terpenuhi' => 0, // Default belum terpenuhi
        ]);

        // Log data pengajuan barang berhasil disimpan
        Log::info('Pengajuan barang berhasil disimpan', [
            'id' => $pengajuanBarang->id,
            'nama_pengaju' => $pengajuanBarang->nama_pengaju,
            'nama_barang' => $pengajuanBarang->nama_barang
        ]);

        // Update status 'terpenuhi' menjadi 1 jika nama barang sudah ada di penerimaan_barang
        PengajuanBarang::whereIn('nama_barang', $barangSudahDiterima)
            ->update(['terpenuhi' => 1]);

        ActivityLog::create([
            'action' => 'create',
            'description' => 'Pengajuan barang ditambahkan',
            'data' => json_encode($pengajuanBarang)
        ]);

        return redirect()->route('pengajuan_barang.index')
            ->with('success', 'Pengajuan barang berhasil ditambahkan!');
    }

     /**
     * Memperbarui data pengajuan barang berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        Log::info('Menerima permintaan untuk memperbarui pengajuan barang', [
            'id' => $id,
            'nama_pengaju' => $request->nama_pengaju,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty
        ]);
    
        // Validasi data input untuk memastikan data yang dimasukkan sesuai aturan
        $request->validate([
            'nama_pengaju' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);
    
        // Cari data pengajuan barang berdasarkan ID, jika tidak ditemukan akan menampilkan error
        $pengajuanBarang = PengajuanBarang::findOrFail($id);
    
        // Simpan data lama sebelum diperbarui untuk kebutuhan log perubahan
        $oldData = $pengajuanBarang->toArray();
    
        // Perbarui data pengajuan barang dengan data baru dari input
        $pengajuanBarang->update([
            'nama_pengaju' => $request->nama_pengaju,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
        ]);
    
         // Simpan log aktivitas perubahan
        ActivityLog::create([
            'action' => 'update',
            'description' => 'Pengajuan barang diperbarui',
            'data' => json_encode([
                'before' => $oldData,
                'after' => $pengajuanBarang->toArray()
            ])
        ]);
    
        Log::info('Pengajuan barang berhasil diperbarui', [
            'id' => $pengajuanBarang->id
        ]);
    
        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil diperbarui!');
    }    

      /**
     * Menghapus data pengajuan barang berdasarkan ID.
     */
    public function destroy($id)
    {
        Log::info('Menerima permintaan untuk menghapus pengajuan barang', ['id' => $id]);

        $pengajuanBarang = PengajuanBarang::findOrFail($id);
        $pengajuanBarang->delete();

        Log::info('Pengajuan barang berhasil dihapus', ['id' => $id]);

        ActivityLog::create([
            'action' => 'delete',
            'description' => 'Pengajuan barang berhasil dihapus',
            'data' => ['id' => $id]
        ]);
        
        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil dihapus!');
    }

    /**
     * Memperbarui status "terpenuhi" dari pengajuan barang.
     * Endpoint ini umumnya dipanggil via AJAX.
     */
    public function updateTerpenuhi(Request $request, $id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->terpenuhi = $request->terpenuhi;
        $pengajuan->save();

        return response()->json(['message' => 'Status terpenuhi berhasil diperbarui!'], 200);
    }

}
