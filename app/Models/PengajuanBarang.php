<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanBarang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_barang';
    protected $fillable = ['nama_pengaju', 'nama_barang', 'tanggal_pengajuan', 'qty', 'terpenuhi'];
}
