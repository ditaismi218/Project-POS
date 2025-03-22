<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Produk::truncate();
        Schema::enableForeignKeyConstraints();
        $file = File::get('database/data/Produk.json');
        $data = json_decode($file);
        foreach ($data as $item) {
            Produk::create([
                // 'id' => $item->id,
                'kode' => $item->kode,
                'nama_barang' => $item->nama_barang,
                'kategori_id' => $item->kategori_id,
            ]);
        }
    }
}
