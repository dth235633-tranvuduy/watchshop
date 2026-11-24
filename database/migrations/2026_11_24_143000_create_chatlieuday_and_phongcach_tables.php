<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatlieuday', function (Blueprint $table): void {
            $table->id();
            $table->string('tenchatlieu');
            $table->string('tenchatlieu_slug');
            $table->timestamps();
        });

        Schema::create('phongcach', function (Blueprint $table): void {
            $table->id();
            $table->string('tenphongcach');
            $table->string('tenphongcach_slug');
            $table->timestamps();
        });

        Schema::table('sanpham', function (Blueprint $table): void {
            $table->string('phongcach')->nullable()->after('chatlieuday');
        });
    }

    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table): void {
            $table->dropColumn('phongcach');
        });
        Schema::dropIfExists('phongcach');
        Schema::dropIfExists('chatlieuday');
    }
};
