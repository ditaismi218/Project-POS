<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index()
    {
        Log::info('Menampilkan semua data member.');
        
        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Akses Data Member',
            'description' => 'User mengakses daftar member.',
            'data' => ['total_member' => Member::count()]
        ]);

        $member = Member::all();
        return view('member.index', compact('member'));
    }

    public function store(Request $request)
    {
        Log::info('Menerima permintaan untuk menambahkan member baru.', ['data' => $request->all()]);

        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|numeric|digits_between:10,12',
            'alamat' => 'required',
            'loyalty_points' => 'nullable|integer',
        ]);

        $member = Member::create($request->only(['nama', 'no_telp', 'alamat', 'loyalty_points']));

        Log::info('Member baru berhasil ditambahkan.', ['nama' => $request->nama]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Tambah Member',
            'description' => "Member baru ditambahkan: {$request->nama}",
            'data' => ['id' => $member->id, 'nama' => $request->nama]
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        Log::info('Menerima permintaan untuk memperbarui member.', ['id' => $id, 'data' => $request->all()]);

        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|numeric|digits_between:10,12',
            'alamat' => 'required',
            'loyalty_points' => 'nullable|integer',
        ]);

        $member = Member::findOrFail($id);
        $member->update([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'loyalty_points' => $request->loyalty_points ?? 0,
        ]);

        Log::info('Member berhasil diperbarui.', ['id' => $id, 'nama' => $request->nama]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Update Member',
            'description' => "Member diperbarui: {$request->nama}",
            'data' => ['id' => $id, 'nama' => $request->nama]
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil diperbarui');
    }

    public function destroy($id)
    {
        Log::info('Menerima permintaan untuk menghapus member.', ['id' => $id]);

        $member = Member::findOrFail($id);
        $member->delete();

        Log::info('Member berhasil dihapus.', ['id' => $id, 'nama' => $member->nama]);

        // Simpan log ke database
        ActivityLog::create([
            'action' => 'Hapus Member',
            'description' => "Member dihapus: {$member->nama}",
            'data' => ['id' => $id, 'nama' => $member->nama]
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil dihapus.');
    }
}
