<?php

namespace Database\Factories;

use App\Models\Penjualan;
use App\Models\User;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenjualanFactory extends Factory
{
    protected $model = Penjualan::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'member_id' => $this->faker->optional(0.7)->passthrough(Member::factory()),
            'no_faktur' => 'INV-' . now()->format('Ymd') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'tgl_faktur' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'total_bayar' => $this->faker->numberBetween(10000, 1000000),
            'status' => 'pending', // Default status diubah menjadi pending
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }

    public function lunas()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'lunas',
            ];
        });
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function denganMember()
    {
        return $this->state(function (array $attributes) {
            return [
                'member_id' => Member::factory(),
            ];
        });
    }

    public function tanpaMember()
    {
        return $this->state(function (array $attributes) {
            return [
                'member_id' => null,
            ];
        });
    }


}