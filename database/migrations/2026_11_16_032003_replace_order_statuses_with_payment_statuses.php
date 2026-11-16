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
        $paymentStatusNames = ['Chưa thanh toán', 'Chờ thanh toán', 'Đã thanh toán'];
        $pendingStatusId = DB::table('tinhtrang')->where('tinhtrang', 'Chờ thanh toán')->value('id');

        if (! $pendingStatusId) {
            $pendingStatusId = DB::table('tinhtrang')->insertGetId([
                'tinhtrang' => 'Chờ thanh toán',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('donhang')
            ->whereNot('tinhtrang_id', $pendingStatusId)
            ->update(['tinhtrang_id' => $pendingStatusId]);
        DB::table('tinhtrang')->whereNotIn('tinhtrang', $paymentStatusNames)->delete();

        foreach (['Chưa thanh toán', 'Đã thanh toán'] as $status) {
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
            'tinhtrang_id' => DB::table('tinhtrang')->where('tinhtrang', 'Chờ duyệt')->value('id')
                ?? DB::table('tinhtrang')->insertGetId([
                    'tinhtrang' => 'Chờ duyệt',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
        ]);
        DB::table('tinhtrang')->whereIn('tinhtrang', ['Chưa thanh toán', 'Chờ thanh toán', 'Đã thanh toán'])->delete();
    }
};
