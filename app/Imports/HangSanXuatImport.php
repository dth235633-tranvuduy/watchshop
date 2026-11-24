<?php

namespace App\Imports;

use App\Models\HangSanXuat;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HangSanXuatImport implements ToModel, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    public function model(array $row): ?HangSanXuat
    {
        $name = trim((string) ($row['tenhang'] ?? $row['ten_hang'] ?? $row['hangsanxuat'] ?? $row['hang_san_xuat'] ?? $row['name'] ?? $row['brand'] ?? ''));
        $image = trim((string) ($row['hinhanh'] ?? $row['hinh_anh'] ?? $row['image'] ?? ''));

        if ($name === '') {
            return null;
        }

        $slug = Str::slug($name);

        if (HangSanXuat::where('tenhang_slug', $slug)->orWhere('tenhang', $name)->exists()) {
            $this->skipped++;

            return null;
        }

        $this->imported++;

        return new HangSanXuat([
            'tenhang' => $name,
            'tenhang_slug' => $slug,
            'hinhanh' => $image !== '' ? $image : null,
        ]);
    }
}
