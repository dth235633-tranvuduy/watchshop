<?php

namespace App\Exports;

use App\Models\PhieuNhap;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PhieuNhapExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function headings(): array
    {
        return [
            'Mã phiếu',
            'Nhà cung cấp',
            'Ngày nhập',
            'Người nhập',
            'Tổng tiền',
            'Ghi chú',
        ];
    }

    public function query(): Builder
    {
        return PhieuNhap::query()->with('NhaCungCap', 'User')->latest('ngaynhap');
    }

    public function map($row): array
    {
        return [
            $row->ma_phieu,
            $row->NhaCungCap?->tennhacungcap ?? '-',
            $row->ngaynhap?->format('d/m/Y H:i:s'),
            $row->User?->name ?? '-',
            number_format((float) $row->tongtien, 0, ',', '.').' đ',
            $row->ghichu ?? '-',
        ];
    }
}
