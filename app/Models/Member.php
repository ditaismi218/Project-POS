<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_member',
        'nama',
        'no_telp',
        'alamat',
        'loyalty_points',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            // Format kode member: "MBRYYYYMMDDHHMMSSXXX" (XXX = 3 huruf acak)
            $randomStr = strtoupper(Str::random(3));
            $member->kode_member = 'MBR' . now()->format('YmdHis') . $randomStr;

            // Set tanggal bergabung otomatis saat pertama kali dibuat
            $member->tgl_bergabung = Carbon::now();
        });
    }

    // Mencegah tgl_bergabung berubah saat update
    public function setTglBergabungAttribute($value)
    {
        if (!$this->exists) {
            $this->attributes['tgl_bergabung'] = $value;
        }
    }
}
