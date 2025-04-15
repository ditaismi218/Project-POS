<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori_produk';

    protected $fillable = [
        'nama_kategori'
    ];

    protected $dates = ['deleted_at'];
    // public function produk()
    // {
    //     return $this->hasMany(Produk::class);
    // }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
