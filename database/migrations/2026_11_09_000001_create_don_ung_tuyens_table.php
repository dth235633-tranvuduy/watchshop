<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donungtuyen', function (Blueprint $table) {
            $table->id();
            $table->string('hoten');
            $table->string('email');
            $table->string('sodienthoai')->nullable();
            $table->string('vitri');
            $table->string('cv_path');
            $table->enum('trangthai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donungtuyen');
    }
};