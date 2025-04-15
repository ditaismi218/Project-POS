<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembayarans = \App\Models\Pembayaran::factory()->count(20)->create();
    
        // Cek dan update status penjualan jika sudah lunas
        foreach ($pembayarans as $pembayaran) {
            $penjualan = $pembayaran->penjualan;
            $total = $penjualan->detailPenjualan->sum('sub_total');
    
            if ($pembayaran->jumlah_bayar >= $total) {
                $penjualan->update(['status' => 'lunas']);
            }
        }
    }
}
