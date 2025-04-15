<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

// @class ActivityLogController
// @desc Mengatur tampilan log aktivitas pengguna.
//       Menyediakan fungsi untuk melihat daftar aktivitas yang terjadi dalam sistem.
class ActivityLogController extends Controller
{
    // @function index
    // @desc Menampilkan daftar log aktivitas terbaru.
    // @return View yang berisi daftar log yang diurutkan dari yang terbaru.
    public function index()
    {
        $logs = ActivityLog::latest()->paginate(10);
        return view('logs.index', compact('logs'));
    }
}
