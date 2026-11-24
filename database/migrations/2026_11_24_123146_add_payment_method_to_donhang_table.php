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
        Schema::table('donhang', function (Blueprint $table) {
            $table->string('phuongthucthanhtoan', 20)->default('cod')->after('ghichu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropColumn('phuongthucthanhtoan');
        });
    }
};
