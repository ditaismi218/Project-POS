<?php

namespace Database\Factories;

use DB;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $data = DB::table('kategori_produk')
            ->inRandomOrder()
            ->select('id')
            ->first();

        $satuanList = ['pcs', 'pack', 'box', 'lusin', 'gram', 'kg', 'ml', 'liter', 'meter', 'botol', 'kaleng', 'sachet', 'strip'];

        return [
            'kode_barang' => 'K' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'nama_barang' => fake()->randomElement([
                'Beras',
                'Minyak Goreng',
                'Gula Pasir',
                'Garam',
                'Mie Instan',
                'Susu Kental Manis',
                'Kopi Bubuk',
                'Teh Celup',
                'Saus Tomat',
                'Kecap Manis',
                'Deterjen',
                'Sabun Mandi',
                'Shampoo',
                'Pasta Gigi'
            ]),
            'kategori_id' => $data->id,
            'satuan' => fake()->randomElement($satuanList),
        ];

    }
}
