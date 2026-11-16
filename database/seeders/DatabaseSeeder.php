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

        foreach (['Chờ duyệt', 'Đang giao', 'Hoàn thành', 'Đã hủy'] as $status) {
            TinhTrang::firstOrCreate(['tinhtrang' => $status]);
        }

        $this->call(ImportFromDhdaSeeder::class);
    }
}
