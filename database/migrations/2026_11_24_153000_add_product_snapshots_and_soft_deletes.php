<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanpham', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('donhang_chitiet', function (Blueprint $table): void {
            $table->string('tensanpham_snapshot')->nullable()->after('sanpham_id');
        });

        Schema::table('chitietphieunhap', function (Blueprint $table): void {
            $table->string('tensanpham_snapshot')->nullable()->after('sanpham_id');
        });

        DB::table('donhang_chitiet')
            ->whereNull('tensanpham_snapshot')
            ->update(['tensanpham_snapshot' => DB::raw('(select tensanpham from sanpham where sanpham.id = donhang_chitiet.sanpham_id)')]);

        DB::table('chitietphieunhap')
            ->whereNull('tensanpham_snapshot')
            ->update(['tensanpham_snapshot' => DB::raw('(select tensanpham from sanpham where sanpham.id = chitietphieunhap.sanpham_id)')]);
    }

    public function down(): void
    {
        Schema::table('chitietphieunhap', function (Blueprint $table): void {
            $table->dropColumn('tensanpham_snapshot');
        });

        Schema::table('donhang_chitiet', function (Blueprint $table): void {
            $table->dropColumn('tensanpham_snapshot');
        });

        Schema::table('sanpham', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
