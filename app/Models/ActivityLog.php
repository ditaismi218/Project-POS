<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['action', 'description', 'data'];

    protected $casts = [
        'data' => 'array', // Menyimpan JSON sebagai array
    ];
}
