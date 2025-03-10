<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $fillable = [
        'no_faktur',
        'tgl_faktur',   
        'user_id',
        'member_id',
        'total_bayar',
        'status',
    ];

    public static function generateNoFaktur()
    {
        $date = date('Ymd');
        $lastOrder = self::whereDate('created_at', now()->toDateString())->latest()->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->no_faktur, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'INV-' . $date . '-' . $newNumber;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($penjualan) {
            $penjualan->no_faktur = self::generateNoFaktur();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
    
}
