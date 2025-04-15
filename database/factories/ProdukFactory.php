<?php

namespace Database\Factories;

use App\Models\KategoriProduk;
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

        if (!$data) {
            $data = KategoriProduk::factory()->create(); // make sure factory ini ada
        }

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
            // 'gambar' => 'produk/' . fake()->image('storage/app/public/produk', 640, 480, null, false),
            'gambar' => fake()->randomElement([
                'produk/chitato.jpg',
                'produk/lays.png',
            ]),

            'satuan' => fake()->randomElement($satuanList),
        ];

    }
}
