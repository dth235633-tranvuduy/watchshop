<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Đánh giá không còn phải chờ duyệt: các đánh giá đang ở trạng thái
     * chờ duyệt được chuyển thẳng sang hiển thị trên trang sản phẩm.
     * Đánh giá đã bị admin ẩn (trangthai = 'an') giữ nguyên.
     */
    public function up(): void
    {
        DB::table('danhgia')
            ->where('trangthai', 'cho_duyet')
            ->update(['trangthai' => 'hien_thi']);
    }

    /**
     * Không khôi phục được trạng thái chờ duyệt cũ.
     */
    public function down(): void
    {
        //
    }
};
