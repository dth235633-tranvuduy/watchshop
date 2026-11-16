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
        Schema::create('chitietphieunhap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieunhap_id')->constrained('phieunhap')->cascadeOnDelete();
            $table->foreignId('sanpham_id')->constrained('sanpham');
            $table->unsignedInteger('soluong');
            $table->decimal('dongianhap', 15, 2);
            $table->timestamps();

            $table->unique(['phieunhap_id', 'sanpham_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietphieunhap');
    }
};
