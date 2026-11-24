<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột dòng chữ nhỏ phía trên tiêu đề banner, đồng thời đưa 3 slide
     * đang hiển thị cứng trên trang chủ thành bản ghi banner trong DB để
     * quản trị viên chỉnh sửa được ở trang quản lý banner. Chỉ tạo khi bảng
     * banner đang trống, tránh ghi đè dữ liệu admin đã nhập.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('tieu_de_phu')->nullable()->after('tieu_de');
        });

        if (DB::table('banners')->count() > 0) {
            return;
        }

        $now = now();
        $slides = [
            [
                'tieu_de_phu' => 'Bộ sưu tập 2025',
                'tieu_de' => 'Đồng Hồ Nam Sang Trọng & Đẳng Cấp',
                'mo_ta' => 'Giảm đến 30% cho dòng sản phẩm mới',
                'hinh_anh' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1600&h=600&fit=crop',
            ],
            [
                'tieu_de_phu' => 'Tinh tế & Quyến rũ',
                'tieu_de' => 'Đồng Hồ Nữ Duyên Dáng Mỗi Khoảnh Khắc',
                'mo_ta' => 'Sự tinh tế trong từng chi tiết',
                'hinh_anh' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=1600&h=600&fit=crop',
            ],
            [
                'tieu_de_phu' => 'Chinh phục mọi thách thức',
                'tieu_de' => 'Đồng Hồ Thể Thao Bền Bỉ & Mạnh Mẽ',
                'mo_ta' => 'Đồng hành cùng mọi hành trình',
                'hinh_anh' => 'https://images.unsplash.com/photo-1548135170-77b9e51226a9?w=1600&h=600&fit=crop',
            ],
        ];

        foreach ($slides as $index => $slide) {
            DB::table('banners')->insert([
                ...$slide,
                'duong_dan' => '/san-pham',
                'thu_tu' => $index + 1,
                'trang_thai' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Chỉ gỡ cột thêm vào; các banner đã tạo được giữ lại.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('tieu_de_phu');
        });
    }
};
