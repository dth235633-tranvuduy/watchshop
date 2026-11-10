<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sanpham', function (Blueprint $table): void {
            $table->unsignedTinyInteger('giamgia')->default(0)->after('dongia');
            $table->decimal('danhgia', 2, 1)->default(0)->after('giamgia');
            $table->string('chatlieuday')->nullable()->after('motasanpham');
            $table->string('matkinh')->nullable()->after('chatlieuday');
            $table->string('dochieunuoc')->nullable()->after('matkinh');
            $table->index(['loaisanpham_id', 'hangsanxuat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table): void {
            $table->dropIndex('sanpham_loaisanpham_id_hangsanxuat_id_index');
            $table->dropColumn(['giamgia', 'danhgia', 'chatlieuday', 'matkinh', 'dochieunuoc']);
        });
    }
};
