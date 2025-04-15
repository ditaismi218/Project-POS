<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition()
    {
        return [
            'kode_member' => 'MBR' . now()->format('YmdHis') . strtoupper(Str::random(3)), // Kode member seperti yang di-boot
            'nama' => $this->faker->name,  // Nama member menggunakan Faker
            'no_telp' => substr(preg_replace('/\D/', '', $this->faker->phoneNumber), 0, 12),  // Menghapus non-digit dan memotong menjadi 12 digit
            'alamat' => $this->faker->address,  // Alamat member
            // Tidak perlu menyertakan tgl_bergabung karena itu otomatis di-set saat pembuatan
        ];
    }
}
