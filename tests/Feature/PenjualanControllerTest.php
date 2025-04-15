<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Member;
use App\Models\PenerimaanBarang;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PenjualanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStorePenjualan()
    {
        // Membuat user dengan role admin
        $user = User::factory()->create([
            'email' => 'kasir@example.com',
            'password' => bcrypt('12345678'),
            'role' => 'kasir',
        ]);

        // Login sebagai kasir
        $this->actingAs($user);

        // Membuat member dan produk untuk pengujian
        $member = Member::factory()->create();
        KategoriProduk::factory()->create();
        $produk = Produk::inRandomOrder()->value('id') ?? Produk::factory()->create();
        PenerimaanBarang::factory()->create([
            'produk_id' => 1,
            'qty' => 10,
        ]);


        // Cek apakah member ada
        // dd($member);  // Pastikan member memiliki id yang valid

        // Menyiapkan data cart untuk pengujian
        $data = [
            'member_id' => $member->id,
            'status' => 'lunas',
            'cart' => [
                [
                    'produk_id' => $produk->id,
                    'qty' => 2,
                ]
            ]
        ];

        // dd($data);
        // Melakukan post request untuk menyimpan penjualan
        $response = $this->post(route('penjualan.store'), $data);

        // dd($response);

        // Ambil ID penjualan yang baru dibuat
        $penjualanId = Penjualan::latest()->first()->id;

        // Memastikan redirect setelah berhasil
        $response->assertRedirect(route('pembayaran.create', ['penjualan' => $penjualanId]));

        // Memastikan data penjualan ada di database
        $this->assertDatabaseHas('penjualan', [
            'user_id' => $user->id,
            'member_id' => $member->id,
            'status' => 'lunas',
        ]);
    }
}
