<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenerimaanBarang extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'penerimaan_barang';
    protected $fillable = [
        'user_id',
        'supplier_id',
        'produk_id',
        'kode_penerimaan',
        'tgl_masuk',
        'harga_jual',
        'harga_satuan',
        'qty',
        'harga_total',
        'expired_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Model PenerimaanBarang
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id'); // Sesuaikan foreign key-nya
    }

}
