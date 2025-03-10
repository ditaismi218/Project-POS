<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Supplier::truncate();
        Schema::enableForeignKeyConstraints();
        $file = File::get('database/data/supplier.json');
        $data = json_decode($file);
        foreach ($data as $item) {
            Supplier::create([
                'id' => $item->id,
            ]);
        }
        
    }
}
