<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'gambar',
        'deskripsi',
        'satuan',
        'gambar',
    ];



    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function penerimaanBarang()
{
    return $this->hasMany(PenerimaanBarang::class, 'produk_id');
}

}
