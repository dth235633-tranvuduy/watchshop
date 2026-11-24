<?php

namespace App\Exports;

use App\Models\HangSanXuat;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HangSanXuatExport implements FromQuery, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return [
            'STT',
            'Tên hãng',
            'Tên hãng không dấu',
        ];
    }

    public function query(): Builder
    {
        return HangSanXuat::query()->orderBy('tenhang');
    }

    public function map($row): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $row->tenhang,
            $row->tenhang_slug ?? '-',
        ];
    }
}
