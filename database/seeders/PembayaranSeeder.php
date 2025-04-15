<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use Illuminate\Database\Seeder;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 20 data pembayaran dengan factory
        Pembayaran::factory()->count(20)->create();
    }
}
