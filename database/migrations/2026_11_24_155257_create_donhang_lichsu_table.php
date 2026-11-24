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
        Schema::create('donhang_lichsu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donhang_id')->constrained('donhang')->cascadeOnDelete();
            $table->foreignId('tinhtrang_id')->nullable()->constrained('tinhtrang')->nullOnDelete();
            $table->string('ten_trang_thai');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ghichu')->nullable();
            $table->timestamps();

            $table->index(['donhang_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhang_lichsu');
    }
};
