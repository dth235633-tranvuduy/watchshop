<?php

namespace App\Imports;

use App\Models\NhaCungCap;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NhaCungCapImport implements ToModel, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    public function model(array $row): ?NhaCungCap
    {
        $name = trim((string) ($row['tennhacungcap'] ?? $row['ten nha cung cap'] ?? $row['ten_nha_cung_cap'] ?? $row['nha cung cap'] ?? $row['name'] ?? ''));

        if ($name === '') {
            return null;
        }

        if (NhaCungCap::where('tennhacungcap', $name)->exists()) {
            $this->skipped++;

            return null;
        }

        $this->imported++;

        return new NhaCungCap([
            'tennhacungcap' => $name,
            'email' => trim((string) ($row['email'] ?? '')) ?: null,
            'sodienthoai' => trim((string) ($row['sodienthoai'] ?? $row['so dien thoai'] ?? $row['so_dien_thoai'] ?? '')) ?: null,
            'diachi' => trim((string) ($row['diachi'] ?? $row['dia chi'] ?? $row['dia_chi'] ?? '')) ?: null,
            'trangthai' => true,
        ]);
    }
}
