<?php

namespace App\Exports;

use App\Models\SanPham;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SanPhamExport implements FromQuery, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return [
            'STT',
            'Tên sản phẩm',
            'Loại máy',
            'Thương hiệu',
            'Số lượng',
            'Đơn giá',
            'Giảm giá (%)',
            'Đánh giá',
            'Mô tả',
            'Chất liệu dây',
            'Mặt kính',
            'Độ chịu nước',
        ];
    }

    public function query(): EloquentBuilder
    {
        return SanPham::query()->with('LoaiSanPham', 'HangSanXuat')->orderBy('tensanpham');
    }

    public function map($row): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $row->tensanpham,
            $row->LoaiSanPham?->tenloai ?? '-',
            $row->HangSanXuat?->tenhang ?? '-',
            $row->soluong,
            number_format((float) $row->dongia, 0, ',', '.').' đ',
            $row->giamgia,
            $row->danhgia,
            $row->motasanpham ?? '-',
            $row->chatlieuday ?? '-',
            $row->matkinh ?? '-',
            $row->dochieunuoc ?? '-',
        ];
    }
}
