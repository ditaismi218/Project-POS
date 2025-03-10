<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // $this->call(KategoriProdukSeeder::class);

        // Produk::factory()->count(22)->create();
        Supplier::factory()->count(10)->create();
        
        // User::factory()->create([
        //     'name' => 'admin',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('admin123'),
        //     'role' => 'admin',
        // ]);
    }
}