<?php

namespace App\Exports;

use App\Models\LoaiSanPham;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LoaiSanPhamExport implements FromQuery, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return [
            'STT',
            'Tên loại máy',
            'Tên loại máy không dấu',
        ];
    }

    public function query(): Builder
    {
        return LoaiSanPham::query()->orderBy('tenloai');
    }

    public function map($row): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $row->tenloai,
            $row->tenloai_slug ?? '-',
        ];
    }
}
