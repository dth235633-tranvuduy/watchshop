<?php

namespace App\Exports;

use App\Models\NhaCungCap;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NhaCungCapExport implements FromQuery, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return [
            'STT',
            'Tên nhà cung cấp',
            'Số điện thoại',
            'Email',
            'Địa chỉ',
            'Trạng thái',
            'Ngày tạo',
        ];
    }

    public function query(): Builder
    {
        return NhaCungCap::query()->select(
            'id',
            'tennhacungcap',
            'sodienthoai',
            'email',
            'diachi',
            'trangthai',
            'created_at'
        )->orderBy('tennhacungcap');
    }

    public function map($row): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $row->tennhacungcap,
            $row->sodienthoai ?? '-',
            $row->email ?? '-',
            $row->diachi ?? '-',
            $row->trangthai ? 'Hoạt động' : 'Ngừng hoạt động',
            $row->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
