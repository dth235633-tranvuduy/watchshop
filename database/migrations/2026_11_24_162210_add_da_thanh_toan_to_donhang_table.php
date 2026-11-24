<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tách trạng thái thanh toán khỏi trạng thái giao hàng:
     * - Thêm cột donhang.da_thanh_toan để biết đơn đã trả tiền hay chưa.
     * - Bộ trạng thái giao hàng mới: Chờ xác nhận, Đã xác nhận, Đang giao, Đã giao, Đã hủy.
     * - Đơn cũ "Đã thanh toán" được chuyển thành đã trả tiền + "Đã xác nhận",
     *   đơn cũ "Chưa thanh toán" được chuyển thành "Chờ xác nhận".
     */
    public function up(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->boolean('da_thanh_toan')->default(false)->after('tinhtrang_id');
        });

        $statusIds = [];
        foreach (['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'] as $status) {
            $statusIds[$status] = DB::table('tinhtrang')->where('tinhtrang', $status)->value('id')
                ?? DB::table('tinhtrang')->insertGetId(['tinhtrang' => $status]);
        }

        $paidStatusId = DB::table('tinhtrang')->where('tinhtrang', 'Đã thanh toán')->value('id');
        if ($paidStatusId) {
            DB::table('donhang')
                ->where('tinhtrang_id', $paidStatusId)
                ->update([
                    'da_thanh_toan' => true,
                    'tinhtrang_id' => $statusIds['Đã xác nhận'],
                ]);
        }

        $unpaidStatusId = DB::table('tinhtrang')->where('tinhtrang', 'Chưa thanh toán')->value('id');
        if ($unpaidStatusId) {
            DB::table('donhang')
                ->where('tinhtrang_id', $unpaidStatusId)
                ->update(['tinhtrang_id' => $statusIds['Chờ xác nhận']]);
        }

        // Bỏ hẳn hai trạng thái thanh toán cũ khỏi danh sách chọn của admin.
        // Lịch sử trạng thái vẫn đọc được nhờ cột ten_trang_thai lưu kèm tên.
        DB::table('tinhtrang')->whereIn('tinhtrang', ['Chưa thanh toán', 'Đã thanh toán'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $pendingId = DB::table('tinhtrang')->where('tinhtrang', 'Chờ xác nhận')->value('id');
        if ($pendingId) {
            DB::table('donhang')
                ->where('tinhtrang_id', $pendingId)
                ->where('da_thanh_toan', false)
                ->update(['tinhtrang_id' => DB::table('tinhtrang')->where('tinhtrang', 'Chưa thanh toán')->value('id')
                    ?? DB::table('tinhtrang')->insertGetId(['tinhtrang' => 'Chưa thanh toán'])]);
        }

        $confirmedId = DB::table('tinhtrang')->where('tinhtrang', 'Đã xác nhận')->value('id');
        if ($confirmedId) {
            DB::table('donhang')
                ->where('tinhtrang_id', $confirmedId)
                ->where('da_thanh_toan', true)
                ->update(['tinhtrang_id' => DB::table('tinhtrang')->where('tinhtrang', 'Đã thanh toán')->value('id')
                    ?? DB::table('tinhtrang')->insertGetId(['tinhtrang' => 'Đã thanh toán'])]);
        }

        Schema::table('donhang', function (Blueprint $table) {
            $table->dropColumn('da_thanh_toan');
        });
    }
};
