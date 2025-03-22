<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        KategoriProduk::truncate();
        Schema::enableForeignKeyConstraints();
        $file = File::get('database/data/KategoriProduk.json');
        $data = json_decode($file);
        foreach ($data as $item) {
            KategoriProduk::create([
                // 'id' => $item->id,
                'nama_kategori' => $item->nama_kategori,
            ]);
        }
    }
}


