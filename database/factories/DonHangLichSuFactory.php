<?php

namespace Database\Factories;

use App\Models\DonHangLichSu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonHangLichSu>
 */
class DonHangLichSuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_trang_thai' => fake()->randomElement(['Chưa thanh toán', 'Đã thanh toán', 'Đang giao', 'Đã giao', 'Đã hủy']),
            'user_id' => null,
            'ghichu' => null,
        ];
    }
}
