<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donhang', function (Blueprint $table): void {
            $table->text('lydo_huy')->nullable()->after('ghichu');
        });
    }

    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table): void {
            $table->dropColumn('lydo_huy');
        });
    }
};
