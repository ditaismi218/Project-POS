<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;
use App\Imports\AbsensiImport;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * AbsensiController bertanggung jawab untuk mengelola operasi CRUD
 * terkait dengan data absensi, termasuk penyimpanan, penghapusan, pembaruan,
 * serta ekspor dan impor data absensi.
 */
class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar absensi yang sudah tercatat.
     * 
     * Fungsi ini mengambil data absensi yang sudah ada dari database dan
     * menampilkannya di halaman utama. Jika terdapat parameter 'export_pdf',
     * maka akan mengekspor laporan absensi ke dalam format PDF.
     */
    public function index(Request $request)
    {
        $absensi = Absensi::with('user')->orderBy('created_at', 'desc')->get();
        $users = User::all();

        // Mengekspor laporan ke PDF jika parameter export_pdf ada
        if ($request->has('export_pdf')) {
            $pdf = PDF::loadView('absensi.pdf', compact('absensi'));
            return $pdf->download('laporan-absensi.pdf');
        }

        return view('absensi.index', compact('absensi', 'users'));
    }

    /**
     * Menyimpan data absensi baru ke dalam database.
     *
     * Fungsi ini menyimpan data absensi yang diterima melalui form input.
     * Dilakukan validasi data untuk memastikan bahwa status dan waktu masuk
     * yang diberikan valid. Jika sudah ada absensi pada tanggal yang sama,
     * maka akan menampilkan pesan pemberitahuan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'waktu_masuk' => 'required',
            'status_masuk' => 'required|in:masuk,sakit,cuti'
        ]);

        $tanggalMasuk = $request->tanggal_masuk ?? Carbon::now('Asia/Jakarta')->toDateString();

        // Cek apakah sudah absen dengan status yang sama
        $existingAbsensi = Absensi::where('user_id', $request->user_id)
            ->whereDate('tanggal_masuk', $tanggalMasuk)
            ->whereIn('status_masuk', ['masuk', 'sakit', 'cuti']) // Pastikan status ada
            ->first();

        if ($existingAbsensi) {
            return redirect()->back()->with('info', 'Karyawan sudah absen pada tanggal ini, status: ' . ucfirst($existingAbsensi->status_masuk));
        }

        $waktuMasuk = in_array($request->status_masuk, ['sakit', 'cuti']) ? '00:00:00' : $request->waktu_masuk;
        $waktuSelesai = in_array($request->status_masuk, ['sakit', 'cuti']) ? '00:00:00' : null;

        Absensi::create([
            'user_id' => $request->user_id,
            'tanggal_masuk' => $tanggalMasuk,
            'waktu_masuk' => $waktuMasuk,
            'status_masuk' => $request->status_masuk,
            'waktu_selesai_kerja' => $waktuSelesai,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil ditambahkan');
    }

    /**
     * Menghapus data absensi berdasarkan ID.
     * Fungsi ini digunakan untuk menghapus data absensi berdasarkan ID yang diberikan.
     */
    public function destroy($id)
    {
        Absensi::destroy($id);
        return redirect()->back()->with('success', 'Data absensi berhasil dihapus');
    }

    /**
     * Memperbarui status absensi (misal: sakit, cuti, masuk).
     * Fungsi ini menerima request untuk memperbarui status absensi
     * dan menyesuaikan waktu masuk serta waktu selesai kerja sesuai
     * dengan status yang dipilih.
     */
    public function updateStatus(Request $request)
    {
        $absen = Absensi::find($request->id);
        if (!$absen)
            return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $absen->status_masuk = $request->status;

        if (in_array($request->status, ['sakit', 'cuti'])) {
            $absen->waktu_masuk = '00:00:00';
            $absen->waktu_selesai_kerja = '00:00:00';
        } elseif ($request->status === 'masuk') {
            $absen->waktu_masuk = Carbon::now('Asia/Jakarta')->format('H:i:s');
            $absen->waktu_selesai_kerja = null;
        }

        $absen->save();

        return response()->json(['success' => 'Status berhasil diubah']);
    }

    /**
     * Memperbarui waktu selesai kerja untuk absensi yang sudah ada.
     * Fungsi ini digunakan untuk memperbarui waktu selesai kerja berdasarkan
     * ID absensi yang diberikan.
     */
    public function selesaikan(Request $request)
    {
        $absen = Absensi::find($request->id);
        if (!$absen)
            return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $absen->waktu_selesai_kerja = Carbon::now('Asia/Jakarta')->format('H:i:s');
        $absen->save();

        return response()->json(['success' => 'Waktu selesai berhasil diupdate']);
    }

    /**
     * Memperbarui data absensi yang sudah ada.
     * Fungsi ini digunakan untuk memperbarui data absensi yang sudah ada
     * berdasarkan ID yang diberikan. Memperbarui data seperti waktu masuk
     * dan status absensi.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_masuk' => 'required|date',
            'waktu_masuk' => 'required',
            'status_masuk' => 'required|in:masuk,sakit,cuti'
        ]);

        $absen = Absensi::findOrFail($id);

        $waktuMasuk = in_array($request->status_masuk, ['sakit', 'cuti']) ? '00:00:00' : $request->waktu_masuk;
        $waktuSelesai = in_array($request->status_masuk, ['sakit', 'cuti']) ? '00:00:00' : $absen->waktu_selesai_kerja;

        $absen->update([
            'user_id' => $request->user_id,
            'tanggal_masuk' => $request->tanggal_masuk,
            'waktu_masuk' => $waktuMasuk,
            'status_masuk' => $request->status_masuk,
            'waktu_selesai_kerja' => $waktuSelesai,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui');
    }

    /**
     * Mengimpor data absensi dari file Excel.
     * Fungsi ini digunakan untuk mengimpor data absensi dari file Excel
     * yang diupload. Data yang sudah ada tidak akan diimpor ulang.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
            ], [
                'file.mimes' => 'File harus berformat Excel (.xlsx, .xls)',
                'file.max' => 'File tidak boleh lebih besar dari 2MB'
            ]);

            $file = $request->file('file');

            if (!$file->isValid()) {
                throw new \Exception('File upload tidak valid');
            }

            $import = new AbsensiImport();
            Excel::import($import, $file);

            if ($import->importedCount > 0) {
                return redirect()->back()->with('success', 'Data berhasil diimpor!');
            } else {
                return redirect()->back()->with('warning', 'Semua data sudah ada, tidak ada yang diimpor.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
    }


    /**
     * Mengekspor data absensi ke dalam file Excel.
     * Fungsi ini digunakan untuk mengekspor seluruh data absensi
     * ke dalam format file Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new AbsensiExport, 'laporan-absensi.xlsx');
    }
}
