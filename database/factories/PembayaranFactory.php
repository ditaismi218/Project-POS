<?php

namespace Database\Factories;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    public function definition()
    {
        $penjualan = Penjualan::where('status', 'pending')
            ->has('detailPenjualan')
            ->inRandomOrder()
            ->first() ?? Penjualan::factory()
                ->pending()
                ->has(DetailPenjualan::factory()->count(rand(1, 5)), 'detailPenjualan')
                ->create();

        $totalPenjualan = $penjualan->detailPenjualan->sum('sub_total');

        // Jumlah bayar secara acak (bisa kurang, pas, atau lebih)
        $jumlahBayar = $this->faker->numberBetween(0, $totalPenjualan * 1.5);
        $kembalian = max(0, $jumlahBayar - $totalPenjualan);

        // ❗️Tidak langsung ubah status di sini

        return [
            'penjualan_id' => $penjualan->id,
            'jumlah_bayar' => $jumlahBayar,
            'kembalian' => $kembalian,
            'metode_pembayaran' => $this->faker->randomElement(['cash', 'debit', 'kredit', 'ewallet']),
            'created_at' => $penjualan->created_at,
            'updated_at' => now(),
        ];
    }


    public function untukPenjualan(Penjualan $penjualan)
    {
        // Pastikan penjualan memiliki detail
        if ($penjualan->detailPenjualan->isEmpty()) {
            DetailPenjualan::factory()->count(rand(1, 5))->create(['penjualan_id' => $penjualan->id]);
        }

        // Hitung total dari detail penjualan
        $totalPenjualan = $penjualan->detailPenjualan->sum('sub_total');

        // Generate jumlah bayar
        $jumlahBayar = $this->faker->numberBetween(0, $totalPenjualan * 1.5);

        // Hitung kembalian
        $kembalian = max(0, $jumlahBayar - $totalPenjualan);

        return [
            'penjualan_id' => $penjualan->id,
            'jumlah_bayar' => $jumlahBayar,
            'kembalian' => $kembalian,
            'created_at' => $penjualan->created_at,
        ];
    }

    public function lunas()
    {
        return $this->state(function (array $attributes) {
            // Dapatkan penjualan terkait
            $penjualan = Penjualan::find($attributes['penjualan_id']);

            // Hitung total penjualan
            $totalPenjualan = $penjualan->detailPenjualan->sum('sub_total');

            // Update status penjualan menjadi lunas
            $penjualan->update(['status' => 'lunas']);

            return [
                'jumlah_bayar' => $totalPenjualan,
                'kembalian' => 0,
            ];
        });
    }

    public function denganKembalian()
    {
        return $this->state(function (array $attributes) {
            // Dapatkan penjualan terkait
            $penjualan = Penjualan::find($attributes['penjualan_id']);

            // Hitung total penjualan
            $totalPenjualan = $penjualan->detailPenjualan->sum('sub_total');

            // Generate jumlah bayar lebih besar dari total
            $jumlahBayar = $totalPenjualan + $this->faker->numberBetween(1000, 50000);

            // Update status penjualan menjadi lunas
            $penjualan->update(['status' => 'lunas']);

            return [
                'jumlah_bayar' => $jumlahBayar,
                'kembalian' => $jumlahBayar - $totalPenjualan,
            ];
        });
    }
}