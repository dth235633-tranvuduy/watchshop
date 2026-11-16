<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TinhTrang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        foreach (['Chưa thanh toán', 'Đã thanh toán', 'Đã hủy'] as $status) {
            TinhTrang::firstOrCreate(['tinhtrang' => $status]);
        }

        $this->call(ImportFromDhdaSeeder::class);
    }
}
