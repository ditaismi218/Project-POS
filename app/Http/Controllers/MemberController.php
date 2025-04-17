<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MemberImport;

class MemberController extends Controller
{
    // Menampilkan daftar semua member
    public function index()
    {
        Log::info('Menampilkan semua data member.');

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Akses Data Member',
            'description' => 'User mengakses daftar member.',
            'data' => ['total_member' => Member::count()]  // Menyimpan jumlah total member
        ]);

        // Mengambil semua data member dan menampilkan di view
        $member = Member::orderBy('id', 'desc')->get();
        return view('member.index', compact('member'));
    }

    // Menyimpan member baru
    public function store(Request $request)
    {
        // dd($request->all());
        Log::info('Menerima permintaan untuk menambahkan member baru.', ['data' => $request->all()]);

        // Validasi data inputan member
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|numeric|digits_between:10,12',
            'alamat' => 'required',
            'tgl_bergabung' => 'required|date',

        ]);

        // Membuat data member baru berdasarkan input
        $member = Member::create($request->only(['nama', 'no_telp', 'alamat', 'tgl_bergabung']));


        Log::info('Member baru berhasil ditambahkan.', ['nama' => $request->nama]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Tambah Member',
            'description' => "Member baru ditambahkan: {$request->nama}",
            'data' => ['id' => $member->id, 'nama' => $request->nama]
        ]);

        // Redirect ke halaman daftar member dengan pesan sukses
        return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan');
    }

    // Memperbarui data member yang sudah ada
    public function update(Request $request, $id)
    {
        Log::info('Menerima permintaan untuk memperbarui member.', ['id' => $id, 'data' => $request->all()]);

        // Validasi data inputan member
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|numeric|digits_between:10,12',
            'alamat' => 'required',
            'tgl_bergabung' => 'required|date',

        ]);

        // Mencari member berdasarkan ID dan memperbarui datanya
        $member = Member::findOrFail($id);
        $member->update([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'tgl_bergabung' => $request->tgl_bergabung,
        ]);

        Log::info('Member berhasil diperbarui.', ['id' => $id, 'nama' => $request->nama]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Update Member',
            'description' => "Member diperbarui: {$request->nama}",
            'data' => ['id' => $id, 'nama' => $request->nama]
        ]);

        // Redirect ke halaman daftar member dengan pesan sukses
        return redirect()->route('member.index')->with('success', 'Member berhasil diperbarui');
    }

    // Menghapus member berdasarkan ID
    public function destroy($id)
    {
        Log::info('Menerima permintaan untuk menghapus member.', ['id' => $id]);

        // Mencari member berdasarkan ID dan menghapusnya
        $member = Member::findOrFail($id);
        $member->delete();

        Log::info('Member berhasil dihapus.', ['id' => $id, 'nama' => $member->nama]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Hapus Member',
            'description' => "Member dihapus: {$member->nama}",
            'data' => ['id' => $id, 'nama' => $member->nama]
        ]);

        // Redirect ke halaman daftar member dengan pesan sukses
        return redirect()->route('member.index')->with('success', 'Member berhasil dihapus.');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
    
        $import = new MemberImport;
        Excel::import($import, $request->file('file'));
    
        $skipped = count($import->skippedRows);
        $imported = $import->importedCount;
    
        if ($imported === 0 && $skipped > 0) {
            return redirect()->route('member.index')->with([
                'info' => "$skipped data dilewati karena sudah pernah diinput.",
            ]);
        }
    
        if ($imported > 0 && $skipped > 0) {
            return redirect()->route('member.index')->with([
                'success' => "$imported data berhasil diimport.",
                'warning' => "$skipped data dilewati karena sudah pernah diinput.",
            ]);
        }
    
        return redirect()->route('member.index')->with('success', 'Semua data member berhasil diimport.');
    }    

}