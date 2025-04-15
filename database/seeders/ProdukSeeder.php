<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     Schema::disableForeignKeyConstraints();
    //     Produk::truncate();
    //     Schema::enableForeignKeyConstraints();
    //     $file = File::get('database/data/Produk.json');
    //     $data = json_decode($file);
    //     foreach ($data as $item) {
    //         Produk::create([
    //             // 'id' => $item->id,
    //             'kode' => $item->kode,
    //             'nama_barang' => $item->nama_barang,
    //             'kategori_id' => $item->kategori_id,
    //         ]);
    //     }
    // }

    // public function run(): void
    // {
    //     Schema::disableForeignKeyConstraints(); // Nonaktifkan sementara FK constraints
    //     Produk::truncate(); // Hapus data produk lama
    //     Schema::enableForeignKeyConstraints(); // Aktifkan kembali FK constraints

    //     // Menambahkan data produk menggunakan factory
    //     Produk::factory()->count(50)->create(); // Buat 50 produk menggunakan factory
    // }

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Produk::truncate();
        Schema::enableForeignKeyConstraints();

        $file = File::get('database/data/Produk.json');
        $data = json_decode($file);

        foreach ($data as $item) {
            $kategori = KategoriProduk::where('nama_kategori', $item->kategori)->first();

            if (!$kategori) {
                $this->command->warn("Kategori '{$item->kategori}' tidak ditemukan, dilewati.");
                continue;
            }

            // Path gambar yang sesuai untuk public storage
            $gambarPath = 'produk/' . $item->gambar;

            // Pastikan gambar ada di dalam folder storage/app/public/produk/
            if (!Storage::disk('public')->exists($gambarPath)) {
                $this->command->warn("Gambar '{$item->gambar}' tidak ditemukan di folder storage.");
                continue;
            }

            Produk::create([
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'gambar' => $gambarPath, // Pastikan path yang disimpan adalah 'storage/produk/nama-gambar.jpg'
                'kategori_id' => $kategori->id,
                'satuan' => $item->satuan,
            ]);
        }

        $this->command->info("Seeder produk selesai dijalankan.");
    }


}
