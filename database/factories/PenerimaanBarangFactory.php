<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenerimaanBarangFactory extends Factory
{
    public function definition(): array
    {
        $hargaSatuan = fake()->numberBetween(5000, 15000);
        $qty = fake()->numberBetween(1, 50);
        $hargaJual = $hargaSatuan + fake()->numberBetween(1000, 3000);
        $expired = fake()->optional()->dateTimeBetween('+1 month', '+2 years');

        return [
            'user_id' => User::factory(),
            'supplier_id' => Supplier::factory(),
            'produk_id' => Produk::inRandomOrder()->value('id') ?? Produk::factory(),
            'kode_penerimaan' => 'PNR-' . strtoupper(fake()->unique()->bothify('###??')),
            'tgl_masuk' => now(),
            'harga_satuan' => $hargaSatuan,
            'harga_jual' => $hargaJual,
            'qty' => $qty,
            'harga_total' => $qty * $hargaSatuan,
            'expired_date' => $expired ? $expired->format('Y-m-d') : null,
        ];
    }
}
