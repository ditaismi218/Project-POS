<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Import Log
use App\Models\ActivityLog; // Import Model ActivityLog


// @class AuthController
// @desc Mengelola proses otentikasi pengguna, termasuk login dan logout.
class AuthController extends Controller
{

    // @function login
    // @desc Memproses permintaan login dari pengguna.
    //       Melakukan validasi, mencatat aktivitas login, dan menangani kegagalan login.
    // @param Request $request - Objek request dari form login.
    // @return Redirect ke dashboard jika berhasil, atau kembali ke halaman login dengan error.
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        // Coba login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate(); // Regenerate session untuk keamanan

            // Logging sukses login ke Laravel log
            Log::info('Login berhasil', [
                'email' => $request->email,
                'waktu' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Logging ke database ActivityLog
            ActivityLog::create([
                'action' => 'Login',
                'description' => 'User berhasil login',
                'data' => [
                    'user_id' => Auth::id(),
                    'email' => $request->email,
                    'ip_address' => $request->ip(),
                    'waktu' => now(),
                ]
            ]);

            return redirect()->route('dashboard'); // Redirect jika sukses
        }

        // Logging gagal login ke Laravel log
        Log::warning('Login gagal', [
            'email' => $request->email,
            'waktu' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Logging ke database ActivityLog untuk login gagal
        ActivityLog::create([
            'action' => 'Login Gagal',
            'description' => 'Percobaan login gagal',
            'data' => [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'waktu' => now(),
            ]
        ]);

        // Jika gagal, kembali ke login dengan pesan error
        return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
    }

    // @function logout
    // @desc Memproses logout pengguna dari sistem.
    //       Mencatat aktivitas logout dan menghancurkan session.
    // @param Request $request - Objek request saat logout.
    // @return Redirect ke halaman login setelah logout berhasil.
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Logging aktivitas logout ke Laravel log
        Log::info('Logout berhasil', [
            'user_id' => $user ? $user->id : null,
            'email' => $user ? $user->email : 'Tidak Diketahui',
            'waktu' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Logging ke database ActivityLog
        ActivityLog::create([
            'action' => 'Logout',
            'description' => 'User berhasil logout',
            'data' => [
                'user_id' => $user ? $user->id : null,
                'email' => $user ? $user->email : 'Tidak Diketahui',
                'ip_address' => $request->ip(),
                'waktu' => now(),
            ]
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}
