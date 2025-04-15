<?php

namespace Database\Factories;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\PenerimaanBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPenjualanFactory extends Factory
{
    protected $model = DetailPenjualan::class;

    public function definition()
    {
        $penerimaan = PenerimaanBarang::factory()->create();
        $produkId = $penerimaan->produk_id;
    
        if (!$produkId) {
            $produk = Produk::inRandomOrder()->value('id') ?? Produk::factory()->create();
            $penerimaan->update(['produk_id' => $produk->id]);
            $produkId = $produk->id;
        }
    
        $qty = $this->faker->numberBetween(1, 10);
    
        return [
            'penjualan_id' => Penjualan::factory()->create()->id, // FIXED
            'penerimaan_barang_id' => $penerimaan->id,
            'produk_id' => $produkId,
            'qty' => $qty,
            'harga_jual' => $penerimaan->harga_jual,
            'sub_total' => $qty * $penerimaan->harga_jual,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
    

    public function untukPenjualan(Penjualan $penjualan)
    {
        return $this->state(function (array $attributes) use ($penjualan) {
            return [
                'penjualan_id' => $penjualan->id,
            ];
        });
    }

    public function denganProduk(Produk $produk)
    {
        $penerimaan = PenerimaanBarang::factory()->create(['produk_id' => $produk->id]);
        
        return $this->state(function (array $attributes) use ($penerimaan) {
            return [
                'penerimaan_barang_id' => $penerimaan->id,
                'produk_id' => $penerimaan->produk_id,
                'harga_jual' => $penerimaan->harga_jual,
                'sub_total' => $attributes['qty'] * $penerimaan->harga_jual,
            ];
        });
    }

    public function denganQty(int $qty)
    {
        return $this->state(function (array $attributes) use ($qty) {
            return [
                'qty' => $qty,
                'sub_total' => $qty * $attributes['harga_jual'],
            ];
        });
    }
}