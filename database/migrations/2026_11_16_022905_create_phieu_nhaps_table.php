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
        Schema::create('phieunhap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhacungcap_id')->constrained('nhacungcap');
            $table->foreignId('user_id')->constrained('users');
            $table->string('ma_phieu')->unique();
            $table->dateTime('ngaynhap');
            $table->decimal('tongtien', 15, 2)->default(0);
            $table->text('ghichu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieunhap');
    }
};
