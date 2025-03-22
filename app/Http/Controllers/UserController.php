<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Import Log facade

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        Log::info('Menampilkan daftar user', ['total_users' => $users->count()]);
        ActivityLog::create([
            'action' => 'view',
            'description' => 'Melihat daftar user',
            'data' => json_encode(['user_id' => auth()->id(), 'total_users' => $users->count()])
        ]);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        Log::info('Menampilkan form pembuatan user baru');
        ActivityLog::create([
            'action' => 'view_create_form',
            'description' => 'Melihat form tambah user',
            'data' => json_encode(['user_id' => auth()->id()])
        ]);

        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        Log::info('Menerima data untuk membuat user baru', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        Log::info('User baru berhasil ditambahkan', ['user_id' => $user->id, 'name' => $user->name]);

        // Catat aktivitas
        ActivityLog::create([
            'action' => 'create',
            'description' => 'Menambahkan user baru',
            'data' => json_encode(['admin_id' => auth()->id(), 'user_id' => $user->id, 'name' => $user->name])
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        Log::info('Menampilkan form untuk mengedit user', ['user_id' => $user->id, 'name' => $user->name]);
        ActivityLog::create([
            'action' => 'view_edit_form',
            'description' => 'Melihat form edit user',
            'data' => json_encode(['admin_id' => auth()->id(), 'user_id' => $user->id])
        ]);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldData = $user->toArray();

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required'
        ]);

        $user = User::findOrFail($id);

        // Log data yang akan diperbarui
        Log::info('Menerima data untuk memperbarui user', [
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        // Update data user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            Log::info('Password user telah diperbarui', ['user_id' => $user->id]);
        }

        $user->save();

        Log::info('User berhasil diperbarui', ['user_id' => $user->id]);
        ActivityLog::create([
            'action' => 'update',
            'description' => 'Memperbarui user',
            'data' => json_encode([
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'before' => $oldData,
                'after' => $user->toArray()
            ])
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Log penghapusan user
        Log::info('Menghapus user', ['user_id' => $user->id, 'name' => $user->name]);
        ActivityLog::create([
            'action' => 'delete',
            'description' => 'Menghapus user',
            'data' => json_encode(['admin_id' => auth()->id(), 'user_id' => $user->id, 'name' => $user->name])
        ]);
        $user->delete();

        Log::info('User berhasil dihapus', ['user_id' => $user->id, 'name' => $user->name]);

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
