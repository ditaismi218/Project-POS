<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'tbl_absen_kerja';

    protected $fillable = [
        'user_id',
        'tanggal_masuk',
        'waktu_masuk',
        'status_masuk',
        'waktu_selesai_kerja',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
