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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('loai_giam', 20);
            $table->decimal('giatrigiam', 15, 2);
            $table->decimal('dieukien_toithieu', 15, 2)->default(0);
            $table->dateTime('ngay_batdau')->nullable();
            $table->dateTime('ngay_ketthuc')->nullable();
            $table->unsignedInteger('soluong')->nullable();
            $table->unsignedInteger('daludung')->default(0);
            $table->boolean('trangthai')->default(true);
            $table->timestamps();

            $table->index(['trangthai', 'ngay_batdau', 'ngay_ketthuc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
