<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriProduk>
 */
class KategoriProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        fake()->unique(true); // Reset unique constraint setiap kali factory dijalankan
    
        return [
            'nama_kategori' => fake()->randomElement(['Alat Mandi', 'Minyak', 'Bumbu', 'Mie']),
        ];
    }
    
}
