<?php

namespace Database\Seeders;

use App\Models\PenerimaanBarang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PenerimaanBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel dulu (opsional)
        Schema::disableForeignKeyConstraints();
        PenerimaanBarang::truncate();
        Schema::enableForeignKeyConstraints();

        // Generate 20 data dummy
        PenerimaanBarang::factory()->count(20)->create();
    }
}
