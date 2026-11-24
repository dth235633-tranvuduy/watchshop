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
            ->join('sanpham', 'sanpham.id', '=', 'donhang_chitiet.sanpham_id')
            ->whereNull('donhang_chitiet.tensanpham_snapshot')
            ->update(['donhang_chitiet.tensanpham_snapshot' => DB::raw('sanpham.tensanpham')]);

        DB::table('chitietphieunhap')
            ->join('sanpham', 'sanpham.id', '=', 'chitietphieunhap.sanpham_id')
            ->whereNull('chitietphieunhap.tensanpham_snapshot')
            ->update(['chitietphieunhap.tensanpham_snapshot' => DB::raw('sanpham.tensanpham')]);
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
