<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $unpaidId = DB::table('tinhtrang')->where('tinhtrang', 'Chưa thanh toán')->value('id');
        if (! $unpaidId) {
            $unpaidId = DB::table('tinhtrang')->insertGetId([
                'tinhtrang' => 'Chưa thanh toán',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('donhang')->update(['tinhtrang_id' => $unpaidId]);
        DB::table('tinhtrang')->whereNotIn('tinhtrang', ['Chưa thanh toán', 'Đã thanh toán', 'Đã hủy'])->delete();

        foreach (['Đã thanh toán', 'Đã hủy'] as $status) {
            if (! DB::table('tinhtrang')->where('tinhtrang', $status)->exists()) {
                DB::table('tinhtrang')->insert([
                    'tinhtrang' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('donhang')->update([
            'tinhtrang_id' => DB::table('tinhtrang')->where('tinhtrang', 'Chưa thanh toán')->value('id')
                ?? DB::table('tinhtrang')->insertGetId([
                    'tinhtrang' => 'Chưa thanh toán',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
        ]);
        DB::table('tinhtrang')->whereIn('tinhtrang', ['Chưa thanh toán', 'Đã thanh toán', 'Đã hủy'])->delete();
    }
};
