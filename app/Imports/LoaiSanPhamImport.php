<?php

namespace App\Imports;

use App\Models\LoaiSanPham;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LoaiSanPhamImport implements ToModel, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    public function model(array $row): ?LoaiSanPham
    {
        $name = trim((string) ($row['tenloai'] ?? $row['ten_loai'] ?? $row['tenloaimay'] ?? $row['ten_loai_may'] ?? $row['tenloaisanpham'] ?? $row['ten_loai_san_pham'] ?? $row['name'] ?? $row['category'] ?? $row['machine_type'] ?? ''));

        if ($name === '') {
            return null;
        }

        $slug = Str::slug($name);

        if (LoaiSanPham::where('tenloai_slug', $slug)->orWhere('tenloai', $name)->exists()) {
            $this->skipped++;

            return null;
        }

        $this->imported++;

        return new LoaiSanPham([
            'tenloai' => $name,
            'tenloai_slug' => $slug,
        ]);
    }
}
