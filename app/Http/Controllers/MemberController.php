<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $member = Member::all();
        return view('member.index', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|numeric|digits_between:10,12',
            'alamat' => 'required',
            'loyalty_points' => 'nullable|integer',
        ]);

        Member::create($request->only(['nama', 'no_telp', 'alamat', 'loyalty_points']));

        return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
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

        return redirect()->route('member.index')->with('success', 'Member berhasil diperbarui');
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('member.index')->with('success', 'Member berhasil dihapus.');
    }
}
