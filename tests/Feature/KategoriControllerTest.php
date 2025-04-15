<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class KategoriControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreSuccesfully(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $data = [
            'nama_kategori' => 'Test Kategori',
        ];

        $response = $this->post('/kategori', $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('kategori_produk', $data);
    }


    public function testUpdateKategori(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $kategori = KategoriProduk::create([
            'nama_kategori' => 'Kategori Lama',
        ]);

        $dataUpdate = [
            'nama_kategori' => 'Kategori Baru',
        ];

        $response = $this->put("/kategori/{$kategori->id}", $dataUpdate);

        $response->assertStatus(302);

        $this->assertDatabaseHas('kategori_produk', [
            'id' => $kategori->id,
            'nama_kategori' => 'Kategori Baru',
        ]);
    }

    public function testDeleteKategoriSuccessfully(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $kategori = KategoriProduk::factory()->create(); 

        $response = $this->delete("/kategori/{$kategori->id}");

        $response->assertRedirect(route('kategori.index'));
        $response->assertSessionHas('success', 'Kategori berhasil dihapus.');

        $this->assertSoftDeleted('kategori_produk', [
            'id' => $kategori->id,
        ]);
    }

    
}
